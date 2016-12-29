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
 * CometbyTheme Theme Ajax Handler
 *
 * Class ThemeAjaxHandler
 * @version   1.0
 * @package   CometbyTheme/Core
 * @category  Class
 * @author    Comet.by
 */
final class ThemeAjaxHandler
{
    use SingletonTrait;

    public static function getInstance(array $aConf)
    {
        if (self::$_inst === null) {
            self::$_inst = new self($aConf);
        }
        return self::$_inst;
    }

    private $aHandlers;
    private $prefix = '';

    private function __construct(array $aConf)
    {
        if (is_string($aConf['prefix'])) $this->setPrefix($aConf['prefix']);
        $this->aHandlers = [];
    }

    private function setPrefix($prefix)
    {
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $prefix)) return false;
        $this->prefix = $prefix;
    }

    /**
     * @return return special prefix
     */
    public function ajaxActionPrefix()
    {
        return $this->prefix;
    }

    /**
     * Adding one of several handlers for ajax hook. *Warning!* Usually ajax hook ends for die(), that's why have no sense create more than one hook for each and any unique name
     * @param string $name : name of the theme ajax hook, looks like hook_name for example
     * @param callable $handler : clousure, do it like this $o->addHandler('hook_name', function() { echo 'resp'; die(); });
     * @return null
     */
    public function addHandler($name, callable $handler)
    {
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name)) throw new \Exception(sprinf("Incorrect ajax action name '%s' given", $name));

        $this->aHandlers [] = [
            'name' => $name,
            'handler' => $handler,
        ];

        add_action("wp_ajax_" . $this->prefix . $name, [$this, $name]);
        add_action("wp_ajax_nopriv_" . $this->prefix . $name, [$this, $name]);
        return;
    }

    public function __call($name, $args = [])
    {
        foreach ($this->aHandlers as $aPair) {
            if ($aPair['name'] == $name) call_user_func($aPair['handler'], $args);
        }
    }
}