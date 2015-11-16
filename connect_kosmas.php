<?php
    require_once 'config_kosmas.php';
    require_once 'FileMaker.php';

    $kosmas = new FileMaker();
    $kosmas->setProperty('database', DATABASE);
    $kosmas->setProperty('hostspec', HOSTSPEC);
    $kosmas->setProperty('username', USERNAME);
    $kosmas->setProperty('password', PASSWORD);
?>