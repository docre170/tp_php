<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';

session_destroy();
session_start();
set_flash('success', 'Vous etes deconnecte.');
redirect_to('auth/login.php');

