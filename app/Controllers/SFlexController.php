<?php declare(strict_types=1);
require_once dirname(__DIR__) . "/View.php";
require_once dirname(__DIR__) . "/Models/SFlexPost.php";
require_once dirname(__DIR__) . "/Models/UploadJob.php";
final class SFlexController
{
    public function create(): void
    {
        $u = require_auth();
        csrf();
        $async = (($_POST['ajax'] ?? '') === '1');
        $jobId = (int) ($_POST['upload_job_id'] ?? 0);
        $caption = trim((string) ($_POST["caption"] ?? ""));
        $f = $_FILES["media"] ?? null;
        $path = null;
        $type = null;
        if (
            $caption === "" &&
            (!$f || ($f["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
        ) {
            $this->uploadError($async, $jobId, (int) $u['id'], "Add a caption or media.");
            return;
        }
        if ($f && ($f["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (
                $f["error"] !== UPLOAD_ERR_OK ||
                (int) $f["size"] > 4 * 1024 * 1024 * 1024
            ) {
                $this->uploadError($async, $jobId, (int) $u['id'], "The media upload failed or is larger than 4 GB.");
                return;
            }
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f["tmp_name"]);
            $type = str_starts_with($mime, "image/")
                ? "image"
                : (str_starts_with($mime, "video/")
                    ? "video"
                    : null);
            if (!$type) {
                $this->uploadError($async, $jobId, (int) $u['id'], "Upload a valid image or video.");
                return;
            }
            $ext = $type === "image" ? "jpg" : "mp4";
            $folder = "sflex/" . $type . "/" . date("Y-m-d");
            $dir = dirname(__DIR__, 2) . "/storage/" . $folder;
            if (!is_dir($dir)) {
                mkdir($dir, 0700, true);
            }
            $name = bin2hex(random_bytes(16)) . "." . $ext;
            if (!move_uploaded_file($f["tmp_name"], $dir . "/" . $name)) {
                $this->uploadError($async, $jobId, (int) $u['id'], "Media could not be stored.");
                return;
            }
            $path = $folder . "/" . $name;
        }
        SFlexPost::create(
            (int) $u["id"],
            $caption,
            $path,
            $type,
            $u["role"] === "super_admin" ? "approved" : "pending"
        );
        $postId = (int) db()->lastInsertId();
        if ($jobId > 0) {
            UploadJob::complete($jobId, (int) $u['id'], 'sflex', $postId);
        }
        if ($async) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                'ok' => true,
                'message' => $u['role'] === 'super_admin' ? 'Post published.' : 'Post sent for approval.',
                'link' => '/sflex/post/'.$postId,
            ]);
            return;
        }
        flash(
            "success",
            $u["role"] === "super_admin"
                ? "Post published."
                : "Post sent for approval."
        );
        redirect("/sflex");
    }

    private function uploadError(bool $async, int $jobId, int $userId, string $message): void
    {
        if ($jobId > 0) {
            UploadJob::fail($jobId, $userId, $message);
        }
        if ($async) {
            http_response_code(422);
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(['error' => $message]);
            return;
        }
        flash("error", $message);
        redirect("/sflex/create");
    }
    public function media(array $p): void
    {
        require_auth();
        foreach ($p as $v) {
            if (basename($v) !== $v) {
                http_response_code(404);
                return;
            }
        }
        $f = realpath(
            dirname(__DIR__, 2) .
                "/storage/sflex/" .
                $p["type"] .
                "/" .
                $p["date"] .
                "/" .
                $p["file"]
        );
        if (!$f || !is_file($f)) {
            http_response_code(404);
            return;
        }
        header("Content-Type: " . (new finfo(FILEINFO_MIME_TYPE))->file($f));
        readfile($f);
        exit();
    }
    public function react(array $p): void
    {
        $u = require_auth();
        csrf();
        $r = (string) ($_POST["reaction"] ?? "");
        if (!in_array($r, ["like", "heart", "smile", "laugh", "cry"], true)) {
            http_response_code(422);
            exit();
        }
        SFlexPost::react((int) $p["id"], (int) $u["id"], $r);
        header("Content-Type: application/json");
        echo json_encode(["ok" => true]);
    }
    public function comment(array $p): void
    {
        $u = require_auth();
        csrf();
        $body = trim((string) ($_POST["body"] ?? ""));
        if ($body === "" || mb_strlen($body) > 1000) {
            flash("error", "Comments must be between 1 and 1000 characters.");
        } else {
            SFlexPost::comment((int) $p["id"], (int) $u["id"], $body);
            flash("success", "Comment added.");
        }
        redirect("/sflex");
    }
    public function review(array $p): void
    {
        require_super_admin();
        csrf();
        SFlexPost::review(
            (int) $p["id"],
            ($_POST["status"] ?? "") === "approved" ? "approved" : "rejected"
        );
        redirect("/sflex/review");
    }
    public function comments(array $p): void
    {
        $u = require_auth();
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(
            [
                "comments" => SFlexPost::comments(
                    (int) $p["id"],
                    $u["role"] === "super_admin"
                ),
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function moderateComment(array $p): void
    {
        require_super_admin();
        csrf();
        $action = (string) ($_POST["action"] ?? "");
        if (in_array($action, ["hide", "unhide", "remove"], true)) {
            SFlexPost::moderateComment((int) $p["id"], $action);
        }
        header("Content-Type: application/json");
        echo '{"ok":true}';
    }
}
