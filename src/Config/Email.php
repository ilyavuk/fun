<?php

namespace App\Config;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{

    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer;
//Tell PHPMailer to use SMTP
        $this->mail->isSMTP();
//Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 2;
//Set the hostname of the mail server
        $this->mail->Host = 'smtp.gmail.com';
// use
        // $this->mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = "";
//Password to use for SMTP authentication
        $this->mail->Password = "";
//Set who the message is to be sent from
        $this->mail->setFrom('from@test.com', 'First Last');
        $this->mail->From = 'user@domain.com';

//Set an alternative reply-to address

        $this->mail->addAddress('test...', 'John Doe');
//Set the subject line
        $this->mail->Subject = 'PHPMailer GMail SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        // $this->mail->msgHTML(file_get_contents('contents.html'), __DIR__);
//Replace the plain text body with one created manually

        $this->mail->Body = "This is a test email";
        $this->mail->AltBody = 'This is a plain-text message body';

        $this->mail->send();
//Attach an image file
        // $this->mail->addAttachment('images/phpmailer_mini.png');

    
    }

}
