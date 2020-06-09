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
	 * Get all demos and return them as array
	 *
	 * @return array
	 */

	public function getDemoIds()
	{
		$arrResult = array();
		$objResult = $this->Database->execute("SELECT id, title FROM tl_demo ORDER BY title");

		while ($objResult->next()) {
			$arrResult[$objResult->id] = $objResult->title;
		}

		return $arrResult;
	}
}
