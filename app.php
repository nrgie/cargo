#!/usr/bin/php5
<?php

require_once(__DIR__ . '/lib/database.php');

$db = new database();

require_once(__DIR__ . '/lib/entities.php');
require_once(__DIR__ . '/lib/users.php');


if (file_exists(__DIR__ . '/lib/install.php') && include_once(__DIR__ . '/lib/install.php')) {
    rename (__DIR__ . '/lib/install.php', __DIR__ . '/lib/install.executed');
    echo "Installing data into the database was successful!";
}

