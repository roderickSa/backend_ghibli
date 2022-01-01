<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Str;

class TymonTWTRoderProvider extends EloquentUserProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function retrieveByCredentials(array $credentials) {
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'pass')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }
    public function validateCredentials(UserContract $user, array $credentials) {
        $plain = $credentials['pass'];
        return $plain == $user->getAuthPassword();
    }
}
