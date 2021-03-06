<?php

declare(strict_types=1);

namespace Pollen\Cookie;

use DateTimeInterface;
use InvalidArgumentException;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;

class CookieJar implements CookieJarInterface
{
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * Instances des cookies déclarées.
     * @var CookieInterface[]|array
     */
    protected $cookies = [];

    /**
     * Durée de vie d'un cookie.
     * @var int|string|DateTimeInterface
     */
    protected $lifetime = 0;

    /**
     * Préfixe de qualification des valeur de cookie.
     * @var string|null
     */
    protected $prefix;

    /**
     * Suffixe de salage du nom de qualification du cookie.
     * @var string|null
     */
    protected $salt;

    /**
     * Nom de qualification du domaine.
     * @var string|null
     */
    public $domain;

    /**
     * Limitation de l'accessibilité du cookie au protocole HTTP.
     * @var bool
     */
    public $httpOnly = true;

    /**
     * Chemin relatif de validation.
     * @var string|null
     */
    public $path;

    /**
     * Indicateur d'activation de l'encodage d'url lors de l'envoi du cookie.
     * @var bool
     */
    public $raw = false;

    /**
     * Directive de permission d'envoi du cookie.
     * @see https://developer.mozilla.org/fr/docs/Web/HTTP/Headers/Set-Cookie
     * @var string|null strict|lax
     */
    public $sameSite;

    /**
     * Indicateur d'activation du protocole sécurisé HTTPS.
     * @var bool|null
     */
    public $secure;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): CookieJarInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function add(CookieInterface $cookie): CookieJarInterface
    {
        $this->cookies[$cookie->getAlias()] = $cookie;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function fetchQueued(): array
    {
        $queued = [];
        foreach ($this->cookies as $cookie) {
            if ($cookie->isQueued()) {
                $queued[] = $cookie;
                $cookie->unqueue();
            }
        }
        return $queued;
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): ?CookieInterface
    {
        return $this->cookies[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getAvailability($lifetime = null): int
    {
        if ($lifetime === null) {
            $lifetime = $this->lifetime;
        }

        if (!is_int($lifetime) && !is_string($lifetime) && !$lifetime instanceof DateTimeInterface) {
            throw new RuntimeException(
                'Unable to determine cookie availability, must require an int type or type string or DateTimeInterface instance for expiration value'
            );
        }

        if (!is_numeric($lifetime)) {
            $lifetime = strtotime($lifetime);

            if (false === $lifetime) {
                throw new InvalidArgumentException(
                    'Unable to determine cookie availability, textual datetime could not parsed into a Unix timestamp'
                );
            }
        }

        if ($lifetime === 0) {
            return 0;
        }

        return $lifetime instanceof DateTimeInterface ? $lifetime->getTimestamp() : time() + $lifetime;
    }

    /**
     * @inheritDoc
     */
    public function getDefaults(
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $httpOnly = null,
        ?bool $raw = null,
        ?string $sameSite = null
    ): array {
        return [
            $path ?: $this->path,
            $domain ?: $this->domain,
            $secure ?: $this->secure,
            filter_var($httpOnly ?? $this->httpOnly, FILTER_VALIDATE_BOOLEAN),
            filter_var($raw ?? $this->raw, FILTER_VALIDATE_BOOLEAN),
            $sameSite ?: $this->sameSite,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function make(string $alias, array $args = []): CookieInterface
    {
        return $this->cookies[$alias] = new Cookie($alias, $args, $this);
    }

    /**
     * @inheritDoc
     */
    public function setDefaults(
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $httpOnly = null,
        ?bool $raw = null,
        ?string $sameSite = null
    ): CookieJarInterface {
        [$this->path, $this->domain, $this->secure, $this->httpOnly, $this->raw, $this->sameSite] = [
            $path,
            $domain,
            $secure,
            filter_var($httpOnly ?? true, FILTER_VALIDATE_BOOLEAN),
            filter_var($raw ?? false, FILTER_VALIDATE_BOOLEAN),
            $sameSite,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLifetime($lifetime): CookieJarInterface
    {
        if (!is_int($lifetime) && !is_string($lifetime) && !$lifetime instanceof DateTimeInterface) {
            $lifetime = 0;
        }

        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSalt(string $salt): CookieJarInterface
    {
        $this->salt = $salt;

        return $this;
    }
}