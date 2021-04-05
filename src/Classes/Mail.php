<?php

namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $apiKey = 'ea611911734157ca41449898cfc6fffb';
    private $apiKeySecret = '6c7b503ac839a9fd274185520f5a5a11';


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
