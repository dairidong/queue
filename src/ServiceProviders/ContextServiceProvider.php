<?php


namespace SimpleAmqp\ServiceProviders;


use Enqueue\AmqpLib\AmqpConnectionFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ContextServiceProvider implements ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple['context'] = function ($app) {
            return new AmqpConnectionFactory($app['config']->getConnectionConfig());
        };
    }
}