<?php

/*
 * This file is part of the CometbyTheme package.
 *
 * (c) Alexandr Shevchenko [comet.by] alexandr@comet.by
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CometbyTheme;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

spl_autoload_register(function ($name) {
    $nameParts = explode('\\', $name);
    $nameParts = array_slice($nameParts, 1);
    $baseIncPath = __DIR__ . DIRECTORY_SEPARATOR . 'inc';
    $fullPath = $baseIncPath;

    foreach ($nameParts as $key => $part) {
        $fullPath .= DIRECTORY_SEPARATOR . $part;
    }
    $fullPath .= '.php';

    if (is_readable($fullPath)) require $fullPath;
}, true, true);