<?php

declare(strict_types=1);

namespace Pollen\Cookie\Middleware;

use Pollen\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Pollen\Routing\BaseMiddleware;
use Pollen\Cookie\CookieJar;

class CookieMiddleware extends BaseMiddleware
{
    /**
     * @inheritDoc
     */
    public function beforeSend(PsrResponse $response, RouterInterface $router): PsrResponse
    {
        if (!headers_sent() && ($cookies = CookieJar::fetchQueued())) {
            foreach ($cookies as $cookie) {
                $response = $response->withAddedHeader('Set-Cookie', (string)$cookie);
            }
        }

        return $router->beforeSendResponse($response);
    }
}