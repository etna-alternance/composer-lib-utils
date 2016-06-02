<?php

namespace TestLibUtils;

use Silex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

use ETNA\Silex\Provider\Config as ETNAConf;
use ETNA\Silex\Provider\SPrinter\SPrinterServiceProvider;
use ETNA\Silex\Provider\SPrinter\SPrinter;
use ETNA\Silex\Provider\RabbitMQ\RabbitConfig;

class EtnaConfig implements ServiceProviderInterface
{
    private $rabbitmq_config;

    public function __construct()
    {
        $this->rabbitmq_config = [
            "exchanges" => [
                "default" => [
                    "name"        => "etna",
                    "channel"     => "default",
                    "type"        => "direct",
                    "passive"     => false,
                    "durable"     => true,
                    "exclusive"   => false,
                    "auto_delete" => false,
                ],
            ],
            "queues" => [
                "email" => [
                    "name"        => "email",
                    "passive"     => false,
                    "durable"     => true,
                    "exclusive"   => false,
                    "auto_delete" => false,
                    "exchange"    => "etna",
                    "routing.key" => "email",
                    "channel"     => "default",
                ],
                "sprinter.lefran_f" => [
                    "name"        => "sprinter.lefran_f",
                    "passive"     => false,
                    "durable"     => true,
                    "exclusive"   => false,
                    "auto_delete" => false,
                    "exchange"    => "SPrinter",
                    "routing.key" => "sprinter.lefran_f",
                    "channel"     => "default",
                ],
                "sprinter.norris_c" => [
                    "name"        => "sprinter.norris_c",
                    "passive"     => false,
                    "durable"     => true,
                    "exclusive"   => false,
                    "auto_delete" => false,
                    "exchange"    => "SPrinter",
                    "routing.key" => "sprinter.norris_c",
                    "channel"     => "default",
                ]
            ]
        ];
    }

    /**
     *
     * @{inherit doc}
     */
    public function register(Container $app)
    {
        $app["rmq_producers"] = [
            'email' => [
                'connection'        => 'default',
                'exchange_options'  => $this->rabbitmq_config['exchanges']['default'],
                'queue_options'     => ['name' => 'email', 'routing_keys' => ['email']]
            ]
        ];

        $app["rmq_producers"] = array_merge($app["rmq_producers"], SPrinter::getProducerConfig());

        $app->register(new RabbitConfig($this->rabbitmq_config));
        $app->register(new SPrinterServiceProvider());
    }

    /**
     *
     * @{inherit doc}
     */
    public function boot(Application $app)
    {
        return $app;
    }
}
