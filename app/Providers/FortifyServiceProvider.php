<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\ServiceProvider;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ────────────────────────────────────────────────────────────────
        // Custom redirect after successful login based on role
        // ────────────────────────────────────────────────────────────────

        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $user = $request->user();

                    // Both admin_level_1 and admin_level_2 share the SAME dashboard
                    if (in_array($user->role ?? null, ['admin_level_1', 'admin_level_2'])) {
                        return redirect()->route('admin.dashboard');
                    }

                    // Driver dashboard
                    if ($user->role === 'driver') {
                        return redirect()->route('driver.dashboard');
                    }

                    
                    if ($user->role === 'worker') {
                        return redirect()->route('worker.dashboard');
                    }

                    // Fallback for pending users or unknown roles
                    return redirect()->route('dashboard');
                }
            };
        });
    }

}
