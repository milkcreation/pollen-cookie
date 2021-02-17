<?php

declare(strict_types=1);

namespace Pollen\Cookie;

use Psr\Container\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * @mixin \Pollen\Support\Concerns\ConfigBagTrait
 * @mixin \Pollen\Support\Concerns\ContainerAwareTrait
 */
interface CookieJarInterface
{
    /**
     * Suppression du cookie.
     *
     * @return static
     */
    public function clear(): CookieJarInterface;

    /**
     * Création de l'instance du cookie.
     *
     * @param array|null ...$args {
     *      @type string|array|null $value Valeur du cookie à définir.
     *      @type int $expire
     *      @type string|null $path
     *      @type string|null $domain
     *      @type boolean $secure
     *      @type boolean $httpOnly
     *      @type boolean $raw
     *      @type string|null $sameSite
     * }
     *
     * @return Cookie
     */
    public function create(?array ...$args): Cookie;

    /**
     * Récupération de la liste des cookies en attente de traitement dans la réponse globale.
     *
     * @return Cookie[]|array
     */
    public static function fetchQueued(): array;

    /**
     * Récupération de la valeur d'un cookie.
     *
     * @param string|null $key Clé d'indice de la valeur. La valeur doit être un tableau. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get(?string $key = null, $default = null);

    /**
     * Récupération de l'instance du conteneur d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Récupération du nom de qualification du domaine du site associé.
     *
     * @return string|null
     */
    public function getDomain(): ?string;

    /**
     * Récupération du nom de qualification d'un cookie.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération du chemin de validité des cookies.
     *
     * @return string
     */
    public function getPath(): ?string;

    /**
     * Vérifie si le cookie est en attente de traitement dans la réponse globale.
     *
     * @return bool
     */
    public function isQueued(): bool;

    /**
     * Implémentation d'un instance de cookie.
     *
     * @param string $alias Alias de qualification de l'instance.
     * @param string|array|null $attrs Nom de qualification lorsque celui diffère de l'alias|attributs de configuration.
     *
     * @return static
     */
    public function make(string $alias, $attrs = null): CookieJarInterface;

    /**
     * Définition du cookie.
     *
     * @param string|array|null $value Valeur du cookie à définir.
     * @param array|null ...$args {
     *      Liste dynamique d'arguments complémentaires de définition du cookie.
     *
     *      @var int $expire
     *      @var string|null $path
     *      @var string|null $domain
     *      @var boolean $secure
     *      @var boolean $httpOnly
     *      @var boolean $raw
     *      @var string|null $sameSite
     * }
     *
     * @return static
     */
    public function set($value = null, ?array ...$args): CookieJarInterface;

    /**
     * Définition de la liste des arguments par défaut.
     *
     * @param string|array|null $value
     * @param int $expire
     * @param string|null $path
     * @param string|null $domain
     * @param boolean|null $secure
     * @param boolean $httpOnly
     * @param boolean $raw
     * @param string|null $sameSite
     *
     * @return static
     */
    public function setArgs(
        $value = null,
        int $expire = 0,
        ?string $path = '/',
        ?string $domain = null,
        ?bool $secure = null,
        bool $httpOnly = true,
        bool $raw = false,
        ?string $sameSite = null
    ): CookieJarInterface;

    /**
     * Définition de l'activation de l'encodage en base64 de la valeurs des cookies.
     *
     * @param boolean $active
     *
     * @return static
     */
    public function setBase64(bool $active = false): CookieJarInterface;

    /**
     * Définition du nom de qualification du domaine du site associé.
     *
     * @param string|null $domain
     *
     * @return static
     */
    public function setDomain(?string $domain = null): CookieJarInterface;

    /**
     * Définition du nom de qualification du cookie.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): CookieJarInterface;

    /**
     * Définition du chemin de validité des cookies.
     *
     * @param string|null $path
     *
     * @return static
     */
    public function setPath(?string $path = null): CookieJarInterface;

    /**
     * Définition de la mise en file du cookie pour un traitement dans la réponse globale.
     *
     * @param bool $queued
     *
     * @return static
     */
    public function setQueued(bool $queued = true): CookieJarInterface;

    /**
     * Définition du suffixe de Salage du nom de qualification des cookies.
     *
     * @param string $salt
     *
     * @return static
     */
    public function setSalt(string $salt = ''): CookieJarInterface;
}
