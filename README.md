# Bihan

Bihan means "small" in [Breton language](https://en.wikipedia.org/wiki/Breton_language).
It started as a personnal project to improve my knowledge on building framework using Symfony.
So I decided to use main HTTP [Symfony](https://github.com/symfony) components, [Pimple](https://github.com/silexphp/Pimple) dependencies container and
[fast-route](https://github.com/nikic/FastRoute) router to implement a fast and lightweight framework.
Bihan is very inspired from [Silex](https://github.com/silexphp/Silex) and is perfect to implement small rest API.

I recommend you to read the very good article about fast-route and
[fast request routing using regular expressions](https://www.npopov.com/2014/02/18/Fast-request-routing-using-regular-expressions.html).

## Installation

```
$ composer require bihan/bihan
```

## Usage

```php
<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\JsonResponse;

$app = new Bihan\Application();

$app->match('GET', '/', function () {
  return new JsonResponse(['code' => 'OK']);
});

$app->run();
```

## Tests

```
$ composer install
$ phpunit
```
