<?php


namespace SimpleAmqp\ServiceProviders;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SimpleAmqp\Dispatch\Dispatcher;

class DispatchServiceProvider implements ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple['dispatcher'] = function ($app) {
            return new Dispatcher($app);
        };
    }
}