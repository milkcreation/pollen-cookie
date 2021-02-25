<?php

declare(strict_types=1);

namespace Pollen\Cookie;

use DateTimeInterface;
use Symfony\Component\HttpFoundation\Cookie as BaseCookie;

/**
 * @mixin \Pollen\Support\Concerns\ConfigBagAwareTrait
 * @mixin \Pollen\Support\Concerns\ContainerAwareTrait
 */
interface CookieJarInterface
{
    /**
     * Ajout dune instance de cookie.
     *
     * @param Cookie $cookie
     *
     * @return static
     */
    public function add(Cookie $cookie): CookieJarInterface;

    /**
     * Récupération des instances déclarées.
     *
     * @return CookieJarInterface[]|BaseCookie[]|array
     */
    public function all(): array;

    /**
     * Récupération de la liste des cookies en attente de traitement dans la réponse globale.
     *
     * @return Cookie[]|BaseCookie[]|array
     */
    public function fetchQueued(): array;

    /**
     * Récupération d'une instance déclarée selon son alias.
     *
     * @param string $alias
     *
     * @return CookieInterface|BaseCookie|null
     */
    public function get(string $alias): ?CookieInterface;

    /**
     * Récupération de la durée de disponibilité d'un cookie.
     *
     * @param int|string|DateTimeInterface|null
     *
     * @return int
     */
    public function getAvailability($lifetime = null): int;

    /**
     * Récupération des paramètres de cookie par défaut.
     *
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool|null $httpOnly
     * @param bool|null $raw
     * @param string|null $sameSite
     *
     * @return array
     */
    public function getDefaults(
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $httpOnly = null,
        ?bool $raw = null,
        ?string $sameSite = null
    ): array;

    /**
     * Récupération du suffixe de salage du nom de qualification du cookie.
     *
     * @return string|null
     */
    public function getSalt(): ?string;

    /**
     * Implémentation d'un instance de la classe.
     *
     * @param string $alias
     * @param array $args.
     *
     * @return CookieInterface|BaseCookie
     */
    public function make(string $alias, array $args = []): CookieInterface;

    /**
     * Définition des paramètres de cookie par défaut.
     *
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool|null $httpOnly
     * @param bool|null $raw
     * @param string|null $sameSite
     *
     * @return static
     */
    public function setDefaults(
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $httpOnly = null,
        ?bool $raw = null,
        ?string $sameSite = null
    ): CookieJarInterface;

    /**
     * Définition de la durée de vie d'un cookie.
     *
     * @param int|string|DateTimeInterface
     *
     * @return static
     */
    public function setLifetime($lifetime): CookieJarInterface;

    /**
     * Définition du suffixe de salage du nom de qualification du cookie.
     *
     * @param string $salt
     *
     * @return static
     */
    public function setSalt(string $salt): CookieJarInterface;
}
