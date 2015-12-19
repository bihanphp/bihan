<?php
/**
 *
 */

namespace Bihan;

/**
 * Interface that must implement all Silex service providers.
 */
interface BootableProviderInterface
{
    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app);
}