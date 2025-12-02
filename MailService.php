<?php

// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService
{
    private $mailer;
    private $config;

    // Initialize PHPMailer with SMTP configuration
    public function __construct()
    {
        // Load email configuration
        $this->config = require_once __DIR__ . '/mail_config.php';
        $this->mailer = new PHPMailer(true);

        // Configure SMTP settings
        $this->mailer->isSMTP();
        $this->mailer->Host = 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = '480b1a2184fe02';
        $this->mailer->Password = '380b913c9896b7';
        $this->mailer->Port = 465;

        // Connection settings
        $this->mailer->Timeout = 10;
        $this->mailer->SMTPKeepAlive = false;

        // Set default sender
        $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
        
        // Disable debug output (0 = off, 1 = client, 2 = server)
        $this->mailer->SMTPDebug = 0;
    }

    // Send email to recipient
    public function send($to, $subject, $htmlBody, $textBody = '')
    {
        try {
            // Add recipient
            $this->mailer->addAddress($to);

            // Set email content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $htmlBody;
            // Use provided text or strip HTML tags as fallback
            $this->mailer->AltBody = $textBody ?: strip_tags($htmlBody);

            // Send email
            $result = $this->mailer->send();
            error_log("Mail sending attempt result: " . ($result ? "Success" : "Failed"));
            return $result;
        } catch (Exception $e) {
            // Log errors
            error_log('Mailer Error: ' . $this->mailer->ErrorInfo);
            error_log('Exception: ' . $e->getMessage());
            return false;
        } finally {
            // Clear recipient list for next email
            $this->mailer->clearAddresses();
        }
    }
}