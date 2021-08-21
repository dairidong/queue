<?php


namespace SimpleAmqp\Dispatch;


use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Interop\Amqp\Impl\AmqpMessage;
use Interop\Queue\Destination;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use SimpleAmqp\Connection;
use SimpleAmqp\Container;
use SimpleAmqp\Exceptions\InvalidMessageTypeException;

class Dispatcher
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * Dispatcher constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param array|scalar|JsonSerializable|AmqpMessage $message
     * @param string $destination
     * @return PendingDispatch
     */
    public function dispatch($message, string $destination)
    {
        return new PendingDispatch($message, $destination, $this);
    }

    public function sendToQueue(PendingDispatch $prepare)
    {
        $message = $this->createMessage($prepare->getMessage(), $prepare->getPriority(), $prepare->getExpire());
        $queue = $this->getConnection()->getQueue($prepare->getDestination());

        $this->send($queue, $message, $prepare->getDelay());
    }

    public function sendToExchange(PendingDispatch $prepare)
    {
        $message = $this->createMessage($prepare->getMessage(), $prepare->getPriority(), $prepare->getExpire());
        $exchange = $this->getConnection()->getExchange($prepare->getDestination());

        if ($routing_key = $prepare->getRoutingKey()) {
            $message->setRoutingKey($routing_key);
        }

        $this->send($exchange, $message, $prepare->getDelay());
    }

    protected function send(Destination $destination, AmqpMessage $message, int $delay)
    {
        $producer = $this->getConnection()->createProducer();

        if ($delay > 0) {
            $producer->setDelayStrategy(new RabbitMqDlxDelayStrategy())
                ->setDeliveryDelay($delay);
        }
        $producer->send($destination, $message);
    }

    protected function createMessage($message, int $priority, int $expire)
    {
        $message = $this->castMessage($message);

        if (empty($message->getMessageId())) {
            $message->setMessageId(Uuid::uuid4()->toString());
        }

        if (empty($message->getTimestamp())) {
            $message->setTimestamp(time());
        }

        if ($priority > 0) {
            $message->setPriority($priority);
        }

        if ($expire > 0) {
            $message->setExpiration($expire);
        }

        return $message;
    }

    protected function castMessage($message)
    {
        switch (true) {
            case $message instanceof AmqpMessage:
            case is_scalar($message) :
                break;
            case is_array($message):
            case $message instanceof JsonSerializable:
                $message = json_encode($message);
                break;
            default:
                throw new InvalidMessageTypeException("Message's type can only be scalar, array, JsonSerializable and AmqpMessage.");
        }

        return $this->getConnection()->createMessage($message);
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->app['connection'];
    }
}