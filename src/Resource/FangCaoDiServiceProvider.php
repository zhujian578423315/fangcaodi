<?php

namespace FangCaoDi\Resource;

use Illuminate\Support\ServiceProvider;

class FangCaoDiServiceProvider extends ServiceProvider
{

    protected $defer = false; // 延迟加载服务

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/fangcaodi.php' => config_path('fangcaodi.php'), // 发布配置文件到 laravel 的config 下
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('fangCaoDi', function ($app) {
            return new FangCaoDiController();
        });
        dd($this->app);

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */

    public function provides()
    {

        // 因为延迟加载 所以要定义 provides 函数 具体参考laravel 文档

        return ['fangCaoDi'];

    }
}
