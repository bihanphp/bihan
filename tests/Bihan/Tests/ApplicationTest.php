<?php

namespace Bihan\Tests;

use Bihan\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
}