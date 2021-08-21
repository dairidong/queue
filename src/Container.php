<?php


namespace SimpleAmqp;


use Pimple\Container as BaseContainer;
use SimpleAmqp\ServiceProviders\ConfigServiceProvider;
use SimpleAmqp\ServiceProviders\ConnectionServiceProvider;
use SimpleAmqp\ServiceProviders\ContextServiceProvider;
use SimpleAmqp\ServiceProviders\DispatchServiceProvider;

/**
 * Class Container
 * @package Container
 */
class Container extends BaseContainer
{
    /**
     * @var array
     */
    protected $input_config = [];

    /**
     * @var string[]
     */
    protected $providers = [
        ConfigServiceProvider::class,
        ContextServiceProvider::class,
        ConnectionServiceProvider::class,
        DispatchServiceProvider::class
    ];

    public function __construct(array $config = [], array $values = [])
    {
        $this->input_config = $config;
        parent::__construct($values);

        $this->registerProviders($this->providers);
    }

    protected function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $this->register(new $provider);
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->input_config;
    }


}