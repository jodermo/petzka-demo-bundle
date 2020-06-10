<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */


$GLOBALS['TL_MODELS']['tl_demo'] = 'Petzka\DemoBundle\Model\DemoModel';
$GLOBALS['TL_MODELS']['tl_demo_data'] = 'Petzka\DemoBundle\Model\DemoDataModel';

// backend module

$GLOBALS['BE_MOD']['demo_bundle']['demo'] = array(
	'tables' => array(
		'tl_demo',
		'tl_demo_data'
	),
	'icon' => 'bundles/petzkademo/img/icon.png',
);



// frontend module

array_insert($GLOBALS['FE_MOD'], 2, array(
	'demo_bundle' => array(
	    'demo_module' => 'Petzka\DemoBundle\Module\Demo',
		'mod_demoModule' => 'Petzka\DemoBundle\FrontentModule\PetzkaDemoModule',
	)
));

// content elements

array_insert($GLOBALS['TL_CTE'], 2, array(
	'demo_bundle' => array(
	    'demo_module' => 'Petzka\DemoBundle\Module\Demo',
        'demo_element' => 'Petzka\DemoBundle\ContentElement\PetzkaDemoContentElement',
    )
));

// frontend widgets

array_insert($GLOBALS['TL_FFL'], 2, array(
	'demo_value' => 'Petzka\DemoBundle\Widget\PetzkaDemoFrontendWidget',
));


// backend widgets

array_insert($GLOBALS['BE_FFL'], 2, array(
	'demo_value' => 'Petzka\DemoBundle\Widget\PetzkaDemoBackendWidget',
));

