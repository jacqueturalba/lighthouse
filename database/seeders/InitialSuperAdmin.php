<?php
require_once dirname(__DIR__,2).'/app/Bootstrap.php';
$email=config('INITIAL_SUPER_ADMIN_EMAIL','admin@example.com'); $password=config('INITIAL_SUPER_ADMIN_PASSWORD');
if (!$password) throw new RuntimeException('Set INITIAL_SUPER_ADMIN_PASSWORD in .env before seeding.');
$s=db()->prepare('SELECT id FROM users WHERE serial_no=?'); $s->execute(['INITIAL-SUPER-ADMIN']);
if (!$s->fetch()) { db()->prepare("INSERT INTO users (serial_no,name,email,password,role,status,must_change_password) VALUES (?,?,?,?,?,?,1)")->execute(['INITIAL-SUPER-ADMIN','Initial Super Admin',$email,password_hash($password,PASSWORD_ARGON2ID),'super_admin','active']); log_event('initial_super_admin_created',['email'=>$email]); echo "Initial Super Admin created.\n"; } else echo "Initial Super Admin already exists.\n";
