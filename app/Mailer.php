<?php
declare(strict_types=1);
require_once __DIR__.'/Bootstrap.php';

function send_reset_email(string $to, string $token): void {
  $url=rtrim(config('APP_URL','http://localhost:8000'),'/').'/reset-password?token='.rawurlencode($token).'&email='.rawurlencode($to);
  $subject='Reset your '.config('APP_NAME','LIGHTHOUSE').' password'; $body="A password reset was requested. This link expires in ".config('PASSWORD_RESET_MINUTES','60')." minutes:\r\n\r\n{$url}\r\n\r\nIf you did not request this, ignore this message.";
  $headers='From: '.config('MAIL_FROM_NAME','LIGHTHOUSE').' <'.config('MAIL_FROM_ADDRESS').">\r\nContent-Type: text/plain; charset=UTF-8";
  if (config('APP_ENV','local') === 'local' && !config('SMTP_HOST')) { file_put_contents(dirname(__DIR__).'/storage/logs/mail.log',"TO: {$to}\nSUBJECT: {$subject}\n{$body}\n---\n",FILE_APPEND|LOCK_EX); return; }
  smtp_send($to, $subject, $body, $headers);
}
function smtp_send(string $to, string $subject, string $body, string $headers): void {
  $host=config('SMTP_HOST'); if (!$host) throw new RuntimeException('SMTP_HOST is required outside local development.');
  $port=(int)config('SMTP_PORT','587'); $encryption=strtolower(config('SMTP_ENCRYPTION','tls') ?? '');
  $socket=@stream_socket_client(($encryption==='ssl'?'ssl://':'tcp://').$host.':'.$port,$errno,$errstr,15,STREAM_CLIENT_CONNECT);
  if (!$socket) throw new RuntimeException('SMTP connection failed.'); stream_set_timeout($socket,15);
  $read=function() use($socket){ $reply=''; do { $line=fgets($socket,515); if($line===false) throw new RuntimeException('SMTP server did not respond.'); $reply.=$line; } while(isset($line[3]) && $line[3]==='-'); if((int)substr($reply,0,3)>=400) throw new RuntimeException('SMTP delivery failed.'); return $reply; };
  $write=function(string $command) use($socket,$read){ fwrite($socket,$command."\r\n"); return $read(); };
  $read(); $write('EHLO localhost'); if($encryption==='tls') { $write('STARTTLS'); if(!stream_socket_enable_crypto($socket,true,STREAM_CRYPTO_METHOD_TLS_CLIENT)) throw new RuntimeException('SMTP TLS negotiation failed.'); $write('EHLO localhost'); }
  $username=config('SMTP_USERNAME'); if($username) { $write('AUTH PLAIN '.base64_encode("\0{$username}\0".config('SMTP_PASSWORD',''))); }
  $from=config('MAIL_FROM_ADDRESS'); $write('MAIL FROM:<'.$from.'>'); $write('RCPT TO:<'.$to.'>'); $write('DATA'); fwrite($socket,"Subject: {$subject}\r\n{$headers}\r\n\r\n".str_replace("\n.","\n..",$body)."\r\n.\r\n"); $read(); $write('QUIT'); fclose($socket);
}
