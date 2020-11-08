<?php
App::uses('ModuleBase', 'Lib');
App::uses('Folder', 'Utility');
App::uses('TooltipsException', 'Module/Tooltips/Error');

class TooltipsModule extends ModuleBase
{
	/**
	 * Path of tooltip (usually a section to which tooltip belongs) - it's name of folder where your tooltips are placed
	 * @var string
	 */
	protected $path = '';

	/**
	 * Alias of model of section where this tooltip is displayed
	 * @var  string
	 */
	protected $modelAlias = '';

	/**
	 * Type of tooltip
	 * 
	 * Options:
	 *  - large
	 *  - small
	 * 
	 * @var string
	 */
	protected $type = 'large';

	/**
	 * The name of dataset of tooltips (name of folder where tooltips are located)
	 * @var string
	 */
	protected $dataset = '';

	protected $fileId = null;
	protected $tooltip = null;
	protected $tooltipPrefix = 'Tooltip';

	const BASE_URL = 'tooltips/tooltips/';
	const TOOLTIP_DISPLAY_ACTION = 'tooltip';
	const TOOLTIP_LOG_ACTION = 'saveLog';
	const FULL_BASE_DIR = APP . 'Module/Tooltips/Data/';
	const BASE_DIR = 'Tooltips.Data/';
	const FULL_TEMPLATES_DIR = APP . 'Module/Tooltips/View/Elements/Templates/';
	const TEMPLATES_DIR = 'Tooltips.Elements/Templates/';

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function setModelAlias($alias)
	{
		$this->modelAlias = $alias;
	}

	public function setFileId($fileId)
	{
		$this->fileId = $fileId;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function setDataset($dataset)
	{
		$this->dataset = $dataset;
	}

	public function findAllTooltips($sort = true)
	{
		$dir = new Folder($this->getTooltipPath(true));
		$tooltips = $dir->find($this->tooltipPrefix . '.*\.php');
		
		if ($sort) {
			sort($tooltips);
		}

		return $tooltips;
	}

	protected function getType($uc = true)
	{
		return $uc ? ucfirst($this->type) : $this->type;
	}

	protected function getFileId()
	{
		$fileId = $this->fileId;
		if (empty($fileId)) {
			$tooltips = $this->findAllTooltips();
			if (!empty($tooltips)) {
				$fileId = $this->getFileIdFromTooltipFileName($tooltips[0]);
			}
		}

		return $fileId;
	}

	public function getTooltip()
	{
		$fileName = $this->getTooltipName($this->getFileId(), false);

		if (!in_array($fileName . '.php', $this->findAllTooltips())) {
			throw new TooltipsException(__('The tooltip (%s) which you\'re trying to use doesn\'t exists', $fileName));
		}

		$className = $fileName;

		App::uses($fileName, $this->getTooltipPath());
		$tooltip = new $className();

		return $this->tooltip = $tooltip;
	}

	protected function getTooltipPath($full = false)
	{
		$base_dir = $full ? self::FULL_BASE_DIR : self::BASE_DIR;
		return $base_dir . $this->getType() . DS . $this->path . DS;
	}

	public function getTooltipTemplate()
	{
		return self::TEMPLATES_DIR . $this->tooltip->getTemplate();
	}

	protected function getFileIdFromTooltipFileName($fileName)
	{
		$fileId = substr($fileName, strlen($this->tooltipPrefix), -1 * strlen('.php'));
		return $fileId;
	}

	public function getTooltipButtons()
	{
		$buttons = [];
		$type = strtolower($this->getType(false));
		if ($type === 'small') {
			$buttons = $this->getSmallTooltipButtons();
		} elseif ($type === 'large') {
			$buttons = $this->getLargeTooltipButtons();
		}

		return $buttons;
	}

	public function getSmallTooltipButtons()
	{
		$buttons = [
			'gotIt' => [
				'name' => __('Got It!'),
				'path' => self::BASE_URL . self::TOOLTIP_LOG_ACTION . '/' . $this->modelAlias . '/' . $this->getType(false)
			]
		];
		$tooltip = $this->getTooltip();
		$showMoreBtn = $tooltip->getShowMoreBtn();
		if (!empty($showMoreBtn)) {
			$path = $this->getTooltipBtnPath('large', $showMoreBtn['dataset']);
			if (!empty($showMoreBtn['id'])) {
				$path .= $showMoreBtn['id'];
			}
			$buttons['showMore'] = [
				'name' => __('Show me more!'),
				'path' => $path
			];
		}

		return $buttons;
	}

	public function getLargeTooltipButtons()
	{
		$buttons = [];
		$tooltips = $this->findAllTooltips();
		$actualTooltip = $this->getTooltipName($this->getFileId(), true);
		$count = count($tooltips);
		$path = $this->getTooltipBtnPath($this->getType(false), $this->dataset);
		for ($i = 0; $i < $count; ++$i) {
			$tooltip = $tooltips[$i];
			if ($actualTooltip === $tooltip) {
				if ($i > 0) {
					$buttons[] = [
						'previous' => $path . $this->getFileIdFromTooltipFileName($tooltips[$i - 1])
					];
				}
				if ($i < $count - 1) {
					$buttons[] = [
						'next' => $path . $this->getFileIdFromTooltipFileName($tooltips[$i + 1])
					];
				}
				if ($i == $count - 1) {
					$buttons[] = [
						'gotIt' => self::BASE_URL . self::TOOLTIP_LOG_ACTION . '/' . $this->modelAlias . '/' . $this->getType(false)
					];
				}

				break;
			}
		}

		return $buttons;
	}

	protected function getTooltipBtnPath($type = null, $dataset = null)
	{
		$path = self::BASE_URL . self::TOOLTIP_DISPLAY_ACTION . '/' . $this->modelAlias . '/';

		if (!empty($type)) {
			$path .= $type . '/';
		}

		if (!empty($dataset)) {
			$path .= $dataset . '/';
		}

		return $path;
	}

	protected function getTooltipName($fileId, $ext = false)
	{
		$tooltipName = $this->tooltipPrefix . $fileId;
		if ($ext) {
			$tooltipName .= '.php';
		}

		return $tooltipName;
	}
}