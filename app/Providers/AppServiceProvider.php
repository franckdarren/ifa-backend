<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
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
        if ($base64 = env('FIREBASE_CREDENTIALS_BASE64')) {
            $json = base64_decode($base64);
            Storage::disk('local')->put('firebase/firebase_credentials.json', $json);
            config([
                'firebase.credentials.file' => storage_path('app/firebase/firebase_credentials.json'),
            ]);
        }
    }
}
