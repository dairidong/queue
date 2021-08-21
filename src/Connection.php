<?php


namespace SimpleAmqp;


use Enqueue\AmqpLib\AmqpContext;
use Interop\Amqp\AmqpDestination;
use Interop\Amqp\Impl\AmqpQueue;
use Interop\Amqp\Impl\AmqpTopic;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class Connection
{

    /**
     * @var AmqpContext
     */
    protected $context;

    /**
     * @var array
     */
    protected $default_flags = [
        AmqpDestination::FLAG_DURABLE,// 持久化
    ];

    /**
     * @var array
     */
    protected $exchange_types = [
        AMQPExchangeType::DIRECT,
        AMQPExchangeType::FANOUT,
        AMQPExchangeType::HEADERS,
        AMQPExchangeType::TOPIC
    ];

    /**
     * @var \Interop\Amqp\Impl\AmqpQueue[]
     */
    protected $queues = [];

    /**
     * @var \Interop\Amqp\Impl\AmqpTopic[]
     */
    protected $exchanges = [];

    /**
     * Connection constructor.
     * @param AmqpContext $context
     */
    public function __construct(AmqpContext $context)
    {
        $this->context = $context;
    }

    public function getQueue(string $queue_name)
    {
        if (!array_key_exists($queue_name, $this->queues)) {
            $this->createQueue($queue_name);
        }
        return $this->queues[$queue_name];
    }

    public function getExchange(string $exchange_name)
    {
        if (!array_key_exists($exchange_name, $this->exchanges)) {
            $this->createExchange($exchange_name);
        }
        return $this->exchanges[$exchange_name];
    }

    /**
     * @param string $queue_name
     * @param array $flags
     * @return \Interop\Amqp\Impl\AmqpQueue
     */
    public function createQueue(string $queue_name, array $flags = []): AmqpQueue
    {
        $queue = $this->context->createQueue($queue_name);
        $queue->setArgument('x-max-priority', 10);

        $flags = array_merge($flags, $this->default_flags);
        foreach ($flags as $flag) {
            $queue->addFlag($flag);
        }

        $this->context->declareQueue($queue);
        $this->queues[$queue_name] = $queue;
        return $queue;
    }

    /**
     * 创建交换机对象
     * @param string $exchange_name
     * @param array $flags
     * @return AmqpTopic
     */
    public function createExchange(string $exchange_name, array $flags = []): AmqpTopic
    {
        $exchange = $this->context->createTopic($exchange_name);

        $flags = array_merge($flags, $this->default_flags);
        foreach ($flags as $flag) {
            $exchange->addFlag($flag);
        }

        $this->context->declareTopic($exchange);
        $this->exchanges[$exchange_name] = $exchange;
        return $exchange;
    }

    public function createMessage(string $message)
    {
        return $this->context->createMessage($message);
    }

    public function createProducer()
    {
        return $this->context->createProducer();
    }

}