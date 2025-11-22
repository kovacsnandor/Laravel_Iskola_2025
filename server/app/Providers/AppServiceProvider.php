<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route; // <--- EZT KELL HOZZÁADNI
use Laravel\Sanctum\Http\Middleware\CheckAbilities; // <--- EZT KELL HOZZÁADNI
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
        //
        // Az alapértelmezett string hossza 191 karakterre csökkentése
        Schema::defaultStringLength(191);
        //Middleware regisztráció
        Route::aliasMiddleware('ability', CheckAbilities::class);

        //2. KIVÉTELKEZELÉS REGISZTRÁCIÓJA
        Exceptions::renderable(function (AccessDeniedHttpException $e, $request) {
            // Csak API kérésekre fusson le
            if ($request->is('api/*')) {
                $message = $e->getMessage() ?? 'Access denied.';

                if (str_contains($message, 'Invalid ability provided.')) {
                    $message = 'Access denied.';
                }

                return response()->json([
                    'message' => $message
                ], 403);
            }
        });

        // Exceptions::renderable(function (\Throwable $e, $request) {
        //     if ($request->is('api/*')) {
        //         // Csak a hiba idejére, hogy lásd a valódi kivételt
        //         if ($e->getMessage() === 'Invalid ability provided.') {
        //             // Írd ki a konzolra (vagy logba) a teljes hibaüzenetet és stack trace-t
        //             Log::error('Sanctum Ability Hiba:', ['exception' => $e]);

        //             // Küldd vissza a részletes hibaüzenetet
        //             return response()->json([
        //                 'message' => 'Hiba történt a képességek ellenőrzésekor.',
        //                 'error_details' => $e->getMessage(),
        //                 'file' => $e->getFile(),
        //                 'line' => $e->getLine(),
        //             ], 500); // Használjunk 500-at technikai hibára
        //         }
        //     }
        // });


        //Policy regisztráció
        Gate::policy(User::class, UserPolicy::class);
    }
}
