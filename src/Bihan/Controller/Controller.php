<?php
/**
 *
 */

namespace Bihan\Controller;

use Bihan\Application;

/**
 * Base controller class that provide Application inheritance.
 */
abstract class Controller
{
    protected $app;

    /**
     * Constructor.
     *
     * @param Application $app An Application instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
