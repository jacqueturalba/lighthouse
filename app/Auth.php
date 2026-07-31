<?php
declare(strict_types=1);
require_once __DIR__.'/Bootstrap.php';

function current_user(): ?array { if (empty($_SESSION['user_id'])) return null; $s=db()->prepare('SELECT id,name,email,role,status,last_login_at,must_change_password FROM users WHERE id=?'); $s->execute([$_SESSION['user_id']]); $user=$s->fetch(); return $user && $user['status']==='active' ? $user : null; }
function require_auth(): array { $user=current_user(); if (!$user) { flash('error','Please sign in to continue.'); redirect('/login'); } return $user; }
function require_super_admin(): array { $user=require_auth(); if ($user['role'] !== 'super_admin') { http_response_code(403); render('Access denied','<p>You do not have permission to access this page.</p>'); exit; } return $user; }
function csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_token'] ?? '')) { http_response_code(419); render('Session expired','<p>Please return to the form and try again.</p>'); exit; } }
function valid_password(string $password): ?string { $min=(int)config('PASSWORD_MIN_LENGTH','12'); if (strlen($password)<$min || !preg_match('/[A-Z]/',$password) || !preg_match('/[a-z]/',$password) || !preg_match('/\d/',$password) || !preg_match('/[^A-Za-z0-9]/',$password)) return "Password must be at least {$min} characters and include upper-case, lower-case, number, and symbol."; return null; }
function attempt_login(string $email,string $password): bool { $s=db()->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); $s->execute([$email]); $user=$s->fetch(); if (!$user || $user['status']!=='active' || !password_verify($password,$user['password'])) return false; session_regenerate_id(true); $_SESSION['user_id']=$user['id']; db()->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?')->execute([$user['id']]); log_event('login_success',['user_id'=>$user['id']]); return true; }
function logout(): void { $id=$_SESSION['user_id']??null; $_SESSION=[]; if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); } session_destroy(); log_event('logout',['user_id'=>$id]); }
