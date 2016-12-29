# CometbyTheme
The class kit for fast modify Wordpress Themes

*php 5.6+*
<br>
*Wordpress 4+*

###Including example
In the *functions.php* just require *cometby.php*

`
require_once(get_template_directory() . '/cometby/cometby.php');
`

Than you can configure something

`use CometbyTheme\Theme;
 /* Theme config */
 
 Theme::getPlacement()->setPlacePropConf([
     'events_cat_slug' => 'sobyitiya',
     'publ_cat_slug'   => 'publikatsii',
     'inter_cat_slug'  => 'intervyu',
     'video_cat_slug'  => 'video',
     'okrug_page_id'   => 12,
 ]);
 
 Theme::getCustomizer()->initCustomKit([
     [
         'name'     => 'mainhome',
         'title'    => 'Основные / Главная',
         'desc'     => 'Настройки сайта и главной страницы',
         'sections' => [
             [
                 'name'     => 'header',
                 'title'    => 'Шапка',
                 'desc'     => 'Настройка шапки',
                 'settings' => [
                     [
                         'name'  => 'head-caption',
                         'label' => 'Заголовк в шапке',
                         'type'  => 'text',
                     ],
                     [
                         'name'  => 'head-text',
                         'label' => 'Текст в шапке',
                         'type'  => 'textarea',
                     ],
                 ],
             ],
             [
                 'name'     => 'footer',
                 'title'    => 'Подвал',
                 'desc'     => 'Настройка подвала',
                 'settings' => [
                     [
                         'name'  => 'copy-left-cap',
                         'label' => 'Правовой текст в подвале',
                         'type'  => 'textarea',
                     ],
                 ],
             ],
         ],
     ],
     [
         'name'     => 'contacts',
         'title'    => 'Контакты',
         'desc'     => 'Настройки контактов',
         'sections' => [
             [
                 'name'     => 'map',
                 'title'    => 'Карта',
                 'desc'     => 'Настройка карты',
                 'settings' => [
                     [
                         'name'  => 'baloon-head',
                         'label' => 'Заголовок балуна',
                         'type'  => 'text',
                     ],
                     [
                         'name'  => 'baloon-text',
                         'label' => 'Текст в балуне',
                         'type'  => 'textarea',
                     ],
                 ],
             ],
         ],
     ],
 ]);`