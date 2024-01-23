<?php

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;
    private $logger;
     public function __construct(MailerInterface $mailer, LoggerInterface $logger)
     {
         $this->mailer = $mailer;
         $this->logger = $logger;

     }

    public function sendEmail($to, $subject, $body)
    {
        $email = (new Email())
            ->from('no-reply@monsite.com')
            ->addTo($to)
            ->subject($subject)
            ->text($body);

        $this->mailer->send($email);

        // Log the email sending
        $this->logger->info('Email sent', [
            'to' => $to,
            'subject' => $subject
        ]);
    }


}