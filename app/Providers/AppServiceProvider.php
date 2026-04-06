<?php

namespace App\Providers;

use App\Models\Reserva;
use App\Observers\ReservaObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Reserva::observe(ReservaObserver::class);

        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verifica tu correo - ' . config('app.name'))
                ->view('emails.verificar-correo', ['url' => $url, 'user' => $notifiable]);
        });

        View::composer('layouts.cliente', function ($view) {
            $userId = Auth::id();

            if (!$userId) {
                $view->with('notificaciones', collect());
                return;
            }

            $notificaciones = Reserva::with('espacio')
                ->where('user_id', $userId)
                ->orderBy('reserva_id', 'DESC')
                ->take(5)
                ->get();

            $view->with('notificaciones', $notificaciones);
        });
    }
}
