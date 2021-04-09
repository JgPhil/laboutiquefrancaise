<?php

namespace App\Classes\Mail;


use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\Dotenv\Dotenv;
use App\Classes\Mail\AppMailerInterface;

class MailSender implements AppMailerInterface
{
    public function __construct()
    {
        $path = dirname(__DIR__, 3) . '/.env.local';
        (new Dotenv())->load($path);
    }


    public function send($toEmail, $toName, $subject, $content)
    {
        $mj = new Client($_ENV['API_KEY'], $_ENV['API_KEY_SECRET'], true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $_ENV['ADMIN_EMAIL'],
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => "$toName"
                        ]
                    ],
                    'TemplateID' => 2770435,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
    }
}
