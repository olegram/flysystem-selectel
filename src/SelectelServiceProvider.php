<?php

namespace ArgentCrusade\Flysystem\Selectel;

use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use ArgentCrusade\Selectel\CloudStorage\CloudStorage;
use ArgentCrusade\Selectel\CloudStorage\Api\ApiClient;
use Illuminate\Contracts\Cache\Repository;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItemPool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;

class SelectelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Storage::extend('selectel', function ($app, $config) {
            $api = new ApiClient($config['username'], $config['password']);

            $psr6 = new CacheItemPool($app->make(Repository::class));
            $psr16 = new SimpleCacheBridge($psr6);
            $api->setCache($psr16);

            $storage = new CloudStorage($api);
            $container = $storage->getContainer($config['container']);

            if (isset($config['container_url'])) {
                $container->setUrl($config['container_url']);
            }

            return new Filesystem(new SelectelAdapter($container));
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
