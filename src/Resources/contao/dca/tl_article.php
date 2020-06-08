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
    'author',
    'author,article_custom_value',
    $GLOBALS['TL_DCA']['tl_article']['palettes']['default']
);

// Hinzufügen der Feld-Konfiguration
$GLOBALS['TL_DCA']['tl_article']['fields']['article_custom_value'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['article_custom_value'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>8),
    'sql'       => "varchar(8) NOT NULL default ''"
);
