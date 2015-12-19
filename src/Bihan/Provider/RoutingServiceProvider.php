<?php
/**
 *
 */

namespace Bihan\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Bihan\EventListenerProviderInterface;
use Bihan\EventListener\RouterListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Symfony Routing component Provider.
 */
class RoutingServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app)
    {
    	$app['route_parser_class']     = 'FastRoute\\RouteParser\\Std';
        $app['route_generator_class']  = 'FastRoute\\DataGenerator\\GroupCountBased';
        $app['route_dispatcher_class'] = 'FastRoute\\Dispatcher\\GroupCountBased';
        $app['route_collector_class']  = 'FastRoute\\RouteCollector';

        $app['route_collector'] = function($app) {
        	return new $app['route_collector_class'](
        		new $app['route_parser_class'],
        		new $app['route_generator_class']
        	);
        };

        $app['route_dispatcher'] = function($app) {
        	return new $app['route_dispatcher_class'](
        		$app['route_collector']->getData()
        	);
        };

    	$app['router_listener'] = function ($app) {
            return new RouterListener($app['route_dispatcher']);
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['router_listener']);
    }
}