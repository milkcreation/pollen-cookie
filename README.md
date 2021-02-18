# Pollen Cookie Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/cookie/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen **Cookie** Component.

## Installation

```bash
composer require pollen-solutions/cookie
```

## Basic Usage

```php
use Pollen\Cookie\CookieJar;

// CookieJar instantiation
$cookieJar = new CookieJar();

// CookieJar hydratation
$cookieJar->make('cookie.test.1', ['value' => 'test1']);
$cookieJar->make(
    'cookie.test.2',
    [
        'name'      => 'cookie_test_2',
        'salt'      => '_' . md5('cookie.test.2.salt'),
        'value'     => [
            'test2-value1',
            'test2-value2'
        ],
        'encrypted' => true,
        'prefix'    => 'testprefix_',
        'lifetime'  => 3600,
        //'path'      => 'site-base_path',
        //'domain'    => 'site-domaine.ltd',
        'secure'    => false,
        'httpOnly'  => false,
        'raw'       => true,
        'sameSite'  => 'strict',
    ]
);

/** Get Cookie */
$cookie1 = $cookieJar->get('cookie.test.1');
$cookie2 = $cookieJar->get('cookie.test.2');

var_dump((string)$cookie1);
var_dump((string)$cookie2);
exit;
/**/
```

## Send Cookie

### PSR

```php
use Laminas\Diactoros\Response;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Cookie\CookieJar;

// CookieJar instantiation
$cookieJar = new CookieJar();

// CookieJar hydratation
$cookie = $cookieJar->make('cookie.test', ['value' => 'test']);

$response = new Response();
$response = $response->withAddedHeader('Set-Cookie', (string)$cookie);
(new SapiEmitter())->emit($response);
exit;
```

### Pollen Http (works on Symfony)

```php
use Pollen\Cookie\CookieJar;
use Pollen\Http\Response as HttpResponse;

// CookieJar instantiation
$cookieJar = new CookieJar();

// CookieJar hydratation
$cookie = $cookieJar->make('cookie.test', ['value' => 'test']);

$httpResponse = new HttpResponse();
$httpResponse->headers->setCookie($cookie);
$httpResponse->send();
```

## Get HTTP Request cookie value

```php
use Pollen\Cookie\CookieInterface;
use Pollen\Http\Request as HttpRequest;

// In this example, we consider :
// - Global $cookieJar as an implemented instance of CookieJar.
// - $cookie1 and $cookie2 as declared cookies in CookieJar
// - An HTTP Response was already sent with $cookie1 and $cookie2.

global $cookieJar;

/** @var CookieInterface $cookie1 */
$cookie1 = $cookieJar->get('cookie1');

/** @var CookieInterface $cookie2 */
$cookie1 = $cookieJar->get('cookie1');

$httpValue1 = $cookie1->httpValue();
$httpValue2 = $cookie2->httpValue(HttpRequest::createFromGlobals());

var_dump($httpValue1, $httpValue2);
```

## Queued Cookie and QueuedCookiesMiddleware (Pollen Routing example)

```php
use Pollen\Cookie\CookieJar;
use Pollen\Cookie\Middleware\QueuedCookiesMiddleware;
use Pollen\Http\Request;
use Pollen\Http\Response;
use Pollen\Http\ResponseInterface;
use Pollen\Routing\Router;

// Create the Request object
$request = Request::createFromGlobals();

// CookieJar instantiation
$cookieJar = (new CookieJar())->setDefaults($request->getBasePath());

// CookieJar hydratation
$cookie = $cookieJar->make('cookie.test', ['value' => 'test2'])->queue();

// Router instantiation
$router = new Router();

// Setting QueuedCookiesMiddleware
$router->middleware(new QueuedCookiesMiddleware($cookieJar));

// Map a route
$router->map('GET', '/', function (): ResponseInterface {
    return new Response('<h1>Hello, World!</h1>');
});

// Catch HTTP Response
$response = $router->handleRequest($request);

// Send the response to the browser
$router->sendResponse($response);

// Trigger the terminate event
$router->terminateEvent($request, $response);
```
