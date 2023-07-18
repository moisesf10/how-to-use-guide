<?php

namespace App\Providers;

use App\Models\Configuration;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
/*
        $configurations = Configuration::all();
        config()->set('app.system', $configurations);
        $this->parseConfigurations($configurations);

        Vite::macro('image', fn ($asset) => $this->asset("resources/img/{$asset}"));
        */
    }

    private function parseConfigurations(Collection $configurations){

        $smtp = $configurations->where('uid', 'smtp')->first();
        if($smtp){
            $host = $smtp?->content?->host ?? env('MAIL_HOST');
            $port = $smtp?->content?->port ?? env('MAIL_PORT');
            $security = $smtp?->content?->security ?? env('MAIL_ENCRYPTION');
            $userName = $smtp?->content?->login ?? env('MAIL_USERNAME');
            $password = $smtp?->content?->password ?? env('MAIL_PASSWORD');
            $mailFrom = $smtp?->content?->login ?? env('MAIL_FROM_ADDRESS');
            config()->set('mail.mailers.smtp.host', $host);
            config()->set('mail.mailers.smtp.port', $port);
            config()->set('mail.mailers.smtp.encryption', $security);
            config()->set('mail.mailers.smtp.username', $userName);
            config()->set('mail.mailers.smtp.password', $password);
        }

    }
}
