<?php
/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\Module;

use Petzka\DemoBundle\Model\DemoModel;
use Petzka\DemoBundle\Model\DemoDataModel;

class Demo extends \Module
{
	/**
	 * @var string Template
	 */
	protected $strTemplate = 'demo_module';

	/**
	 * @return string
	 */
	public function generate()
	{
		// Display a wildcard in the back end
		if (TL_MODE === 'BE') {
			$template = new \BackendTemplate('be_wildcard');

			$template->wildcard = '### Demo Bundle ###';
			$template->title = $this->name;
			$template->id = $this->id;
			$template->link = $this->name;
			$template->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			if ($this->objModel->rsts_id && ($demo = DemoModel::findByPk($this->objModel->rsts_id)) !== null) {
				$template->id = $demo->id;
				$template->link = $demo->name;
				$template->href = 'contao/main.php?do=demo_bundle&amp;table=tl_demo_data&amp;id=' . $demo->id;
			}

			return $template->parse();
		}

		$this->files = \FilesModel::findMultipleByUuids($this->multiSRC);

		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;

		$images = array();

		if ($this->files) {

			$files = $this->files;
			$filesExpaned = array();

			// Get all images
			while ($files->next()) {
				if ($files->type === 'file') {
					$filesExpaned[] = $files->current();
				}
				else {
					$subFiles = \FilesModel::findByPid($files->uuid);
					while ($subFiles && $subFiles->next()) {
						if ($subFiles->type === 'file'){
							$filesExpaned[] = $subFiles->current();
						}
					}
				}
			}

			foreach ($filesExpaned as $files) {

				// Continue if the files has been processed or does not exist
				if (isset($images[$files->path]) || ! file_exists(TL_ROOT . '/' . $files->path)) {
					continue;
				}

				$file = new \File($files->path, true);

				if (!$file->isGdImage && !$file->isImage) {
					continue;
				}

				$arrMeta = $this->getMetaData($files->meta, $objPage->language);

				// Add the image
				$images[$files->path] = array
				(
					'id'        => $files->id,
					'uuid'      => isset($files->uuid) ? $files->uuid : null,
					'name'      => $file->basename,
					'singleSRC' => $files->path,
					'alt'       => $arrMeta['alt'],
					'title'     => $arrMeta['title'],
					'imageUrl'  => $arrMeta['link'],
					'caption'   => $arrMeta['caption'],
				);

			}

			if ($this->orderSRC) {
				// Turn the order string into an array and remove all values
				$order = deserialize($this->orderSRC);
				if (!$order || !is_array($order)) {
					$order = array();
				}
				$order = array_flip($order);
				$order = array_map(function(){}, $order);

				// Move the matching elements to their position in $order
				foreach ($images as $k => $v) {
					if (array_key_exists($v['uuid'], $order)) {
						$order[$v['uuid']] = $v;
						unset($images[$k]);
					}
				}

				$order = array_merge($order, array_values($images));

				// Remove empty (unreplaced) entries
				$images = array_filter($order);
				unset($order);
			}

			$images = array_values($images);

			foreach ($images as $key => $image) {
				$newImage = new \stdClass();
				$image['size'] = isset($this->imgSize) ? $this->imgSize : $this->size;
				$this->addImageToTemplate($newImage, $image, null, null, \FilesModel::findByPk($image['id']));
				if ($this->rsts_navType === 'thumbs') {
					$newImage->thumb = new \stdClass;
					$image['size'] = $this->rsts_thumbs_imgSize;
					$this->addImageToTemplate($newImage->thumb, $image);
				}
				$images[$key] = $newImage;
			}

		}

		$this->Template->images = $images;


		// $assetsDir = 'bundles/petzkademobundle';

		// $GLOBALS['TL_JAVASCRIPT'][] = $assetsDir . '/js/rocksolid-slider.min.js|static';
		// $GLOBALS['TL_CSS'][] = $assetsDir . '/css/rocksolid-slider.min.css||static';

	}

