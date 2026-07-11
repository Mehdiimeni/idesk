<?php
<<<<<<< HEAD

=======
>>>>>>> 5591029... some change
namespace ICore;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

<<<<<<< HEAD
class SmtpMailer
{
    private PHPMailer $mail;

    public function __construct(
        string $host,
        string $username,
        string $password,
        int $port = 587,
        string $secure = 'tls'
    ) {
        $this->mail = new PHPMailer(true);

=======
class SmtpMailer {
    private $mail;

    public function __construct($host, $username, $password, $port = 587, $secure = 'tls') {
        $this->mail = new PHPMailer(true);
>>>>>>> 5591029... some change
        $this->mail->isSMTP();
        $this->mail->Host = $host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $username;
        $this->mail->Password = $password;
        $this->mail->SMTPSecure = $secure;
        $this->mail->Port = $port;
<<<<<<< HEAD
        $this->mail->CharSet = 'UTF-8';

        $this->mail->isHTML(true);
    }

    public function sendMail(
        string $to,
        string $subject,
        string $body,
        string $fromName = '',
        string $fromEmail = ''
    ): bool {
        try {
            $this->resetMail();

            $senderEmail = $fromEmail !== ''
                ? $fromEmail
                : $this->mail->Username;

            $this->mail->setFrom($senderEmail, $fromName);
            $this->mail->addAddress($to);

            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);

            return $this->mail->send();

        } catch (Exception $e) {
            return false;
        }
    }

    private function resetMail(): void
    {
        $this->mail->clearAddresses();
        $this->mail->clearCCs();
        $this->mail->clearBCCs();
        $this->mail->clearReplyTos();
        $this->mail->clearAttachments();
        $this->mail->clearCustomHeaders();
    }
}
=======
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
>>>>>>> 5591029... some change
