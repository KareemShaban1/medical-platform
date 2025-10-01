<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewAdmin;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\CustomAuthentication;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $request = request();
        // check if route start with admin/
        if ($request->is('admin/*')) {
            Config::set('fortify.guard', 'admin');
            Config::set('fortify.password', 'admins');
            Config::set('fortify.prefix', 'admin');
        }

        if ($request->is('clinic/*')) {
            Config::set('fortify.guard', 'clinic');
            Config::set('fortify.password', 'clinic_users');
            Config::set('fortify.prefix', 'clinic');
        }

        if ($request->is('supplier/*')) {
            Config::set('fortify.guard', 'supplier');
            Config::set('fortify.password', 'supplier_users');
            Config::set('fortify.prefix', 'supplier');
        }

        // Handle patient routes (frontend patient auth - login only)
        // Only set patient guard for exact frontend routes (not clinic/* or supplier/* routes)
        if (($request->is('login') || $request->is('forgot-password') || $request->is('reset-password'))
            && !$request->is('clinic/*') && !$request->is('supplier/*') && !$request->is('admin/*')) {
            Config::set('fortify.guard', 'patient');
            Config::set('fortify.password', 'patients');
            Config::set('fortify.prefix', '');
        }





        //// login response
        // redirect user (admin/clinic/supplier) after login
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                if ($request->user('supplier')) {
                    // redirect supplier to /supplier/dashboard
                    return redirect('/supplier/dashboard');
                }

                if ($request->user('clinic')) {
                    // redirect clinic to /clinic/dashboard
                    return redirect('/clinic/dashboard');
                }

                if ($request->user('admin')) {
                    // dd("admin");
                    return redirect('/admin/dashboard');
                }

                if ($request->user('patient')) {
                    // redirect patient to dashboard or homepage
                    return redirect('/user');
                }

                return redirect('/');
            }
        });


        //// logout response
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                return redirect('/');
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            //// block ip which try more than 5 failed attempts
            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });



        if (Config::get('fortify.guard') == 'admin') {
            //// this method will be used in "web" guard only
            Fortify::authenticateUsing([new CustomAuthentication, 'authenticateAdmin']);
            //// point to clinic auth folder [views/clinic/auth]
            Fortify::viewPrefix('backend.dashboards.admin.auth.');
        } elseif (Config::get('fortify.guard') == 'supplier') {
            //// this method will be used in "supplier" guard only
            Fortify::authenticateUsing([new CustomAuthentication, 'authenticateSupplierUser']);
            //// point to clinic auth folder [views/clinic/auth]
            Fortify::viewPrefix('backend.dashboards.supplier.auth.');
        } elseif (Config::get('fortify.guard') == 'clinic') {
            //// this method will be used in "medical_laboratory" guard only
            Fortify::authenticateUsing([new CustomAuthentication, 'authenticateClinicUser']);
            //// point to clinic auth folder [views/clinic/auth]
            Fortify::viewPrefix('backend.dashboards.clinic.auth.');
        } elseif (Config::get('fortify.guard') == 'patient') {
            //// this method will be used for patient authentication
            Fortify::authenticateUsing([new CustomAuthentication, 'authenticatePatient']);
            //// point to frontend auth views
            Fortify::viewPrefix('frontend.auth.');
        }
    }
}
