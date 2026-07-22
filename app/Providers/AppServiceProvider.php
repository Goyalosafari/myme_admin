<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

class AppServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        Paginator::useBootstrapFive();

        Mail::extend('smtp', function (array $config) {
            $scheme = match(true) {
                ($config['encryption'] ?? '') === 'ssl'  => 'smtps',
                ($config['encryption'] ?? '') === 'tls'  => 'smtp',
                default                                  => '',
            };

            $transport = (new EsmtpTransportFactory)->create(new Dsn(
                $scheme,
                $config['host'],
                $config['username'] ?? null,
                $config['password'] ?? null,
                $config['port'] ?? null
            ));

            $transport->getStream()->setStreamOptions([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                ],
            ]);

            return $transport;
        });
    }
}
