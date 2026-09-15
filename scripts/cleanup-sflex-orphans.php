<?php

declare(strict_types=1);

/**
 * LIGHTHOUSE
 * SFlex Orphan Media Cleanup
 *
 * Removes SFlex image/video files that are no longer referenced
 * by the database.
 *
 * Intended to run once daily at 3:00 AM Philippine Time.
 *
 * Supports:
 * - Current multi-media: sflex_post_media.media_path
 * - Legacy single-media: sflex_posts.media_path
 * - image/
 * - video/
 * - date-based folders
 * - dry-run mode
 *
 * Usage:
 *
 *   php scripts/cleanup-sflex-orphans.php --dry-run
 *   php scripts/cleanup-sflex-orphans.php
 */

const CLEANUP_AGE_HOURS = 24;

// ------------------------------------------------------------
// Project paths
// ------------------------------------------------------------

$projectRoot = dirname(__DIR__);

/*
 * Use the same storage location used by SFlexController:
 *
 * dirname(__DIR__, 2) . '/storage/sflex/'
 *
 * For this script:
 *
 * dirname(__DIR__) = lighthouse/
 */
$sflexRoot = realpath(
    $projectRoot . '/storage/sflex'
);

if ($sflexRoot === false || !is_dir($sflexRoot)) {
    fwrite(
        STDERR,
        "ERROR: SFlex storage directory not found:\n" .
        $projectRoot . '/storage/sflex' .
        PHP_EOL
    );

    exit(1);
}

// ------------------------------------------------------------
// Load LIGHTHOUSE Bootstrap
// ------------------------------------------------------------

require_once $projectRoot . '/app/Bootstrap.php';

// Bootstrap sets APP_TIMEZONE.
// Fall back to Asia/Manila if needed.
$timezone = new DateTimeZone(
    config('APP_TIMEZONE', 'Asia/Manila')
);

$now = new DateTimeImmutable(
    'now',
    $timezone
);

$cutoffTimestamp =
    $now->getTimestamp() -
    (CLEANUP_AGE_HOURS * 60 * 60);

$dryRun = in_array(
    '--dry-run',
    $argv ?? [],
    true
);

// ------------------------------------------------------------
// Logging
// ------------------------------------------------------------

$logDirectory =
    $projectRoot .
    '/storage/logs';

if (!is_dir($logDirectory)) {
    @mkdir(
        $logDirectory,
        0700,
        true
    );
}

$logFile =
    $logDirectory .
    '/sflex-cleanup.log';

function cleanupLog(string $message): void
{
    global $logFile, $now;

    $line =
        '[' .
        $now->format('Y-m-d H:i:s') .
        ' ' .
        $now->format('T') .
        '] ' .
        $message .
        PHP_EOL;

    echo $line;

    @file_put_contents(
        $logFile,
        $line,
        FILE_APPEND | LOCK_EX
    );
}

// ------------------------------------------------------------
// Normalize database media paths
// ------------------------------------------------------------

function normalizeMediaPath(string $path): ?string
{
    $path = trim($path);

    if ($path === '') {
        return null;
    }

    $path = str_replace(
        '\\',
        '/',
        $path
    );

    $path = ltrim(
        $path,
        '/'
    );

    /*
     * Normalize possible database formats:
     *
     * storage/sflex/image/2026-09-09/file.jpg
     * sflex/image/2026-09-09/file.jpg
     * image/2026-09-09/file.jpg
     *
     * Into:
     *
     * image/2026-09-09/file.jpg
     */

    if (str_starts_with($path, 'storage/sflex/')) {

        $path = substr(
            $path,
            strlen('storage/sflex/')
        );

    } elseif (str_starts_with($path, 'sflex/')) {

        $path = substr(
            $path,
            strlen('sflex/')
        );
    }

    $path = ltrim(
        $path,
        '/'
    );

    if ($path === '') {
        return null;
    }

    /*
     * SFlex only stores media under:
     *
     * image/
     * video/
     */

    if (
        !str_starts_with($path, 'image/') &&
        !str_starts_with($path, 'video/')
    ) {
        return null;
    }

    /*
     * Prevent path traversal.
     */

    if (
        str_contains($path, '../') ||
        str_contains($path, "\0")
    ) {
        return null;
    }

    return $path;
}

// ------------------------------------------------------------
// Start cleanup
// ------------------------------------------------------------

cleanupLog(
    '============================================================'
);

cleanupLog(
    'SFlex orphan cleanup started' .
    ($dryRun ? ' [DRY RUN]' : '')
);

cleanupLog(
    'SFlex storage: ' .
    $sflexRoot
);

cleanupLog(
    'Cutoff: ' .
    (new DateTimeImmutable('@' . $cutoffTimestamp))
        ->setTimezone($timezone)
        ->format('Y-m-d H:i:s') .
    ' ' .
    $now->format('T')
);

// ------------------------------------------------------------
// Collect database-referenced media
// ------------------------------------------------------------

$referenced = [];

// ------------------------------------------------------------
// Current multi-media table
// ------------------------------------------------------------

try {

    $query = db()->query(
        'SELECT media_path
         FROM sflex_post_media
         WHERE media_path IS NOT NULL
         AND media_path <> \'\''
    );

    while (
        $row = $query->fetch(PDO::FETCH_ASSOC)
    ) {

        $path = normalizeMediaPath(
            (string) $row['media_path']
        );

        if ($path !== null) {
            $referenced[$path] = true;
        }
    }

} catch (Throwable $e) {

    cleanupLog(
        'WARNING: Could not read sflex_post_media: ' .
        $e->getMessage()
    );
}

