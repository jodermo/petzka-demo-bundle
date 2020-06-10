<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

$strName = 'tl_form_field';

$GLOBALS['TL_DCA'][$strName]['palettes']['demo_value'] = $GLOBALS['TL_DCA'][$strName]['palettes']['select'];
$GLOBALS['TL_DCA'][$strName]['palettes']['demo_value'].= ';{demo_legend},is_demo;';

$GLOBALS['TL_DCA'][$strName]['fields']['is_demo'] = array
(
    'label'                   => &$GLOBALS['TL_LANG'][$strName]['is_demo'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);
