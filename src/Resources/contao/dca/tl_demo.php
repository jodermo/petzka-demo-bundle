<?php


/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_demo'] = array(
  'config' => array
  (
    'dataContainer'               => 'Table',
    'enableVersioning'            => true
  ),

  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'flag'                    => 2,
      'fields'                  => array('title'),
      'headerFields'            => array('title','language','tstamp'),
      'panelLayout'             => 'filter;sort,search,limit',
      'child_record_class'      => 'no_padding'
    ),
    'global_operations' => array
    (
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
      ),
    ),
    'operations' => array
    (
      'edit' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_demo']['edit'],
        'href'                => 'table=tl_content',
        'icon'                => 'edit.svg'
      ),
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_demo']['editmeta'],
        'href'                => 'act=edit',
        'icon'                => 'header.svg'
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_demo']['copy'],
        'href'                => 'act=paste&amp;mode=copy',
        'icon'                => 'copy.svg'
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_demo']['cut'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.svg'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_demo']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.svg',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
      ),
      'toggle' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_demo']['toggle'],
        'icon'                => 'visible.svg',
        'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
        'button_callback'     => array('sioweb.dummy.dca.tl_demo', 'toggleIcon')
      ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_demo']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.svg'
      )
    )
  ),

  'palettes' => array
  (
    '__selector__'                => array('published'),
    'default'                     => '{title_legend},title,alias;{teaser_legend},description;{publish_legend},published',
  ),

  'subpalettes' => array
  (
    'published'           => 'start,stop'
  ),

  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_demo']['title'],
      'inputType'               => 'text',
      'exclude'                 => true,
      'filter'                  => true,
      'sorting'                 => true,
      'eval'                    => array('mandatory'=>true,'maxlength'=>255,'tl_class'=>'w50','gsIgnore'=>true),
    ),
    'alias' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_demo']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'search'                  => true,
      'eval'                    => array('rgxp'=>'alias','doNotCopy'=>true,'maxlength'=>128,'tl_class'=>'w50','gsIgnore'=>true),
      'save_callback' => array
      (
        array('sioweb.dummy.dca.tl_demo', 'generateAlias')
      )
    ),
    'description' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_demo']['description'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'textarea',
      'eval'                    => array('rte'=>'tinyMCE','style'=>'height: 50px;','tl_class'=>'clr long','gsIgnore'=>true),
    ),
    'published' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_demo']['published'],
      'exclude'                 => true,
      'filter'                  => true,
      'flag'                    => 1,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
    ),
    'start' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_demo']['start'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
    ),
    'stop' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_demo']['stop'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
    )
  )
);
