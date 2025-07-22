<?php

namespace App\Providers;

use App\Models\Message;
use App\Observers\MessageObserver;
use App\Helpers\UserHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        
        // Enregistrer l'observateur pour les messages
        Message::observe(MessageObserver::class);
        
        // Enregistrer le helper UserHelper comme fonction globale
        if (!function_exists('user_profile_image')) {
            function user_profile_image($user, $size = 50, $class = 'rounded-circle') {
                return UserHelper::getProfileImage($user, $size, $class);
            }
        }
        
        // Directive Blade pour l'image de profil
        Blade::directive('userImage', function ($expression) {
            return "<?php echo user_profile_image($expression); ?>";
        });

        // Directive Blade pour afficher les images de profil
        Blade::directive('profileImage', function ($expression) {
            return "<?php echo \App\Helpers\ImageHelper::displayProfileImage($expression); ?>";
        });

        // Directive Blade pour afficher les images
        Blade::directive('image', function ($expression) {
            return "<?php echo \App\Helpers\ImageHelper::displayImage($expression); ?>";
        });
    }
}
