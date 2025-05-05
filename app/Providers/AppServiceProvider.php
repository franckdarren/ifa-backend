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
            // 1) Décoder la chaîne Base64
            $json = base64_decode($base64);

            // 2) S'assurer que le dossier existe
            if (!Storage::disk('local')->exists('firebase')) {
                Storage::disk('local')->makeDirectory('firebase');
            }

            // 3) Écrire le fichier JSON dans storage/app/firebase
            Storage::disk('local')->put('firebase/firebase_credentials.json', $json);

            // 4) Mettre à jour la config Kreait pour pointer sur ce fichier
            config([
                'firebase.credentials.file' => storage_path('app/firebase/firebase_credentials.json'),
            ]);
        }
    }
}
