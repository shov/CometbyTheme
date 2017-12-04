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
 * Take some often-use method for class contains REST API methods
 * @package CometbyTheme
 * @category Trait
 */
trait RestUserTrait
{
    protected function addCallback($urlTail, callable $callback, $args = [], $methods = 'GET')
    {
        if(empty($urlTail)) throw new \InvalidArgumentException("Wrong url tail for REST api");

        add_action('rest_api_init', function () use ($urlTail, $callback, $args, $methods) {
            register_rest_route('macho/v2', $urlTail, [
                'methods' => $methods,
                'callback' => $callback,
                'args' => $args
            ]);
        });
    }
}