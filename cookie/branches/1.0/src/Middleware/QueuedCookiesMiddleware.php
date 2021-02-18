<?php

declare(strict_types=1);

namespace Pollen\Cookie\Middleware;

use Pollen\Cookie\CookieJarInterface;
use Pollen\Routing\BaseMiddleware;
use Pollen\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;

class QueuedCookiesMiddleware extends BaseMiddleware
{
    /**
     * @var CookieJarInterface $cookieJar
     */
    protected $cookieJar;

    /**
     * @param CookieJarInterface $cookieJar
     */
    public function __construct(CookieJarInterface $cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }

    /**
     * @inheritDoc
     */
    public function beforeSend(PsrResponse $response, RouterInterface $router): PsrResponse
    {
        if (!headers_sent() && ($cookies = $this->cookieJar->fetchQueued())) {
            foreach ($cookies as $cookie) {
                $response = $response->withAddedHeader('Set-Cookie', (string)$cookie);
            }
        }

        return $router->beforeSendResponse($response);
    }
}