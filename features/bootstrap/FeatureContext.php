<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\HttpFoundation\Request;

use ETNA\Auth\Services\AuthCheckingService;
use ETNA\Auth\Services\AuthCookieService;

use ETNA\FeatureContext\BaseContext;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends BaseContext
{
    /**
     * @BeforeScenario @Sprinter
     */
    public function createSprinterQueue()
    {
        $channel = $this->getContainer()->get('old_sound_rabbit_mq.SPrinter_producer')->getChannel();
        $channel->exchange_declare('SPrinter', 'direct', false, true, false);

        $channel->queue_declare('sprinter.blu', false, true, false, false);
        $channel->queue_declare('sprinter.lefran_f', false, true, false, false);

        $channel->queue_bind('sprinter.blu', 'SPrinter', 'sprinter.blu');
        $channel->queue_bind('sprinter.lefran_f', 'SPrinter', 'sprinter.lefran_f');
    }
}
