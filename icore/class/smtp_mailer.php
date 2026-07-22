<?php
namespace ICore;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SmtpMailer {
    private $mail;

    public function __construct($host, $username, $password, $port = 587, $secure = 'tls') {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = $host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $username;
        $this->mail->Password = $password;
        $this->mail->SMTPSecure = $secure;
        $this->mail->Port = $port;
        $this->mail->CharSet = "UTF-8";
    }

    public function sendMail($to, $subject, $body, $fromName = '', $fromEmail = '') {
        try {
            $this->mail->setFrom($fromEmail ?: $this->mail->Username, $fromName);
            $this->mail->addAddress($to);

            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false; 
        }
    }
}
