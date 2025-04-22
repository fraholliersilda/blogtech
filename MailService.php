<?php
require_once __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService
{
    private $mailer;
    private $config;
    
    public function __construct()
    {
        $this->config = require_once __DIR__ . '/mail_config.php';
        $this->mailer = new PHPMailer(true);
        
        $this->mailer->isSMTP();
        $this->mailer->Host = 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = '480b1a2184fe02';
        $this->mailer->Password = '380b913c9896b7';
        $this->mailer->Port = 465;
        
        $this->mailer->Timeout = 10;
        $this->mailer->SMTPKeepAlive = false;
        
        $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
        $this->mailer->SMTPDebug = 0;
    }
    public function send($to, $subject, $htmlBody, $textBody = '')
{
    try {
        $this->mailer->addAddress($to);
        
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $htmlBody;
        $this->mailer->AltBody = $textBody ?: strip_tags($htmlBody);
        
        $result = $this->mailer->send();
        error_log("Mail sending attempt result: " . ($result ? "Success" : "Failed"));
        return $result;
    } catch (Exception $e) {
        error_log('Mailer Error: ' . $this->mailer->ErrorInfo);
        error_log('Exception: ' . $e->getMessage());
        return false;
    } finally {
        $this->mailer->clearAddresses();
    }
}
}