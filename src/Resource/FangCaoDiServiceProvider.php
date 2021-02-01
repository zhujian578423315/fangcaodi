<?php

namespace FangCaoDi\Resource;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class FangCaoDiServiceProvider extends ServiceProvider
{

    protected $defer = true; // 延迟加载服务

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
        $this->app->booted(function () {
            $console_kernel = $this->app[\Illuminate\Contracts\Console\Kernel::class];
            //注册推送命令
            $console_kernel->registerCommand(new FangCaoDiOrderPush());
            //设定推送任务
            $schedule = $this->app[Schedule::class];
            $schedule->command(FangCaoDiOrderPush::class, ['--auto'])
                ->withoutOverlapping()->hourly()->between('1:00', '3:00');
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('fangCaoDi', function ($app) {
        });
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
