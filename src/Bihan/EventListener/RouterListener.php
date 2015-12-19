<?php
/**
 *
 */

namespace Bihan\EventListener;

use FastRoute\Dispatcher;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Initializes the context from the request and sets request attributes based on a matching route.
 */
class RouterListener implements EventSubscriberInterface
{
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param Dispatcher           $dispatcher A Dispatcher instance
     * @param LoggerInterface|null $logger     The logger
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }

        $method   = $request->getMethod();
        $pathInfo = $request->getPathInfo();

        $routeInfo = $this->dispatcher->dispatch($method, $pathInfo);

        switch ($routeInfo[0])
        {
            case Dispatcher::NOT_FOUND:
                $message = sprintf('No route found for "%s %s"', $method, $pathInfo);

                if ($referer = $request->headers->get('referer')) {
                    $message .= sprintf(' (from "%s")', $referer);
                }

                throw new NotFoundHttpException($message, $e);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];

                $message = sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $method, $pathInfo, implode(', ', $allowedMethods));

                throw new MethodNotAllowedHttpException($allowedMethods, $message, $e);
                break;
    
            case Dispatcher::FOUND:
                $controller = $routeInfo[1];
                $parameters = $routeInfo[2];

                $request->attributes->add(array_merge(
                    ['_controller' => $controller],
                    $parameters
                ));
                break;
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 32)),
        );
    }
}
