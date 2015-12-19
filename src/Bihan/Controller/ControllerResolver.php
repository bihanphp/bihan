<?php
/**
 *
 */

namespace Bihan\Controller;

use Bihan\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request;

/**
 * Adds Application as a valid argument for controllers.
 */
class ControllerResolver extends BaseControllerResolver
{
    protected $app;

    /**
     * Constructor.
     *
     * @param Application     $app    An Application instance
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(Application $app, LoggerInterface $logger = null)
    {
        $this->app = $app;

        parent::__construct($logger);
    }

    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        foreach ($parameters as $param) {
            if ($param->getClass() && $param->getClass()->isInstance($this->app)) {
                $request->attributes->set($param->getName(), $this->app);

                break;
            }
        }

        return parent::doGetArguments($request, $controller, $parameters);
    }
}
