<?php

/**
 * Liste des routes générées avec la méthode `Router::web`.
 */
return [
    ['method' => 'GET', 'url' => '', 'action' => 'index'],
    ['method' => 'GET', 'url' => '/{id:\d+}/{action:[a-zA-Z-]+}'],
    ['method' => 'GET', 'url' => '/{action:[a-zA-Z-]+}'],
    ['method' => 'GET', 'url' => '/{id:\d+}', 'action' => 'detail'],
    ['method' => 'POST', 'url' => '/{action:[a-zA-Z-]+}'],
    ['method' => 'POST', 'url' => '/{id:\d+}/{action:[a-zA-Z-]+}'],
];
