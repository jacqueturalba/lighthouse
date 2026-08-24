<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/View.php';
require_once dirname(__DIR__).'/Models/PressRelease.php';

final class PressReleaseController
{

    public function store(array $params = []): void
    {
        $user = require_super_admin();
        csrf();

        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $eventDate = trim((string) ($_POST['event_date'] ?? ''));
        $links = $_POST['links'] ?? [];

        $file = $_FILES['cover_photo'] ?? null;
        $path = '';
        $errors = [];

        if ($title === '' || mb_strlen($title) > 150) {
            $errors[] = 'Enter a title up to 150 characters.';
        }

        if ($eventDate === '') {
            $errors[] = 'Event date is required.';
        }

        /*
        * Cover photo is OPTIONAL.
        * Only validate it if a file was actually uploaded.
        */
        if (
            $file &&
            isset($file['error']) &&
            $file['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'There was a problem uploading the cover photo.';
            } else {
                $allowed = [
                    'jpg'  => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png'  => 'image/png',
                ];

                $extension = strtolower(
                    pathinfo((string) $file['name'], PATHINFO_EXTENSION)
                );

                $mime = is_uploaded_file($file['tmp_name'])
                    ? (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name'])
                    : '';

                if (
                    !isset($allowed[$extension]) ||
                    $mime !== $allowed[$extension]
                ) {
                    $errors[] = 'Only valid JPG, JPEG, or PNG files are allowed.';
                }

                if ((int) $file['size'] > 3 * 1024 * 1024) {
                    $errors[] = 'Files must be 3 MB or smaller.';
                }
            }
        }

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('/press-release-upload');
        }

        /*
        * Store cover photo only when one was uploaded.
        */
        if (
            $file &&
            isset($file['error']) &&
            $file['error'] === UPLOAD_ERR_OK
        ) {
            $root = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage';

            $folder = 'press-releases/images';

            $directory = rtrim($root, '/\\')
                . DIRECTORY_SEPARATOR
                . $folder;

            if (!is_dir($directory)) {
                mkdir($directory, 0700, true);
            }

            $extension = strtolower(
                pathinfo((string) $file['name'], PATHINFO_EXTENSION)
            );

            $stored = bin2hex(random_bytes(20)) . '.' . $extension;

            $path = $folder . '/' . $stored;

            if (
                !move_uploaded_file(
                    $file['tmp_name'],
                    $directory . DIRECTORY_SEPARATOR . $stored
                )
            ) {
                flash('error', 'The file could not be stored.');
                redirect('/press-release-upload');
            }
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'event_date' => $eventDate,
            'cover_photo' => $path,
            'links' => $links,
        ];

        try {
            PressRelease::recordPR($data, $user);
        } catch (Throwable $e) {

            if (!empty($path) && isset($directory, $stored)) {
                @unlink(
                    $directory . DIRECTORY_SEPARATOR . $stored
                );
            }

            flash(
                'error',
                'The press release could not be recorded.'
            );

            redirect('/press-release-upload');
        }

        flash('success', 'Press release uploaded.');
        redirect('/press-releases');
    }

    public function update(array $params = []): void
    {
        $user = require_super_admin();
        csrf();

        $id = (int) ($params['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'Invalid press release.');
            redirect('/press-releases');
        }

        $existing = PressRelease::getPressReleaseById($id);

        if (!$existing) {
            flash('error', 'Press release not found.');
            redirect('/press-releases');
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $eventDate = trim((string) ($_POST['event_date'] ?? ''));
        $links = $_POST['links'] ?? [];

        $errors = [];

        if ($title === '' || mb_strlen($title) > 150) {
            $errors[] = 'Enter a title up to 150 characters.';
        }

        if ($eventDate === '') {
            $errors[] = 'Event date is required.';
        }

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('/press-release-edit?id=' . $id);
        }

        $coverPhoto = $existing['cover_photo'] ?? '';
        $newFile = $_FILES['cover_photo'] ?? null;

        /*
        * Only replace the cover photo if a new one was uploaded.
        */
        if (
            $newFile &&
            ($newFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
        ) {

            $allowed = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
            ];

            $extension = strtolower(
                pathinfo(
                    (string) $newFile['name'],
                    PATHINFO_EXTENSION
                )
            );

            $mime = is_uploaded_file($newFile['tmp_name'])
                ? (new finfo(FILEINFO_MIME_TYPE))
                    ->file($newFile['tmp_name'])
                : '';

            if (
                !isset($allowed[$extension]) ||
                $mime !== $allowed[$extension]
            ) {
                flash(
                    'error',
                    'Only valid JPG, JPEG, or PNG files are allowed.'
                );

                redirect('/press-release-edit?id=' . $id);
            }

            if ((int) $newFile['size'] > 3 * 1024 * 1024) {
                flash(
                    'error',
                    'Files must be 3 MB or smaller.'
                );

                redirect('/press-release-edit?id=' . $id);
            }

            $root = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage';

            $folder = 'press-releases/images';

            $directory = rtrim($root, '/\\')
                . DIRECTORY_SEPARATOR
                . $folder;

            if (!is_dir($directory)) {
                mkdir($directory, 0700, true);
            }

            $stored = bin2hex(random_bytes(20))
                . '.'
                . $extension;

            if (
                !move_uploaded_file(
                    $newFile['tmp_name'],
                    $directory . DIRECTORY_SEPARATOR . $stored
                )
            ) {
                flash(
                    'error',
                    'The new cover photo could not be stored.'
                );

                redirect('/press-release-edit?id=' . $id);
            }

            /*
            * Delete old cover photo after the new one
            * has successfully been uploaded.
            */
            if (!empty($coverPhoto)) {

                $oldPath = rtrim($root, '/\\')
                    . DIRECTORY_SEPARATOR
                    . str_replace(
                        ['/', '\\'],
                        DIRECTORY_SEPARATOR,
                        $coverPhoto
                    );

                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $coverPhoto = $folder . '/' . $stored;
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'event_date' => $eventDate,
            'cover_photo' => $coverPhoto,
            'links' => $links,
        ];

        try {

            PressRelease::updatePR(
                $id,
                $data,
                $user
            );

        } catch (Throwable $e) {

            flash(
                'error',
                'The press release could not be updated.'
            );

            redirect('/press-release-edit?id=' . $id);
        }

        flash(
            'success',
            'Press release updated successfully.'
        );

        redirect('/press-releases?p=' . $id);
    }

    public function delete(array $params = []): void
    {
        $user = require_super_admin();
        csrf();

        $id = (int) ($params['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'Invalid press release.');
            redirect('/press-releases');
        }

        try {

            $deleted = PressRelease::deletePR(
                $id,
                $user
            );

            if (!$deleted) {
                flash(
                    'error',
                    'Press release not found.'
                );

                redirect('/press-releases');
            }

        } catch (Throwable $e) {

            flash(
                'error',
                'The press release could not be deleted.'
            );

            redirect('/press-releases');
        }

        flash(
            'success',
            'Press release deleted successfully.'
        );

        redirect('/press-releases');
    }

    private function validated(array $input): array
    {
    }
}