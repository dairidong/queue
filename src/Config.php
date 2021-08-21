<?php


namespace SimpleAmqp;


class Config
{
    /**
     * @var array
     */
    protected $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConnectionConfig()
    {
        return array_merge([
            'host'               => 'localhost',
            'port'               => 5672,
            'user'               => 'guest',
            'pass'               => 'guest',
            'vhost'              => '/',
            'read_timeout'       => 3.,
            'write_timeout'      => 3.,
            'connection_timeout' => 3.,
            'heartbeat'          => 0,
            'persisted'          => false,
            'lazy'               => true,
            'qos_global'         => false,
            'qos_prefetch_size'  => 0,
            'qos_prefetch_count' => 1,
            'ssl_on'             => false,
            'ssl_verify'         => true,
            'ssl_cacert'         => '',
            'ssl_cert'           => '',
            'ssl_key'            => '',
            'ssl_passphrase'     => '',
        ], $this->config['connection'] ?: []);
    }
}