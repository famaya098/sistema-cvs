<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Plaza;
use App\Observers\PlazaObserver;

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
        // Registrar el observer para el modelo Plaza
        Plaza::observe(PlazaObserver::class);
        
        // validación de captcha
        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('captcha.secret'),
                'response' => $value,
                'remoteip' => request()->ip()
            ]);
            
            return $response->json()['success'];
        }, 'La verificación de seguridad falló. Por favor, intenta nuevamente.');
    }
}