<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = str_replace
(
    'alias',
    'alias,demo_field',
    $GLOBALS['TL_DCA']['tl_article']['palettes']['default']
);


$GLOBALS['TL_DCA']['tl_article']['fields']['demo_field'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_member']['demo_field'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>8),
    'sql'       => "varchar(8) NOT NULL default ''"
);


