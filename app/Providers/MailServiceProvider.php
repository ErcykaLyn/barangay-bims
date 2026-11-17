<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->resolving('mail.manager', function (MailManager $mailManager) {
            $mailManager->extend('smtp', function ($config) {
                // Kunin ang settings mula sa config
                $host = $config['host'] ?? 'smtp.gmail.com';
                $port = $config['port'] ?? 587;
                $username = $config['username'] ?? null;
                $password = $config['password'] ?? null;

                // Gumawa ng EsmtpTransport
                $transport = new EsmtpTransport($host, $port);
                
                // I-set ang credentials
                if ($username && $password) {
                    $transport->setUsername($username);
                    $transport->setPassword($password);
                }

                // I-disable ang SSL verification sa pinaka-ilalim
                $stream = $transport->getStream();
                $stream->setStreamOptions([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ]);

                return $transport;
            });
        });
    }
}