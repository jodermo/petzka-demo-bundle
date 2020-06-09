<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */


$GLOBALS['TL_MODELS']['tl_demo'] = 'Petzka\DemoBundle\Model\DemoModel';

$GLOBALS['BE_MOD']['demo_bundle']['demo'] = array(
	'tables' => array(
		'tl_demo'
	),
	'icon' => 'bundles/petzkademo/img/icon.png',
);

array_insert($GLOBALS['BE_FFL'], 2, array(
	'demo_widget' => 'Petzka\DemoBundle\Widget\PetzkaDemoWidget',
));

array_insert($GLOBALS['FE_MOD'], 2, array(
	'demo_bundle' => array(
		'demo_module' => 'Petzka\DemoBundle\Module\PetzkaDemoModule',
	)
));

array_insert($GLOBALS['TL_CTE'], 2, array(
	'demo_bundle' => array(
        'demo_element' => 'Petzka\DemoBundle\ContentElement\PetzkaDemoContentElement',
    )
));
