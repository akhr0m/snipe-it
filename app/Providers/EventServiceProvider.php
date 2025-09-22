<?php

namespace App\Providers;

use App\Listeners\CheckoutableListener;
use App\Listeners\LogListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            \App\Listeners\LogSuccessfulLogin::class,
        ],

        'Illuminate\Auth\Events\Failed' => [
            \App\Listeners\LogFailedLogin::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        LogListener::class,
        CheckoutableListener::class,
    ];
 /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Mendaftarkan UserObserver agar aktif
        //User::observe(UserObserver::class); 
    }
}
