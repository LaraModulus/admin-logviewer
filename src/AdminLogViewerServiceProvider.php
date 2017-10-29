<?php

namespace LaraMod\Admin\Logs;

use Illuminate\Support\ServiceProvider;

class AdminLogViewerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'adminlogs');
        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/laramod/admin/logs'),
        ]);

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/routes.php';
    }
}
