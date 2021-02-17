<?php

declare(strict_types=1);

namespace Pollen\Cookie;

use Pollen\Container\BaseServiceProvider;

class CookieServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        CookieJarInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            CookieJarInterface::class,
            function () {
                return (new CookieJar(
                    [
                        'value'    => null,
                        'expire'   => 3600,
                        'path'     => null,
                        'domain'   => null,
                        'secure'   => null,
                        'httpOnly' => true,
                        'raw'      => false,
                        'sameSite' => null,
                    ], $this->getContainer()
                ));
            }
        );
    }
}
