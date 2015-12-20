<?php

namespace Bihan\Tests;

use Bihan\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Application test cases.
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function testGetRequest()
	{
		$request = Request::create('/');

		$app = new Application();

		$app->match('GET', '/', function(Request $req) use ($request) {
			$response = $request === $req ? 'ok' : 'ko';
			return new Response($response);
		});

		$this->assertEquals('ok', $app->handle($request)->getContent());
	}

	/**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
	public function testNotFoundRouteShouldThrowNotFoundHttpException()
	{
		$app = new Application();

		$app->match('GET', '/', function() {
			return new Response('bar');
		});

		$app->handle(Request::create('/foo'));
	}

	/**
     * @expectedException Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
	public function testNotAllowedMethodForRouteShouldThrowMethodNotAllowedHttpException()
	{
		$app = new Application();

		$app->match('POST', '/', function() {
			return new Response('bar');
		});

		$app->handle(Request::create('/'));
	}
}