	/**
    	 * Parse demo data
    	 *
    	 * @param  \Model\Collection $objData demo data retrieved from the database
    	 * @return array                        parsed demo data
    	 */
    	protected function parseDemoData($objDemoDataArr)
    	{
    		global $objPage;

    		$data = array();
    		$pids = array();
    		$idIndexes = array();

    		if (! $objDemoDataArr) {
    			return $data;
    		}

    		while ($objDemoDataArr->next()) {

    			$entry = $objDemoDataArr->row();
    			$entry['text'] = '';

    			if ($entry['type'] === 'content') {
    				$pids[] = $entry['id'];
    				$idIndexes[(int)$entry['id']] = count($data);
    			}

    			if (
    				in_array($entry['type'], array('image', 'video')) &&
    				trim($entry['singleSRC']) &&
    				($file = \FilesModel::findByUuid($entry['singleSRC'])) &&
    				($fileObject = new \File($file->path, true)) &&
    				($fileObject->isGdImage || $fileObject->isImage)
    			) {
    				$meta = $this->getMetaData($file->meta, $objPage->language);
    				$entry['image'] = new \stdClass;
    				$this->addImageToTemplate($entry['image'], array(
    					'id' => $file->id,
    					'name' => $fileObject->basename,
    					'singleSRC' => $file->path,
    					'alt' => $meta['alt'],
    					'title' => $meta['title'],
    					'imageUrl' => $meta['link'],
    					'caption' => $meta['caption'],
    					'size' => isset($this->imgSize) ? $this->imgSize : $this->size,
    				));
    			}

    			if ($entry['type'] === 'video' && $entry['videoURL'] && empty($entry['image'])) {
    				$entry['image'] = new \stdClass;
    				if (preg_match(
    					'(^
    						https?://  # http or https
    						(?:
    							www\\.youtube\\.com/(?:watch\\?v=|v/|embed/)  # Different URL formats
    							| youtu\\.be/  # Short YouTube domain
    						)
    						([0-9a-z_\\-]{11})  # YouTube ID
    						(?:$|&|/)  # End or separator
    					)ix',
    					html_entity_decode($entry['videoURL']), $matches)
    				) {
    					$video = $matches[1];
    					$entry['image']->src = '//img.youtube.com/vi/' . $video . '/0.jpg';
    				}
    				else {
    					// Grey dummy image
    					$entry['image']->src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAJCAMAAAAM9FwAAAAAA1BMVEXGxsbd/8BlAAAAFUlEQVR42s3BAQEAAACAkP6vdiO6AgCZAAG/wrlvAAAAAElFTkSuQmCC';
    				}
    				$entry['image']->imgSize = '';
    				$entry['image']->alt = '';
    				$entry['image']->title = '';
    				$entry['image']->picture = array(
    					'img' => array('src' => $entry['image']->src, 'srcset' => $entry['image']->src),
    					'sources' => array(),
    				);
    			}

    			if ($entry['type'] !== 'video' && $entry['videoURL']) {
    				$entry['videoURL'] = '';
    			}

    			if ($entry['type'] === 'video' && $entry['videos']) {
    				$videoFiles = deserialize($entry['videos'], true);
    				$videoFiles = \FilesModel::findMultipleByUuids($videoFiles);
    				$videos = array();
    				foreach ($videoFiles as $file) {
    					$videos[] = $file;
    				}
    				$entry['videos'] = $videos;
    			}
    			else {
    				$entry['videos'] = null;
    			}

    			$data[] = $entry;

    		}

    		if (count($pids)) {
    			$entryContents = ContentModel::findPublishedByPidsAndTable($pids, DemoDataModel::getTable());
    			if ($entryContents) {
    				while ($entryContents->next()) {
    					$data[$idIndexes[(int)$entryContents->pid]]['text'] .= $this->getContentElement($entryContents->current());
    				}
    			}
    		}

    		return $data;
    	}
}
