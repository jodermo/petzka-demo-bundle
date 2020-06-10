<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */


namespace Petzka\DemoBundle;


class Demo extends \Backend
{
/**
	 * Return the "toggle visibility" button
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(\Input::get('tid'))) {
			$this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1));
			if (\Environment::get('isAjaxRequest')) {
				exit;
			}
			$this->redirect($this->getReferer());
		}

		$href .= '&amp;id=' . \Input::get('id') . '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

		if (! $row['published']) {
			$icon = 'invisible.gif';
		}

		return '<a href="' . $this->addToUrl($href) . '" title="' . \StringUtil::specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
	}

	    /**
    	 * Return the "edit slider" button
    	 */
    	public function editIcon($row, $href, $label, $title, $icon, $attributes)
    	{
    		if ($row['type'] !== 'default') {
    			return '';
    		}
    		$href .= '&amp;id=' . $row['id'];
    		return '<a href="' . $this->addToUrl($href) . '" title="' . \StringUtil::specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
    	}

    	/**
    	 * Return the "edit slide" button
    	 */
    	public function editDataIcon($row, $href, $label, $title, $icon, $attributes)
    	{
    		if ($row['type'] !== 'default') {
    			return '';
    		}
    		$href .= '&amp;id=' . $row['id'];
    		return '<a href="' . $this->addToUrl($href) . '" title="' . \StringUtil::specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
    	}

    	/**
        	 * Add the type of input field
        	 *
        	 * @return string
        	 */
        	public function listData($arrRow)
        	{
        		return '<div class="tl_content_left">' . $arrRow['title'] . ' <span style="color:#999;padding-left:3px">' . $GLOBALS['TL_LANG']['tl_demo_data']['types'][$arrRow['type']] . '</span></div>';
        	}

    /**
    	 * On load callback for tl_demo_data
    	 *
    	 * @param \DataContainer $dc
    	 * @return void
    	 */
    	public function demoDataOnloadCallback($dc)
    	{

    	}

	/**
	 * DCA Header callback
	 *
	 * Redirects to the parent data if type is not "default"
	 *
	 * @param  array          $headerFields label value pairs of header fields
	 * @param  \DataContainer $dc           data container
	 * @return array
	 */
	public function headerCallback($headerFields, $dc)
	{
		$sliderData = $this->Database
			->prepare('SELECT * FROM ' . $GLOBALS['TL_DCA'][$dc->table]['config']['ptable'] . ' WHERE id = ?')
			->limit(1)
			->execute(CURRENT_ID);

		if ($sliderData->numRows && $sliderData->type !== 'default') {
			$this->redirect('contao/main.php?do=demo&act=edit&id=' . CURRENT_ID . '&ref=' . \Input::get('ref') . '&rt=' . REQUEST_TOKEN);
		}

		return $headerFields;
	}

    /**
	 * DCA Header callback
	 *
	 * Redirects to the parent slide if type is not "default"
	 *
	 * @param  array          $headerFields label value pairs of header fields
	 * @param  \DataContainer $dc           data container
	 * @return array
	 */
	public function headerCallbackContent($headerFields, $dc)
	{
		$slideData = $this->Database
			->prepare('SELECT * FROM ' . $GLOBALS['TL_DCA'][$dc->table]['config']['ptable'] . ' WHERE id = ?')
			->limit(1)
			->execute(CURRENT_ID);

		if ($slideData->numRows && $slideData->type !== 'default') {
			$this->redirect('contao/main.php?do=demo&table=tl_demo_data&act=edit&id=' . CURRENT_ID . '&ref=' . \Input::get('ref') . '&rt=' . REQUEST_TOKEN);
		}

		return $headerFields;
	}

	/**
	 * Get all demos and return them as array
	 *
	 * @return array
	 */

	public function getDemoIds()
	{
		$arrResult = array();
		$objResult = $this->Database->execute("SELECT id, name FROM tl_demo ORDER BY name");

		while ($objResult->next()) {
			$arrResult[$objResult->id] = $objResult->name;
		}

		return $arrResult;
	}
}
