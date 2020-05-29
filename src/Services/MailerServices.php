<?php

namespace App\Services;


class MailerServices
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail($subject, $message) {
        $send_mail = (new \Swift_Message($subject))
            ->setFrom('noreply@locahost')
            ->setTo($_ENV["MAILEVENT"] ?? 'to2@localhost')
            ->setBody(
                $message
                , 'text/html');

        $this->mailer->send($send_mail);

        return true;
    }
}