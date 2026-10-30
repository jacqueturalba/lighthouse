<?php
declare(strict_types=1);

require_once __DIR__.'/../Models/User.php';

final class AuthenticationService
{
    public function currentUser(): ?array
    {
        return empty($_SESSION['user_id']) ? null : User::findActiveById((int) $_SESSION['user_id']);
    }

    public function attempt(string $identifier, string $password): bool
    {
        $user = User::findBySerialNo($identifier);
        if ($user) {
            if ($user['status'] !== 'active' || !password_verify($password, $user['password'])) return false;
        } else {
            $matches = array_values(array_filter(
                User::findAllByEmail(strtolower($identifier)),
                static fn(array $candidate): bool => $candidate['status'] === 'active' && password_verify($password, $candidate['password'])
            ));
            if (count($matches) !== 1) return false;
            $user = $matches[0];
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        User::recordLogin((int) $user['id']);
        log_event('login_success', ['user_id' => $user['id']]);
        return true;
    }

    public function logout(): void
    {
        $id = $_SESSION['user_id'] ?? null;
        $_SESSION = [];
        if (ini_get('session.use_cookies')) { $p = session_get_cookie_params(); setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']); }
        session_destroy();
        log_event('logout', ['user_id' => $id]);
    }
}
