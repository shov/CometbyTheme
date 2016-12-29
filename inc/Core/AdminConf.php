<?php

/*
 * This file is part of the CometbyTheme package.
 *
 * (c) Alexandr Shevchenko [comet.by] alexandr@comet.by
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace CometbyTheme\Core;

use CometbyTheme\SingletonTrait;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AdminConf
 * Contains several high important procedures and methods
 * @package  CometbyTheme/Core
 * @category Class
 * @author   Comet.by
 */
class AdminConf
{
    use SingletonTrait;

    const REPAIR_MODE = false; //For block access to all pages, just set it as true

    /**
     * @return object instance
     */
    public static function init()
    {
        return self::getInstance();
    }

    /**
     * AdminConf constructor.
     */
    private function __construct()
    {
        $this->templateIncluding();
        $this->wpAdminLogo();
    }

    /**
     * @return null
     */
    private function templateIncluding()
    {
        add_action('template_include', function ($originTpl) {
            $admin = $this->checkIsLoggedAdmin();
            if (self::REPAIR_MODE && !$admin) {
                return get_template_directory() . '/stub.php';
            }
            return $originTpl;
        });
        return;
    }

    /**
     * @return bool
     */
    private function checkIsLoggedAdmin()
    {
        if (!is_user_logged_in()) return false;
        $current_user = wp_get_current_user();
        if (user_can($current_user, 'administrator')) {
            return true;
        }
        return false;
    }

    private function wpAdminLogo()
    {
        add_action('login_head', function () {
            ?>
            <style type="text/css">
                h1 a {
                    background-image: url(<?=get_bloginfo(' template_directory ')?>/img/manual_logo.png) !important;
                    background-size: 150px 150px !important;
                    width: 150px !important;
                    height: 150px !important;
                }
            </style>
            <?
        });
    }
}