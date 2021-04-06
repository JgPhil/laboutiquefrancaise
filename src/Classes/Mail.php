<?php

namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\Dotenv\Dotenv;

class Mail
{
    private $dotenv;

    public function __construct()
    {
        $this->dotenv = (new Dotenv())->load(dirname(__DIR__, 2) . '/.env.local');
    }


    public function send($toEmail, $toName, $subject, $content)
    {
        $mj = new Client($_ENV['API_KEY'], $_ENV['API_KEY_SECRET'], true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "jamingph@gmail.com",
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
