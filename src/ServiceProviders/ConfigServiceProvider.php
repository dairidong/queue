<?php


namespace SimpleAmqp\ServiceProviders;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SimpleAmqp\Config;

class ConfigServiceProvider implements ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple['config'] = function ($app) {
            return new Config($app->getConfig());
        };
    }
}