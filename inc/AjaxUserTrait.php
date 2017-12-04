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
 * Take some often-use method for class contains ajax methods
 * @package CometbyTheme
 * @category Trait
 */
trait AjaxUserTrait
{
    /**
     * @param string $debug
     * @param bool $soft
     * @return array
     */
    protected function ajaxFail($debug = '', $soft = false)
    {
        $resp = ['hasError' => true,];

        if (!empty($debug)) {
            $resp = $resp + ['debug' => $debug,];
        }

        if ($soft) {
            return $resp;
        } else {
            echo json_encode($resp);
            die();
        }
    }

    /**
     * @param string $debug
     * @return array
     */
    protected function ajaxSoftFail($debug = '')
    {
        return $this->ajaxFail($debug, true);
    }

    /**
     * @param array $data
     * @param bool $soft
     * @return array
     */
    protected function ajaxSuccess($data = [], $soft = false)
    {
        $resp = ['hasError' => false,];
        if (!empty($data) && is_array($data)) {
            $resp = $resp + $data;
        }

        if ($soft) {
            return $resp;
        } else {
            echo json_encode($resp);
            die();
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function ajaxSoftSuccess($data = [])
    {
        return $this->ajaxSuccess($data, true);
    }

    /**
     * @param array $data
     * @param array|string $args
     * @param callable|null $callback
     * @param callable|null $fail
     * @return mixed|bool If ok, return true
     */
    protected function ajaxRequiredArgs($data, $args = [], callable $callback = null, callable $fail = null)
    {
        if(is_null($fail)) $fail = [$this, 'ajaxFail'];

        if (!is_array($data)) return $fail();
        if (empty($data)) return $fail();

        if (is_string($args) && !empty($args)) $args = [$args,]; //just one arg is req

        assert(is_array($args));
        if (!is_array($args)) return null;
        if (empty($args)) return null;

        foreach ($args as $argNameForArrays => $arg) {
            if (is_array($arg)) {
                $deepData = $data[$argNameForArrays];
                if (!isset($deepData)) return $fail();
                if (is_array($deepData)) {
                    if (empty($deepData)) {
                        continue;
                    }
                } else {
                    $deepData = [$deepData,];
                }
                if(true !== $this->ajaxRequiredArgs($deepData, $arg, $callback, $fail)) return $fail();
                continue;
            }

            if (!isset($data[$arg])) return $fail();

            $defaultCallbackNoEmpty = function ($val) use ($fail) {
                if (!is_numeric($val) && !is_bool($val)) {
                    if (empty($val)) return $fail();
                }
            };

            if (is_null($callback)) $callback = $defaultCallbackNoEmpty;
            if (false === $callback($data[$arg])) return $fail();
        }
        return true;
    }

    /**
     * Add name-hook pair as handler for ajax call
     * You can use both soft|regular terminators, but for best capability and scalability may be better way is use SOFT terminators
     * @param $name
     * @param callable $hook
     */
    protected function addAjaxHandler($name, callable $hook)
    {
        $ajaxHandler = Theme::getAjaxHandler();
        $ajaxHandler->addHandler($name, $hook);
    }
}