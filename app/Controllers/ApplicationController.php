<?php
declare(strict_types=1);
require dirname(__DIR__, 2).'/app/View.php'; 
require dirname(__DIR__, 2).'/app/Mailer.php';
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/'; $method=$_SERVER['REQUEST_METHOD'];

function form_token(): string { 
    return '<input type="hidden" name="_token" value="'.e($_SESSION['csrf']).'">'; 
}

function rate_limited(string $email): bool { 
    $s=db()->prepare('SELECT COUNT(*) FROM login_attempts WHERE email=? AND ip=? AND attempted_at>DATE_SUB(NOW(), INTERVAL '.(int)config('LOGIN_DECAY_MINUTES','15').' MINUTE)'); 
    $s->execute([$email,$_SERVER['REMOTE_ADDR']??'']); 
    return (int)$s->fetchColumn() >= (int)config('LOGIN_MAX_ATTEMPTS','5'); 
}

function reset_link(string $email): void { 
    $token=bin2hex(random_bytes(32)); 
    db()->beginTransaction(); 
    try { 
        db()->prepare('DELETE FROM password_reset_tokens WHERE email=?')->execute([$email]); 
        db()->prepare('INSERT INTO password_reset_tokens (email,token) VALUES (?,?)')->execute([$email,password_hash($token,PASSWORD_ARGON2ID)]); 
        db()->commit(); send_reset_email($email,$token); log_event('password_reset_requested',['email'=>$email]); 
    } catch(Throwable $e) { 
        if(db()->inTransaction()) db()->rollBack(); 
        throw $e; 
    } 
}

function valid_reset_token(string $email, string $token): bool { 
    $s=db()->prepare('SELECT token,created_at FROM password_reset_tokens WHERE email=?'); 
    $s->execute([$email]); 
    $row=$s->fetch(); 
    return $row && strtotime($row['created_at']) >= time()-(int)config('PASSWORD_RESET_MINUTES','60')*60 && password_verify($token,$row['token']); 
}

