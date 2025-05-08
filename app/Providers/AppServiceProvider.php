<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        /**
         * Define Authorization Gates
         */
        Gate::define('SUPER_ADMIN', function (User $user) {
            return $user->hasOneRoles([UserRole::SUPER_ADMIN]);
        });

        // Roles Not Scoped under table keys
        foreach ([
            'RARE_SPP_ADMIN' => UserRole::RARE_SPP_ADMIN,
            'RARE_SPP_READER_ALL' => UserRole::RARE_SPP_READER_ALL,
            'CL_CREATE' => UserRole::CL_CREATE,
            'KEY_ADMIN' => UserRole::KEY_ADMIN,
            'KEY_EDITOR' => UserRole::KEY_EDITOR,
            'TAXONOMY' => UserRole::TAXONOMY,
            'TAXON_PROFILE' => UserRole::TAXON_PROFILE,
            'GLOSSARY_EDITOR' => UserRole::GLOSSARY_EDITOR,
        ] as $gate_name => $database_value) {
            Gate::define($gate_name, function (User $user) use ($database_value) {
                return $user->hasOneRoles([
                    UserRole::SUPER_ADMIN,
                    $database_value,
                ]);
            });
        }

        //  Not yet implemented
        // 'RARE_SPP_READER'
        // 'COLL_ADMIN'
        // 'COLL_EDITOR'
        // 'DATASET_ADMIN'
        // 'DATASET_EDITOR'
        // 'PROJ_ADMIN'
        Gate::define('CL_ADMIN', function (User $user, $clid) {
            return $user->hasOneRoles([
                UserRole::SUPER_ADMIN,
                UserRole::CL_ADMIN => $clid,
            ]);
        });

        Gate::define('PROJ_ADMIN', function (User $user, $pid) {
            return $user->hasOneRoles([
                UserRole::SUPER_ADMIN,
                UserRole::PROJ_ADMIN => $pid,
            ]);
        });

        Gate::define('COLL_EDIT', function (User $user, $collid) {
            return $user->hasOneRoles([
                UserRole::SUPER_ADMIN,
                UserRole::COLL_ADMIN => $collid,
                UserRole::COLL_EDITOR => $collid,
            ]);
        });

        /**
         * Setup Blade Component Folders and Directives
         */
        Blade::anonymousComponentPath(__DIR__ . '/../../resources/views/custom');
        Blade::anonymousComponentPath(__DIR__ . '/../../resources/views/core');

        $this->callAfterResolving('blade.compiler', static function (BladeCompiler $compiler) {
            $compiler->extend(static function ($value) {
                return \preg_replace('/\s*@trim\s*/m', '', $value);
            });
        });

        // Helper Macro to Progate Apline binds from parent component to subcomponent within blade. This May be required if you have dynamic names from generated input fields.
        Blade::directive('bind', function ($expression) {
            return '<?php echo $attributes["x-bind:" . "' . $expression . '"] ? \'x-bind:\'. "' . $expression . '" . \'="\' . $attributes["x-bind:" . "' . $expression . '"] . \'"\': "" ?>';
        });

        /**
         * Add Query logs For Local Enviroments.
         */
        if (config('app.env') === 'local') {
            DB::listen(function ($query) {
                File::append(
                    storage_path('/logs/query.log'),
                    '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
                );
            });
        }

        /**
         * Enable Orcid Oauth
         */
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('orcid', \App\Socialite\Orcid\Provider::class);
        });
    }
}
