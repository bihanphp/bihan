<?php
/**
 *
 */

namespace Bihan;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Bihan\Provider\HttpKernelServiceProvider;
use Bihan\Provider\RoutingServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class Application extends Container implements HttpKernelInterface, TerminableInterface
{
    const VERSION = '0.1';

    protected $providers = [];
    protected $booted = false;

    /**
     * Instantiate a new Application.
     *
     * @param array $values Parameters or objects.
     */
    public function __construct(array $values = array())
    {
        parent::__construct();

        $this['debug']   = false;
        $this['charset'] = 'UTF-8';
        $this['logger']  = null;
        
        $this->register(new HttpKernelServiceProvider());
        $this->register(new RoutingServiceProvider());

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance.
     * @param array                    $values   An array of values that customizes the provider.
     *
     * @return Application
     */
    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        $this->providers[] = $provider;

        parent::register($provider, $values);

        return $this;
    }

    /**
     * Boots all service providers.
     *
     * This method is automatically called by handle(), but you can use it
     * to boot all service providers when not handling a request.
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;
        
        foreach ($this->providers as $provider) {
            if ($provider instanceof EventListenerProviderInterface) {
                $provider->subscribe($this, $this['dispatcher']);
            }

            if ($provider instanceof BootableProviderInterface) {
                $provider->boot($this);
            }
        }
    }

    /**
     * Adds a route to the collection.
     *
     * @param string|string[] $method   Either a string or an array of valid HTTP method
     * @param string          $pattern  The route pattern
     * @param mixed           $callback Either a controller string or a callable
     */

    public function match($method, $pattern, $callback)
    {
        $methods = is_array($method) ? $method : explode('|', $method);

        $this['route_collector']->addRoute($methods, $pattern, $callback);
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $callback  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function on($eventName, $callback, $priority = 0)
    {
        if ($this->booted) {
            $this['dispatcher']->addListener($eventName, $callback, $priority);

            return;
        }

        $this->extend('dispatcher', function (EventDispatcherInterface $dispatcher, $app) use ($callback, $priority, $eventName) {
            $dispatcher->addListener($eventName, $callback, $priority);

            return $dispatcher;
        });
    }

    /**
     * Handles the request and delivers the response.
     *
     * @param Request|null $request Request to process
     */
    public function run(Request $request = null)
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $response = $this->handle($request);
        $response->send();
        $this->terminate($request, $response);
    }

    /**
     * {@inheritdoc}
     *
     * If you call this method directly instead of run(), you must call the
     * terminate() method yourself if you want the finish filters to be run.
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (!$this->booted) {
            $this->boot();
        }

        return $this['kernel']->handle($request, $type, $catch);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $this['kernel']->terminate($request, $response);
    }
}
