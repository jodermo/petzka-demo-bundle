<?php
/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_demo_data'] = array(

	'config' => array(
		'dataContainer' => 'Table',
		'ptable' => 'tl_demo',
		'ctable' => array('tl_article'),
		'switchToEdit' => true,
		'enableVersioning' => true,
		'onload_callback' => array(
			array('Petzka\DemoBundle\Demo', 'demoDataOnloadCallback'),
		),
		'sql' => array(
			'keys' => array(
				'id' => 'primary',
				'pid' => 'index',
			)
		),
	),

	'list' => array(
		'sorting' => array(
			'mode' => 4,
			'fields' => array('sorting'),
			'headerFields' => array('name'),
			'panelLayout' => 'limit',
			'header_callback' => array('Petzka\DemoBundle\Demo', 'headerCallback'),
			'child_record_callback' => array('Petzka\DemoBundle\Demo', 'listData'),
			'child_record_class' => 'no_padding',
		),
		'global_operations' => array(
			'all' => array(
				'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href' => 'act=select',
				'class' => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
			)
		),
		'operations' => array(
			'edit' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['edit'],
				'href' => 'table=tl_content',
				'icon' => 'edit.gif',
				'attributes' => 'class="contextmenu"',
				'button_callback' => array('Petzka\DemoBundle\Demo', 'editDataIcon'),
			),
			'editheader' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['editheader'],
				'href' => 'act=edit',
				'icon' => 'header.gif',
				'attributes' => 'class="edit-header"',
			),
			'copy' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['copy'],
				'href' => 'act=paste&amp;mode=copy',
				'icon' => 'copy.gif',
			),
			'cut' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['cut'],
				'href' => 'act=paste&amp;mode=cut',
				'icon' => 'cut.gif',
			),
			'delete' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['delete'],
				'href' => 'act=delete',
				'icon' => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['toggle'],
				'icon' => 'visible.gif',
				'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback' => array('Petzka\DemoBundle\Demo', 'toggleIcon'),
			),
			'show' => array(
				'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['show'],
				'href' => 'act=show',
				'icon' => 'show.gif',
			)
		)
	),

	'palettes' => array(
		'__selector__' => array('type'),
		'default' => '{title_legend},title,type',
		'date' => '{title_legend},title,type;{date_legend},date'
	),

	'fields' => array(
		'id' => array(
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		),
		'pid' => array(
			'foreignKey' => 'tl_demo.name',
			'sql' => "int(10) unsigned NOT NULL default '0'",
			'relation' => array('type' => 'belongsTo', 'load' => 'eager'),
		),
		'tstamp' => array(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		'sorting' => array(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		'title' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['title'],
			'exclude' => true,
			'search' => true,
			'flag' => 1,
			'inputType' => 'text',
			'eval' => array(
				'maxlength' => 255,
				'tl_class' => 'w50',
			),
			'sql' => "varchar(255) NOT NULL default ''",
		),
		'type' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['type'],
			'exclude' => true,
			'inputType' => 'select',
			'options' => array(
				'default',
				'date'
			),
			'reference' => &$GLOBALS['TL_LANG']['tl_demo_data']['types'],
			'eval' => array(
				'mandatory' => true,
				'includeBlankOption' => true,
				'submitOnChange' => true,
				'tl_class' => 'w50',
			),
			'sql' => "varchar(255) NOT NULL default ''",
		),
		'date' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_demo_data']['start'],
			'exclude' => true,
			'inputType' => 'text',
			'eval' => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql' => "varchar(10) NOT NULL default ''",
		)
	),

);
