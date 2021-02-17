<?php

return [
    // Liste des attributs de configuration par défaut des cookies (facultatif).
    // Valeur par défaut des cookies.
    'value'    => null,
    // Délai d'expiration par défaut des cookies.
    'expire'   => 0,
    // Chemin relatif de validaté par défaut des cookies.
    'path'     => null,
    // Nom de qualification par défaut du domaine des cookies.
    'domain'   => null,
    // Indicateur d'activation par défaut du protocole sécurisé HTTPS des cookies.
    'secure'   => null,
    // Limitation de l'accessibilité par défaut des cookies au protocole HTTP.
    'httpOnly' => true,
    // Indicateur d'activation de l'encodage d'url par défaut lors de l'envoi des cookies.
    'raw'      => false,
    // Directive de permission d'envoi par défaut des cookies.
    // @see https://developer.mozilla.org/fr/docs/Web/HTTP/Headers/Set-Cookie
    'sameSite' => null,
    // Activation par défaut de l'encodage en base64 de la valeur des cookies.
    'base64'   => false,
    // Préfixe de salage par défaut du nom de qualification des cookies.
    'salt'     => '',

    // Exemples de déclaration de cookies.
    'cookies' => [
        // exemple #1 L'alias et le nom de qualification du cookie sont identiques.
        'sample3',

        // exemple #2 Le nom du cookie différe de l'alias de qualification.
        'sample2' => 'only_particular_cookie_name',

        // exemple#3 Définition d'arguments particulier.
        'sample3' => [
            'name'   => 'particular_cookie_name',
            'base64' => true,
            'path'   => '/'
        ]
    ]
];