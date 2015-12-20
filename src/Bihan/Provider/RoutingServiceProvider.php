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

        $app['router.cache_enabled'] = false;
        $app['router.cache_file']    = '';

        $app['route_collector'] = function($app) {
        	return new $app['route_collector_class'](
        		new $app['route_parser_class'],
        		new $app['route_generator_class']
        	);
        };

        $app['route_dispatcher'] = function($app) {
            if ($app['router.cache_enabled'] && file_exists($app['router.cache_file'])) {
                $dispatchData = require $app['router.cache_file'];
                if (is_array($dispatchData)) {
                    return new $app['route_dispatcher_class']($dispatchData);
                }
            }

            $dispatchData = $app['route_collector']->getData();

            if ($app['router.cache_enabled'] && !empty($app['router.cache_file'])) {
                file_put_contents(
                    $app['router.cache_file'],
                    '<?php return ' . var_export($dispatchData, true) . ';'
                );
            }

            return new $app['route_dispatcher_class']($dispatchData);
        };

    	$app['router.listener'] = function ($app) {
            return new RouterListener($app['route_dispatcher']);
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['router.listener']);
    }
}