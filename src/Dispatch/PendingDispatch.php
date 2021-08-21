<?php


namespace SimpleAmqp\Dispatch;


use Interop\Amqp\Impl\AmqpMessage;

class PendingDispatch
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    protected $message;
    protected $destination;

    protected $delay = 0;
    protected $expire = 0;
    protected $priority = 0;
    protected $exchange_type = '';
    protected $routing_key = '';

    /**
     * PendingDispatch constructor.
     * @param \JsonSerializable|array|AmqpMessage|string $message
     * @param string $destination
     * @param Dispatcher $dispatcher
     */
    public function __construct($message, string $destination, Dispatcher $dispatcher)
    {
        $this->message = $message;
        $this->destination = $destination;
        $this->dispatcher = $dispatcher;
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setPriority(int $priority = 0)
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setExchangeType(string $type, string $key = '')
    {
        $this->exchange_type = $type;
        if ($key !== '') {
            $this->setRoutingKey($key);
        }
        return $this;
    }

    public function getExchageType()
    {
        return $this->exchange_type;
    }

    public function setRoutingKey(string $key = '')
    {
        $this->routing_key = $key;
        return $this;
    }

    public function getRoutingKey()
    {
        return $this->routing_key;
    }

    public function delay(int $ttl = 0)
    {
        $this->delay = $ttl;
        return $this;
    }

    public function getDelay()
    {
        return $this->delay;
    }

    public function expire(int $ttl = 0)
    {
        $this->expire = $ttl;
        return $this;
    }

    public function getExpire()
    {
        return $this->expire;
    }

    public function __destruct()
    {
        if ($this->exchange_type) {
            $this->dispatcher->sendToExchange($this);
        } else {
            $this->dispatcher->sendToQueue($this);
        }
    }
}