// ------------------------------------------------------------
// Legacy single-media column
// ------------------------------------------------------------

try {

    $query = db()->query(
        'SELECT media_path
         FROM sflex_posts
         WHERE media_path IS NOT NULL
         AND media_path <> \'\''
    );

    while (
        $row = $query->fetch(PDO::FETCH_ASSOC)
    ) {

        $path = normalizeMediaPath(
            (string) $row['media_path']
        );

        if ($path !== null) {
            $referenced[$path] = true;
        }
    }

} catch (Throwable $e) {

    cleanupLog(
        'WARNING: Could not read sflex_posts.media_path: ' .
        $e->getMessage()
    );
}

cleanupLog(
    'Referenced media files: ' .
    count($referenced)
);

// ------------------------------------------------------------
// Scan image + video
// ------------------------------------------------------------

$scanned = 0;
$orphans = 0;
$deleted = 0;
$recentSkipped = 0;
$unsafeSkipped = 0;
$deleteErrors = 0;

$mediaDirectories = [
    $sflexRoot . '/image',
    $sflexRoot . '/video',
];

foreach ($mediaDirectories as $mediaRoot) {

    if (!is_dir($mediaRoot)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $mediaRoot,
            FilesystemIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $fileInfo) {

        if (!$fileInfo->isFile()) {
            continue;
        }

        $scanned++;

        $realFile = realpath(
            $fileInfo->getPathname()
        );

        if (
            $realFile === false ||
            !is_file($realFile)
        ) {
            continue;
        }

        // ----------------------------------------------------
        // Safety check
        // ----------------------------------------------------

        if (
            !str_starts_with(
                $realFile,
                $sflexRoot . DIRECTORY_SEPARATOR
            )
        ) {

            $unsafeSkipped++;

            cleanupLog(
                'SKIPPED unsafe file: ' .
                $realFile
            );

            continue;
        }

        // ----------------------------------------------------
        // Convert physical path to DB-relative path
        // ----------------------------------------------------

        $relativePath = ltrim(
            str_replace(
                '\\',
                '/',
                substr(
                    $realFile,
                    strlen($sflexRoot)
                )
            ),
            '/'
        );

        // ----------------------------------------------------
        // Referenced by database
        // ----------------------------------------------------

        if (
            isset(
                $referenced[$relativePath]
            )
        ) {
            continue;
        }

        $orphans++;

        // ----------------------------------------------------
        // Protect recent files
        // ----------------------------------------------------

        if (
            $fileInfo->getMTime() >
            $cutoffTimestamp
        ) {

            $recentSkipped++;

            cleanupLog(
                'SKIPPED recent orphan: ' .
                $relativePath
            );

            continue;
        }

        // ----------------------------------------------------
        // Dry run
        // ----------------------------------------------------

        if ($dryRun) {

            cleanupLog(
                'WOULD DELETE: ' .
                $relativePath
            );

            continue;
        }

        // ----------------------------------------------------
        // Delete
        // ----------------------------------------------------

        if (
            @unlink($realFile)
        ) {

            $deleted++;

            cleanupLog(
                'DELETED: ' .
                $relativePath
            );

        } else {

            $deleteErrors++;

            cleanupLog(
                'ERROR deleting: ' .
                $relativePath
            );
        }
    }
}

// ------------------------------------------------------------
// Remove empty date folders
// ------------------------------------------------------------

$emptyFoldersRemoved = 0;

foreach ($mediaDirectories as $mediaRoot) {

    if (!is_dir($mediaRoot)) {
        continue;
    }

    $folders = [];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $mediaRoot,
            FilesystemIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {

        if ($item->isDir()) {
            $folders[] = $item->getPathname();
        }
    }

    foreach ($folders as $folder) {

        $entries = @scandir($folder);

        if ($entries === false) {
            continue;
        }

        $remaining = array_diff(
            $entries,
            ['.', '..']
        );

        if (count($remaining) !== 0) {
            continue;
        }

        if ($dryRun) {

            cleanupLog(
                'WOULD REMOVE EMPTY FOLDER: ' .
                str_replace(
                    '\\',
                    '/',
                    $folder
                )
            );

        } elseif (@rmdir($folder)) {

            $emptyFoldersRemoved++;

            cleanupLog(
                'REMOVED EMPTY FOLDER: ' .
                str_replace(
                    '\\',
                    '/',
                    $folder
                )
            );
        }
    }
}

// ------------------------------------------------------------
// Summary
// ------------------------------------------------------------

cleanupLog(
    '------------------------------------------------------------'
);

cleanupLog(
    'Files scanned: ' .
    $scanned
);

cleanupLog(
    'Orphans found: ' .
    $orphans
);

cleanupLog(
    'Recent orphans skipped: ' .
    $recentSkipped
);

cleanupLog(
    'Unsafe paths skipped: ' .
    $unsafeSkipped
);

cleanupLog(
    'Files deleted: ' .
    $deleted
);

cleanupLog(
    'Delete errors: ' .
    $deleteErrors
);

cleanupLog(
    'Empty folders removed: ' .
    $emptyFoldersRemoved
);

cleanupLog(
    'SFlex orphan cleanup finished' .
    ($dryRun
        ? ' [DRY RUN - NO FILES DELETED]'
        : '')
);

cleanupLog(
    '============================================================'
);

exit(
    $deleteErrors > 0
        ? 1
        : 0
);