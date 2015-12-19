<?php
/**
 *
 */

namespace Bihan;

use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface for event listener providers.
 */
interface EventListenerProviderInterface
{
	public function subscribe(Container $app, EventDispatcherInterface $dispatcher);
}
