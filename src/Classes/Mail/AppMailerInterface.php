<?php
namespace App\Classes\Mail;

interface AppMailerInterface
{
    public function send($toEmail, $toName, $subject, $content);
}
