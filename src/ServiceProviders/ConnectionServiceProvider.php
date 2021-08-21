<?php


namespace SimpleAmqp\ServiceProviders;


use Pimple\Container;
use SimpleAmqp\Connection;

class ConnectionServiceProvider implements \Pimple\ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple['connection'] = function ($app) {
            return new Connection($app['context']);
        };
    }
}