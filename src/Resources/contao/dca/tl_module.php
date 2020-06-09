<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */


$GLOBALS['TL_DCA']['tl_module']['fields']['demo_field'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['demo_field'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];
