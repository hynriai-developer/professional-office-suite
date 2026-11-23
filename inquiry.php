<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo '{"ok":false}'; exit; }
$clean = function($k, $max = 2000) {
  $v = isset($_POST[$k]) ? trim((string)$_POST[$k]) : '';
  $v = str_replace(["\r", "\0"], '', $v);
  return mb_substr($v, 0, $max);
};
if ($clean('website') !== '') { echo '{"ok":true}'; exit; } // honeypot
$name = preg_replace('/[\r\n]+/', ' ', $clean('name', 120));
$email = $clean('email', 200);
$phone = preg_replace('/[\r\n]+/', ' ', $clean('phone', 60));
$suite = preg_replace('/[\r\n]+/', ' ', $clean('suite', 200));
$msg = $clean('msg', 3000);
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(400); echo '{"ok":false}'; exit; }
$to = 'conniebarrettllc@gmail.com';
$subject = 'Office suite inquiry: ' . $suite;
$body = "Name: $name\nPhone: $phone\nEmail: $email\nInterested in: $suite\n\n$msg\n";
$host = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'professionalofficesuite.com');
$headers = "From: Professional Office Suite <noreply@$host>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";
$ok = mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
http_response_code($ok ? 200 : 500);
echo $ok ? '{"ok":true}' : '{"ok":false}';
