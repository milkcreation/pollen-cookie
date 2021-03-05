<?php

declare(strict_types=1);

namespace Pollen\Cookie;

use Pollen\Http\RequestInterface;
use Pollen\Support\Proxy\HttpRequestProxyInterface;

/**
 * @mixin \Symfony\Component\HttpFoundation\Cookie
 */
interface CookieInterface extends HttpRequestProxyInterface
{
    /**
     * Vérification de validité de la valeur dans la requête HTTP.
     *
     * @param RequestInterface|null $request
     * @param mixed|null $value
     *
     * @return bool
     */
    public function checkRequestValue(?RequestInterface $request = null, $value = null): bool;

    /**
     * Suppression du cookie.
     *
     * @return static
     */
    public function clear(): CookieInterface;

    /**
     * Decryptage de la valeur d'un cookie.
     *
     * @param string $hashedValue
     *
     * @return string
     */
    public function decrypt(string $hashedValue): string;

    /**
     * Encryptage de la valeur d'un cookie.
     *
     * @param string $plainValue
     *
     * @return string
     */
    public function encrypt(string $plainValue): string;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de la valeur du cookie dans la requête HTTP.
     *
     * @param RequestInterface|null $request
     *
     * @return mixed
     */
    public function httpValue(?RequestInterface $request = null);

    /**
     * Vérifie si la valeur du cookie est cryptée.
     *
     * @return bool
     */
    public function isEncrypted(): bool;

    /**
     * Vérifie si le cookie est en attente de traitement dans la réponse globale.
     *
     * @return bool
     */
    public function isQueued(): bool;

    /**
     * Mise en place d'une expiration persistante du cookie.
     *
     * @return static
     */
    public function never(): CookieInterface;

    /**
     * Ajoute le cookie à la file d'attente d'envoi dans une réponse HTTP.
     *
     * @return static
     */
    public function queue(): CookieInterface;

    /**
     * Retire le cookie de la file d'attente d'envoi dans une réponse HTTP.
     *
     * @return static
     */
    public function unqueue(): CookieInterface;
}