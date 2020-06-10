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
    'author;',
    'author;{demo_legend},demo_id;',
    $GLOBALS['TL_DCA']['tl_article']['palettes']['default']
);


$GLOBALS['TL_DCA']['tl_article']['fields']['demo_id'] = array(
	'label' => &$GLOBALS['TL_LANG']['tl_module']['demo_id'],
	'exclude' => true,
	'inputType' => 'select',
	'options_callback' => array('Petzka\\DemoBundle\\Demo', 'getDemoIds'),
	'eval' => array(
		'includeBlankOption' => true,
		'mandatory' => true,
	),
	'sql' => "int(10) unsigned NOT NULL default '0'",
);

