<?php

namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $apiKey;
    private $apiKeySecret;

    public function __construct()
    {
        $this->apiKey = getenv('API_KEY');
        $this->apiKeySecret= getenv('API_KEY_SECRET');
    }


    public function send($toEmail, $toName, $subject, $content)
    {
        $mj = new Client($this->apiKey, $this->apiKeySecret, true, ['version' => 'v3.1']);
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
