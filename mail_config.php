<?php

// SMTP configuration for email sending
return [
    'host' => 'sandbox.smtp.mailtrap.io',        // SMTP server host
    'port' => 2525,                               // SMTP server port
    'username' => '480b1a2184fe02',               // SMTP username
    'password' => '380b913c9896b7',               // SMTP password
    'encryption' => 'tls',                        // Encryption type (tls/ssl)
    'from_email' => 'noreply@yourdomain.com',     // Default sender email
    'from_name' => 'BlogTech Support'             // Default sender name
];