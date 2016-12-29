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
 * Class ThemeCustomizer
 * @version   1.0
 * @package   CometbyTheme/Core
 * @category  Class
 * @author    Comet.by
 */
final class ThemeCustomizer
{
    use SingletonTrait;

    private $customKit;
    private $aPanNameBook;
    private $aSecNameBook;
    private $aSetNameBook;

    /**
     * @param array $kit should be in *format:*
     *  [
     *    [
     *      'name' => 'unique panel name',
     *      'desc',
     *      'title',
     *      <'priority',>
     *      'sections, => [
     *        [
     *          'name' => 'unique section name',
     *          'desc',
     *          'title',
     *          <'priority',>
     *          'settings' => [
     *            [
     *              'name' => 'unique setting name',
     *              'label' => ,
     *               <'default',>
     *               <'desc',>
     *               <'priority',>
     *               'type' => 'text'|'textarea'|'pickimg',
     *             ], [ another setting ],
     *          ],
     *        ],
     *        [ another section],
     *      ],
     *    ],
     *    [ another panel ],
     *  ]
     * @return null
     */
    public function initCustomKit($kit = [])
    {
        if (!is_array($kit)) throw new \InvalidArgumentException(sprintf("Custom kit should be an array %s given", $kit));
        if (empty($kit)) return null;
        $this->customKit = $kit;
        $self = $this;
        add_action('customize_register', function ($oCustomizer) use ($self) {
            $self->cometThemeCustomizer($oCustomizer);
        });
    }

    /**
     * @param \WP_Customize_Manager $oCustomizer
     */
    private function cometThemeCustomizer(\WP_Customize_Manager $oCustomizer)
    {
        $oCustomizer->remove_section('static_front_page'); //removing default homepage section

        if (empty($this->customKit)) throw new \UnexpectedValueException(sprintf("Custom kit should be not empty"));
        if (!is_array($this->aPanNameBook)) $this->aPanNameBook = [];
        if (!is_array($this->aSecNameBook)) $this->aSecNameBook = [];
        if (!is_array($this->aSetNameBook)) $this->aSetNameBook = [];

        foreach ($this->customKit as $panel => $aPanel) {
            if (empty($aPanel['name'])) throw new \UnexpectedValueException(sprintf("Panel name should be not empty, '%s' given", $aPanel['name']));
            if (in_array($aPanel['name'], $this->aPanNameBook)) throw new \UnexpectedValueException(sprintf("Panel name to register given %s, already has registered", $aPanel['name']));
            $this->aPanNameBook[] = $aPanel['name'];

            $panPriority = 1;
            if (!empty($aPanel['priority'])) $panPriority = $aPanel['priority'];

            assert(!empty($aPanel['title']));
            assert(!empty($aPanel['desc']));

            $oCustomizer->add_panel($aPanel['name'], [
                'title' => $aPanel['title'],
                'description' => $aPanel['desc'],
                'priority' => $panPriority,
            ]);

            if (!is_array($aPanel['sections'])) throw new \UnexpectedValueException(sprintf("['sections'] should be an array, %s given", $aPanel['sections']));
            foreach ($aPanel['sections'] as $aSection) {
                if (empty($aSection['name'])) throw new \UnexpectedValueException(sprintf("Section name should be not empty, '%s' given", $aSection['name']));
                if (in_array($aSection['name'], $this->aSecNameBook)) throw new \UnexpectedValueException(sprintf("Section name to register given %s, already has registered", $aSection['name']));
                $this->aSecNameBook[] = $aSection['name'];

                $secPriority = 1;
                if (!empty($aSection['priority'])) $secPriority = $aSection['priority'];

                assert(!empty($aSection['title']));
                assert(!empty($aSection['desc']));

                $oCustomizer->add_section($aSection['name'], [
                    'title' => $aSection['title'],
                    'description' => $aSection['desc'],
                    'priority' => $secPriority,
                ]);
                $oCustomizer->get_section($aSection['name'])->panel = $aPanel['name'];

                if (!is_array($aSection['settings'])) throw new \UnexpectedValueException(sprintf("['settings'] should be an array, %s given", $aSection['settings']));
                foreach ($aSection['settings'] as $aSetting) {
                    if (empty($aSetting['name'])) throw new \UnexpectedValueException(sprintf("Setting name should be not empty, '%s' given", $aSetting['name']));
                    if (in_array($aSetting['name'], $this->aSetNameBook)) throw new \UnexpectedValueException(sprintf("Setting name to register given %s, already has registered", $aSetting['name']));
                    $this->aSetNameBook[] = $aSetting['name'];

                    $setPriority = 1;
                    if (!empty($aSetting['priority'])) $setPriority = $aSetting['priority'];

                    $setDefault = '';
                    if (!empty($aSetting['default'])) $setDefault = $aSetting['default'];

                    $setDesc = '';
                    if (!empty($aSetting['desc'])) $setDesc = $aSetting['desc'];

                    assert(!empty($aSetting['label']));
                    assert(!empty($aSetting['section']));
                    assert(!empty($aSetting['priority']));
                    assert(!empty($aSetting['type']));

                    switch ($aSetting['type']) {
                        case('pickimg'):
                            $oCustomizer->add_setting($aSetting['name'], array(
                                'default' => $setDefault,
                            ));
                            $oCustomizer->add_control(new \WP_Customize_Image_Control($oCustomizer, $aSetting['name'], array(
                                'label' => $aSetting['label'],
                                'default' => $setDefault,
                                'description' => $setDesc,
                                'section' => $aSection['name'],
                                'settings' => $aSetting['name'],
                                'priority' => $setPriority,

                            )));
                            break;
                        case('text'):
                        case('textarea'):
                            $oCustomizer->add_setting($aSetting['name'], [
                                'default' => $setDefault,
                            ]);
                            $oCustomizer->add_control($aSetting['name'], [
                                'label' => $aSetting['label'],
                                'default' => $setDefault,
                                'description' => $setDesc,
                                'section' => $aSection['name'],
                                'priority' => $setPriority,
                                'type' => $aSetting['type'],
                            ]);
                            break;
                        default:
                            if (isset($aSetting['callback']) && is_callable($aSetting['callback'])) {
                                $aSettingCopy = $aSetting;
                                $aSettingCopy['callback'] = null;
                                call_user_func($aSetting['callback'], [$aPanel, $aSection, $aSettingCopy]);
                            } else {
                                throw new \UnexpectedValueException(sprintf("Unexcepted setting type given %s", $aSetting['type']));
                            }
                            break;
                    }
                }
            }
        }
    }

    /**
     * @param string $name
     * @param mixed $def
     * @return mixed
     */
    public function getSetting($name, $def = '')
    {
        $name = (string)$name;
        assert(!empty($name));

        if (!is_array($this->aSetNameBook)) $this->aSetNameBook = [];
        return get_theme_mod($name, $def);
    }
}