<?php

/** @var \App\Entities\User $auth */ ?>

<nav class="navbar navbar-dark bg-dark shadow" role="navigation">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= url() ?>">
            <i class="fa-solid fa-scroll me-2" aria-hidden="true"></i>
            <span><?= env("APP_TITLE") ?></span>
        </a>
        <ul class="navbar-nav gap-4 d-none d-md-flex flex-row">
            <li class="nav-item">
                <a class="nav-link <?= activeLink('accueil') ?>" href="<?= url() ?>" data-test="link-accueil">
                    <i class="fa-solid fa-home me-1" aria-hidden="true"></i> Accueil
                </a>
            </li>
        </ul>
        <div>
            <button class="btn bg-transparent border-0 p-1 me-3" data-bs-toggle="modal" data-bs-target="#modalMainSearch" data-test="open-search-modal" data-bs-hover="tooltip" title="Recherche (ctrl+k)">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="navbarOffcanvasLgLabel">
                <div class="offcanvas-header">
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="main-menu navbar-nav mx-auto gap-1 d-md-none">
                        <li class="nav-item">
                            <a class="nav-link <?= activeLink('accueil') ?>" href="<?= url() ?>" data-test="link-home">
                                <i class="fa-solid fa-fw fa-home me-1" aria-hidden="true"></i> Accueil
                            </a>
                        </li>
                    </ul>
                    <?php if (isset($auth)) : ?>
                        <?php if ($auth->isAdmin()) : ?>
                            <!-- Administration -->
                            <ul class="navbar-nav mt-4 gap-1" data-test="nav-administration">
                                <li class="nav-item nav-item-title text-primary mb-2">
                                    <i class="fa-solid fa-fw fa-sliders me-1" aria-hidden="true"></i> Administration
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= activeLink('users') ?>" href="<?= url() ?>/users" data-test="link-users">
                                        <i class="fa-solid fa-fw fa-users-cog me-1" aria-hidden="true"></i> Gestion des utilisateurs
                                    </a>
                                </li>
                            </ul>
                        <?php endif ?>
                        <?php if ($auth->isDev()) : ?>
                            <!-- Administration technique -->
                            <ul class="navbar-nav mt-4 gap-1" data-test="nav-administration-technique">
                                <li class="nav-item nav-item-title text-primary mb-2">
                                    <i class="fa-solid fa-fw fa-screwdriver-wrench me-1" aria-hidden="true"></i> Administration technique
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= activeLink('logs') ?>" href="<?= url() ?>/tools/logs" data-test="link-logs">
                                        <i class="fa-solid fa-fw fa-triangle-exclamation me-1" aria-hidden="true"></i> Journal d'erreurs
                                    </a>
                                </li>
                            </ul>
                        <?php endif ?>
                        <!-- Profil -->
                        <ul class="navbar-nav mt-4 gap-1" data-test="nav-profil">
                            <li class="nav-item text-primary">
                                <i class="fa-solid fa-user-circle me-1" aria-hidden="true"></i> <strong data-test="user-name"><span class="text-uppercase"><?= $auth->usr_lastname ?></span> <?= $auth->usr_firstname ?></strong><br />
                                <em data-test="user-email"><?= $auth->usr_email ?></em><br />
                                <small data-test="user-role"><?= $auth->role->rol_label ?></small>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url() ?>/signout" class="nav-link">
                                    <i class="fa-solid fa-sign-out-alt me-1" aria-hidden="true"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<?php include "modal-search.php" ?>
