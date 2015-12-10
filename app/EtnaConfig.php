<?php

namespace TestLibUtils;

use Silex\Application;
use Silex\ServiceProviderInterface;

use ETNA\Silex\Provider\Config as ETNAConf;
use ETNA\Silex\Provider\SPrinter\SPrinterServiceProvider;

class EtnaConfig implements ServiceProviderInterface
{
    private $rabbitmq_config;

    public function __construct()
    {
        $this->rabbitmq_config = [
            "exchanges" => [
                "SPrinter" => [
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
                    "passive"     => false,
                    "durable"     => true,
                    "exclusive"   => false,
                    "auto_delete" => false,
                    "exchange"    => "default",
                    "routing.key" => "email",
                    "channel"     => "default",
                ],
                "sprinter.lefran_f" => [
                    "passive"     => false,
                    "durable"     => true,
                    "exclusive"   => false,
                    "auto_delete" => false,
                    "exchange"    => "SPrinter",
                    "routing.key" => "",
                    "channel"     => "default",
                ],
                "sprinter.norris_c" => [
                    "passive"     => false,
                    "durable"     => true,
                    "exclusive"   => false,
                    "auto_delete" => false,
                    "exchange"    => "SPrinter",
                    "routing.key" => "",
                    "channel"     => "default",
                ]
            ]
        ];
    }

    /**
     *
     * @{inherit doc}
     */
    public function register(Application $app)
    {
        $app->register(new RabbitMQConf($this->rabbitmq_config));

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
