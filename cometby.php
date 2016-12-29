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

/**
 * Autoloader SPL
 */
require_once('autoloader.php');

Theme::init();