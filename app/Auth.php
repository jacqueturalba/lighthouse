<?php
declare(strict_types=1);
require_once __DIR__.'/Bootstrap.php';
require_once __DIR__.'/Services/AuthenticationService.php';

function auth_service(): AuthenticationService { static $service; return $service ??= new AuthenticationService(); }

function current_user(): ?array { 
    return auth_service()->currentUser();
}
function require_auth(): array { 
    $user=current_user(); 
    if (!$user) { 
        flash('error','Please sign in to continue.'); 
        redirect('/login'); 
    } 
    return $user; 
}
function require_super_admin(): array { 
    $user=require_auth(); 
    if ($user['role'] !== 'super_admin') { 
        http_response_code(403); 
        render('Access denied','<p>You do not have permission to access this page.</p>'); 
        exit;
    } 
    return $user; 
}
function csrf(): void { 
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_token'] ?? '')) { 
        http_response_code(419); 
        render('Session expired','<p>Please return to the form and try again. <a href="/login">Try again</a>.</p>'); 
        exit; 
    } 
}
function valid_password(string $password): ?string { 
    $min=(int)config('PASSWORD_MIN_LENGTH','12'); 
    if (strlen($password)<$min || !preg_match('/[A-Z]/',$password) || !preg_match('/[a-z]/',$password) || !preg_match('/\d/',$password) || !preg_match('/[^A-Za-z0-9]/',$password)) return "Password must be at least {$min} characters and include upper-case, lower-case, number, and symbol."; 
    return null; 
}
function attempt_login(string $email,string $password): bool { 
    return auth_service()->attempt($email, $password);
}
function logout(): void { 
    auth_service()->logout();
}
