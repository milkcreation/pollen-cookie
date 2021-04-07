<?php

declare(strict_types=1);

namespace Pollen\Cookie;

use Exception;
use Pollen\Encryption\Encrypter;
use Pollen\Http\RequestInterface;
use Pollen\Validation\Validator as v;
use Pollen\Support\Proxy\HttpRequestProxy;
use Symfony\Component\HttpFoundation\Cookie as BaseCookie;
use RuntimeException;

class Cookie extends BaseCookie implements CookieInterface
{
    use HttpRequestProxy;

    /**
     * Alias de qualification de l'instance.
     * @var string
     */
    protected $alias = '';

    /**
     * Instance de CookieJar.
     * @var CookieJarInterface
     */
    protected $cookieJar;

    /**
     * Indicateur d'encryptage
     * @var bool
     */
    protected $encrypted = false;

    /**
     * Indicateur de mise en file du cookie en vue de son traitement dans la requête globale.
     * @var bool
     */
    protected $queued = false;

    /**
     * @var string|null
     */
    protected $prefix;

    /**
     * @param string $alias
     * @param array $args
     * @param CookieJarInterface $cookieJar
     */
    public function __construct(string $alias, array $args, CookieJarInterface $cookieJar)
    {
        $this->alias = $alias;
        $this->cookieJar = $cookieJar;

        $name = $args['name'] ?? $alias;
        $salt = $args['salt'] ?? $this->cookieJar->getSalt();
        if (is_string($salt)) {
            $name .= $salt;
        }
        $name = str_replace('.', '_', $name);

        $value = $args['value'] ?? null;

        if ($value !== null && !is_string($value)) {
            try {
                $value = json_encode($value, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                throw new RuntimeException('Cookie could not encode the value in JSON');
            }
        }

        $this->encrypted = filter_var($args['encrypted'] ?? $this->encrypted, FILTER_VALIDATE_BOOLEAN);
        if ($value !== null && $this->encrypted) {
            $value = $this->encrypt($value);
        }

        $this->prefix = $args['prefix'] ?? false;
        if ($value !== null && $this->prefix) {
            if (!is_string($args['prefix'])) {
                throw new RuntimeException('Cookie could not prefix cookie value');
            }
            $value = $this->prefix . $value;
        }

        $expire = $cookieJar->getAvailability($args['lifetime'] ?? null);

        [$path, $domain, $secure, $httpOnly, $raw, $sameSite] = $this->cookieJar->getDefaults(
            $args['path'] ?? null,
            $args['domain'] ?? null,
            $args['secure'] ?? null,
            $args['httpOnly'] ?? null,
            $args['raw'] ?? null,
            $args['sameSite'] ?? null
        );

        parent::__construct($name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * @inheritDoc
     */
    public function checkRequestValue(?RequestInterface $request = null, $value = null): bool
    {
        $httpValue = $this->httpValue($request);

        if ($value === null) {
            $value = $this->getValue();
        }

        return $httpValue !== null && $value === $httpValue;
    }

    /**
     * @inheritDoc
     */
    public function clear(): CookieInterface
    {
        return $this->withValue(null)->withExpires(time() - (60 * 60 * 24 * 365 * 5));
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $hashedValue): string
    {
        return (new Encrypter(substr(hash('sha256', $this->alias), 0, 16)))->decrypt($hashedValue);
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $plainValue): string
    {
        return (new Encrypter(substr(hash('sha256', $this->alias), 0, 16)))->encrypt($plainValue);
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function httpValue(?RequestInterface $request = null)
    {
        if ($request === null) {
            $request = $this->httpRequest();
        }

        if (!$value = $request->cookies->get($this->getName())) {
            return null;
        }

        if (!$this->isRaw()) {
            $value = rawurldecode($value);
        }

        $value = $this->prefix ? substr($value, strlen($this->prefix)) : $value;

        if ($this->isEncrypted()) {
            $value = $this->decrypt($value);
        }

        if (!is_numeric($value) && v::json()->validate($value)) {
            try {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                throw new RuntimeException('Cookie could not decode the value from JSON');
            }
        }

        return $value;
    }
    
    /**
     * @inheritDoc
     */
    public function isEncrypted(): bool
    {
        return $this->encrypted;
    }

    /**
     * @inheritDoc
     */
    public function isQueued(): bool
    {
        return $this->queued;
    }

    /**
     * @inheritDoc
     */
    public function never(): CookieInterface
    {
        return $this->withExpires(time() + (60 * 60 * 24 * 365 * 5));
    }

    /**
     * @inheritDoc
     */
    public function queue(): CookieInterface
    {
        $this->queued = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unqueue(): CookieInterface
    {
        $this->queued = false;

        return $this;
    }
}