class ApplicationController
{
    public function dispatch(): void
    {
        global $path, $method;

        if ($user=current_user()) { 
            if ($user['must_change_password'] && !in_array($path,['/profile','/profile/password','/logout'],true)) { 
                flash('error','You must change your temporary password before continuing.'); 
                redirect('/profile'); 
            } 
        }

        if ($path==='/login' && $method==='GET') { 
            if(current_user()) redirect('/'); 
            view('auth/login',['title'=>'Sign in'],false); 
            exit; 
        }

        if ($path==='/login' && $method==='POST') {
            csrf(); 
            $email=strtolower(trim($_POST['email']??'')); 
            
            if(rate_limited($email)) { 
                log_event('login_rate_limited',['email'=>$email]); 
                flash('error','Too many attempts. Try again later.'); 
                redirect('/login'); 
            } 
            
            if(!attempt_login($email,$_POST['password']??'')) { 
                db()->prepare('INSERT INTO login_attempts (email,ip) VALUES (?,?)')->execute([$email,$_SERVER['REMOTE_ADDR']??'']); 
                log_event('login_failed',['email'=>$email]); 
                flash('error','Invalid email or password.'); 
                redirect('/login'); 
            } 
            
            db()->prepare('DELETE FROM login_attempts WHERE email=?')->execute([$email]); 
            $user=current_user(); 
            redirect($user['must_change_password'] ? '/profile' : '/'); 
        }

        if ($path==='/logout' && $method==='POST') { 
            csrf(); 
            logout(); 
            session_start(); 
            $_SESSION['csrf']=bin2hex(random_bytes(32)); 
            flash('success','You have been signed out.'); 
            redirect('/login'); 
        }

        if ($path==='/forgot-password' && $method==='GET') { 
            view('auth/forgot-password',['title'=>'Reset password'],false); 
            exit; 
        }

        if ($path==='/forgot-password' && $method==='POST') { 
            csrf(); 
            $email=strtolower(trim($_POST['email']??'')); 
            $s=db()->prepare('SELECT email FROM users WHERE email=? AND status="active"'); 
            $s->execute([$email]); 
            if($s->fetch()) { 
                try { 
                    reset_link($email); 
                } catch(Throwable $e) { 
                    log_event('password_reset_delivery_failed',['email'=>$email]); 
                } 
            } 
            flash('success','If that email is registered, a reset link has been sent.'); 
            redirect('/forgot-password'); 
        }

        if ($path==='/reset-password' && $method==='GET') { 
            $email=strtolower(trim($_GET['email']??'')); 
            $token=$_GET['token']??''; 
            if (!valid_reset_token($email,$token)) { 
                http_response_code(404); 
                render('Reset link unavailable','<p>This password reset link is invalid or expired.</p>',false); 
                exit; 
            } 
            view('auth/reset-password',['title'=>'Choose a new password','email'=>$email,'token'=>$token],false); 
            exit; 
        }

        if ($path==='/reset-password' && $method==='POST') { 
            csrf(); 
            $email=strtolower(trim($_POST['email']??'')); 
            $token=$_POST['token']??''; 
            $error=valid_password($_POST['password']??'') ?: (($_POST['password']??'')!==($_POST['confirm_password']??'') ? 'Passwords do not match.' : null); 
            if(!valid_reset_token($email,$token)) $error='This reset link is invalid or expired.'; 
            if($error){
                flash('error',$error);
                redirect('/reset-password?email='.rawurlencode($email).'&token='.rawurlencode($token));
            } 
            db()->beginTransaction(); 
            try{
                db()->prepare('UPDATE users SET password=?,must_change_password=0 WHERE email=?')->execute([password_hash($_POST['password'],PASSWORD_ARGON2ID),$email]);
                db()->prepare('DELETE FROM password_reset_tokens WHERE email=?')->execute([$email]);
                db()->commit();
                log_event('password_reset',['email'=>$email]);
            }catch(Throwable $e){
                db()->rollBack();
                throw $e;
            } flash('success','Password changed. You can now sign in.');
            redirect('/login'); 
        }

        if ($path==='/register' && $method==='GET') { 
            require_super_admin(); 
            view('auth/register',['title'=>'Create account']);
            exit; 
        }

        if ($path==='/register' && $method==='POST') { 
            
            require_super_admin(); 
            csrf(); 
            
            $email=strtolower(trim($_POST['email']??'')); 
            $error=valid_password($_POST['password']??'') ? : (($_POST['password']??'')!==($_POST['confirm_password']??'')?'Passwords do not match.':null); 
            
            if(!filter_var($email,FILTER_VALIDATE_EMAIL))$error='Enter a valid email.'; 

            if($error){
                flash('error',$error);
                redirect('/register');
            } 

            try{
                db()->prepare('INSERT INTO users (name,email,password,role,must_change_password) VALUES (?,?,?,?,1)')->execute([trim($_POST['name']??''),$email,password_hash($_POST['password'],PASSWORD_ARGON2ID),'admin']);
                log_event('user_created',['email'=>$email]);flash('success','Account created.');

            }catch(PDOException $e){
                flash('error','An account with that email already exists.');
            } 
            redirect('/users'); 
        }

        if ($path==='/profile' && $method==='GET') { 
            $u=require_auth(); 
            view('profile/profile',['title'=>'Profile','u'=>$u]); 
            exit; 
        }

        if ($path==='/profile/password' && $method==='POST') { 
            $u=require_auth(); 
            csrf(); 
            $s=db()->prepare('SELECT password FROM users WHERE id=?');
            $s->execute([$u['id']]);$old=$s->fetchColumn(); 
            $error=null; 
            if(!password_verify($_POST['current_password']??'',$old)) $error='Current password is incorrect.'; 
            elseif($error=valid_password($_POST['password']??'')) {

            } elseif(($_POST['password']??'')!==($_POST['confirm_password']??'')) $error='Passwords do not match.'; 
            elseif(password_verify($_POST['password'],$old)) $error='New password must differ from current password.'; 
            
            if($error){
                flash('error',$error);redirect('/profile');
            } 
            db()->prepare('UPDATE users SET password=?,must_change_password=0 WHERE id=?')->execute([password_hash($_POST['password'],PASSWORD_ARGON2ID),$u['id']]);
            session_regenerate_id(true);
            log_event('password_changed',['user_id'=>$u['id']]);
            flash('success','Password changed.');
            redirect('/profile'); 
        }

        if ($path==='/users' && $method==='GET') { 
            require_super_admin(); 
            $users=db()->query('SELECT id,name,email,role,status,created_at,last_login_at FROM users ORDER BY created_at DESC')->fetchAll(); 
            $rows='';
            foreach($users as $u){
                $rows.='<tr><td>'.e($u['name']).'</td><td>'.e($u['email']).'</td><td>'.e($u['role']).'</td><td>'.e($u['status']).'</td><td>'.e($u['created_at']).'</td><td>'.e($u['last_login_at']?:'—').'</td><td><form method="post" action="/users/reset" style="margin:0">'.form_token().'<input type="hidden" name="email" value="'.e($u['email']).'"><button class="btn btn-sm btn-outline-primary py-1 mt-2 mb-2" alt="Send Password Reset Link">Send Reset Email</button></form><form method="post" action="/users/password" style="margin:0"><input type="hidden" name="id" value="'.e((string)$u['id']).'">'.form_token().'<input name="password" type="password" placeholder="New password" class="form-control form-control-sm mt-2 mb-1" required><input name="confirm_password" type="password" placeholder="Confirm password" class="form-control form-control-sm mt-1 mb-2" required><button class="btn btn-sm btn-outline-primary py-1 mt-2 mb-2">Set password</button></form><form method="post" action="/users/role" style="margin:0"><input type="hidden" name="id" value="'.e((string)$u['id']).'">'.form_token().'<select class="form-select form-select-sm mt-2 mb-2" name="role"><option value="admin"'.($u['role']==='admin'?' selected':'').'>Admin</option><option value="super_admin"'.($u['role']==='super_admin'?' selected':'').'>Super Admin</option></select><button  class="btn btn-sm btn-outline-primary py-1 mt-2 mb-2">Update role</button></form></td></tr>'; 
            } 
            
            view('profile/user-management',['title'=>'User Management','rows'=>$rows]);
            exit; 
        }

        if ($path==='/users/password' && $method==='POST') { 
            $actor=require_super_admin();
            csrf();
            $id=(int)($_POST['id']??0);
            $error=valid_password($_POST['password']??'') ?: (($_POST['password']??'')!==($_POST['confirm_password']??'')?'Passwords do not match.':null);
            if(!$id)$error='Invalid user.';
            if($error){
                flash('error',$error);
                redirect('/users');
            }
            db()->prepare('UPDATE users SET password=?,must_change_password=1 WHERE id=?')->execute([password_hash($_POST['password'],PASSWORD_ARGON2ID),$id]);
            log_event('admin_password_changed',['actor_id'=>$actor['id'],'user_id'=>$id]);
            flash('success','User password updated. They must change it after signing in.');
            redirect('/users'); 
        }

        if ($path==='/users/role' && $method==='POST') { 
            $actor=require_super_admin();
            csrf();
            $id=(int)($_POST['id']??0);
            $role=$_POST['role']==='super_admin'?'super_admin':'admin';
            if($id===$actor['id'] && $role!=='super_admin'){
                flash('error','You cannot remove your own Super Admin access.');
                redirect('/users');
            }
            db()->prepare('UPDATE users SET role=? WHERE id=?')->execute([$role,$id]);
            log_event('user_role_changed',['actor_id'=>$actor['id'],'user_id'=>$id,'role'=>$role]);
            flash('success','User role updated.');
            redirect('/users'); 
        }

        if ($path==='/users/reset' && $method==='POST') { 
            require_super_admin();
            csrf();
            $email=strtolower(trim($_POST['email']??''));
            $s=db()->prepare('SELECT email FROM users WHERE email=?');
            $s->execute([$email]);
            if($s->fetch()){
                try{
                    reset_link($email);
                    flash('success','Password reset link sent.');
                }catch(Throwable $e){
                    flash('error','Could not send reset email. Check mail configuration.');
                }
            }
            redirect('/users');
        }

        if ($path==='/press-releases' && $method==='GET') { 
            view('releases/press-releases',['title'=>'Press Releases']); 
            exit; 
        }

        if (in_array($path,['/','/resources','/calendar','/trainings','/guidelines','/press-releases','/static-1','/static-2'],true)) { 
            $u=require_auth(); 
            if($path==='/') view('dashboard/home',['title'=>'Homepage']); 
            else view('static/placeholder',['title'=>ucwords(trim(str_replace('-',' ',$path),'/'))]); 
            exit; 
        }

        http_response_code(404); 
        render('Not found','<p>The requested page does not exist.</p>',false);

    }

}
