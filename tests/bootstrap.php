<?php

define('KIRBY_TESTING', true);

require_once __DIR__ . '/../vendor/autoload.php';

// Disable Kirby's Whoops error handler during testing
// to prevent PHPUnit risky test warnings about unremoved handlers
\Kirby\Cms\App::$enableWhoops = false;
