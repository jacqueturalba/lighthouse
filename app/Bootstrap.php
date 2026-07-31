<?php
declare(strict_types=1);

function env(string $key, ?string $default = null): ?string { static $values = null; if ($values === null) { $values = []; $file = dirname(__DIR__).'/.env'; if (is_file($file)) foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) { if ($line[0] === '#' || !str_contains($line, '=')) continue; [$k,$v] = explode('=', $line, 2); $values[trim($k)] = trim($v, " \t\"'"); } } return $_ENV[$key] ?? getenv($key) ?: ($values[$key] ?? $default); }
function config(string $key, ?string $default = null): ?string { return env($key, $default); }
function db(): PDO { static $pdo; if (!$pdo) { $dsn='mysql:host='.config('DB_HOST','127.0.0.1').';port='.config('DB_PORT','3306').';dbname='.config('DB_DATABASE').';charset=utf8mb4'; $pdo=new PDO($dsn, config('DB_USERNAME'), config('DB_PASSWORD'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]); } return $pdo; }
function log_event(string $event, array $context=[]): void { unset($context['password'],$context['token'],$context['session']); $row=['time'=>gmdate('c'),'event'=>$event,'context'=>$context]; file_put_contents(dirname(__DIR__).'/storage/logs/app.log',json_encode($row,JSON_UNESCAPED_SLASHES).PHP_EOL,FILE_APPEND|LOCK_EX); }
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
function flash(string $key, ?string $message=null): ?string { if ($message !== null) { $_SESSION['_flash'][$key]=$message; return null; } $value=$_SESSION['_flash'][$key]??null; unset($_SESSION['_flash'][$key]); return $value; }
function redirect(string $path): never { header('Location: '.$path, true, 302); exit; }

date_default_timezone_set(config('APP_TIMEZONE','Asia/Manila'));
if (config('APP_ENV', 'local') === 'production') {
  ini_set('display_errors', '0');
  ini_set('display_startup_errors', '0');
}
if (PHP_SAPI !== 'cli') {
  $secure = config('SESSION_SECURE_COOKIE', config('APP_ENV') === 'production' ? 'true' : 'false') === 'true';
  $sessionPath=config('SESSION_PATH', 'storage/sessions'); if (!str_starts_with($sessionPath, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:[\\\\\/]/',$sessionPath)) $sessionPath=dirname(__DIR__).DIRECTORY_SEPARATOR.$sessionPath;
  if (!is_dir($sessionPath)) mkdir($sessionPath,0700,true); session_save_path($sessionPath);
  session_name(config('SESSION_NAME','lighthouse_session'));
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
  session_start();
  if (!isset($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
  if (isset($_SESSION['last_activity']) && time()-$_SESSION['last_activity'] > (int)config('SESSION_LIFETIME','120')*60) { $_SESSION=[]; session_destroy(); session_start(); $_SESSION['csrf']=bin2hex(random_bytes(32)); }
  $_SESSION['last_activity']=time();
}
