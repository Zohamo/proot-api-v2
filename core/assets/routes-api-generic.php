<?php

/**
 * Liste des routes générées avec la méthode `Router::api`.
 */
$id = "/{id:\d+}";
return [
    ['method' => 'POST', 'url' => '', 'action' => 'create'],
    ['method' => 'GET', 'url' => '', 'action' => 'getAll'],
    ['method' => 'PUT', 'url' => '', 'action' => 'bulkUpdate'],
    ['method' => 'DELETE', 'url' => '', 'action' => 'deleteAll'],
    ['method' => 'GET', 'url' => $id, 'action' => 'get'],
    ['method' => 'PUT', 'url' => $id, 'action' => 'update'],
    ['method' => 'DELETE', 'url' => $id, 'action' => 'delete'],
];
