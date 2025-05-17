<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        // Add a Sanctum token authentication callback to check if the user is active
        \Laravel\Sanctum\Sanctum::authenticateAccessTokensUsing(function ($accessToken, $isValid) {
            // If the token is valid, also check the user's status
            if ($isValid) {
                $user = $accessToken->tokenable;
                
                // If the user has status=0 or is soft-deleted, reject the token
                if ((isset($user->status) && (int)$user->status === 0) || (isset($user->deleted_at) && $user->deleted_at !== null)) {
                    // We're returning false to make the token invalid
                    return false;
                }
            }
            
            return $isValid;
        });

        //
        Blade::directive('currency', function ($amount) {
            return "<?php echo '₹' . number_format($amount, 2); ?>";
        });
    }
}
