<?php
App::uses('TooltipsException', 'Module/Tooltips/Error');
App::uses('TooltipsModule', 'Module/Tooltips/Lib');
App::uses('Folder', 'Utility');

abstract class TooltipBuilder
{
	/**
	 * Template of the tooltip
	 * @var string
	 */
	private $template = '';
	/**
	 * Header of the tooltip
	 * @var string
	 */
	private $header = '';
	/**
	 * Paragraphs of the tooltip
	 * @var array
	 */
	private $paragraphs = [];
	/**
	 * Images of the tooltip
	 * @var array
	 */
	private $images = [];
	/**
	 * Videos of the tooltip
	 * @var array
	 */
	private $videos = [];
	/**
	 * Youtube videos IDs for the tooltip
	 * @var array
	 */
	private $youtubeIds = [];
	/**
	 * Show more button
	 * @var array
	 */
	private $showMoreBtn = null;

	public function __construct()
	{
		$this->init();
	}

	abstract public function init();

	public function getTemplate()
	{
		return $this->template;
	}

	public function getHeader()
	{
		return $this->header;
	}

	public function getParagraphs()
	{
		return $this->paragraphs;
	}

	public function getImages()
	{
		return $this->images;
	}

	public function getVideos()
	{
		return $this->videos;
	}

	public function getYoutubeIds()
	{
		return $this->youtubeIds;
	}

	public function getShowMoreBtn()
	{
		return $this->showMoreBtn;
	}

	protected function setTemplate($tpl)
	{
		if (!in_array($tpl . '.ctp', $this->getTemplates())) {
			throw new TooltipsException(__('The template (%s) which you\'re trying to use doesn\'t exists', $tpl));
		}

		$this->template = $tpl;
	}

	protected function getTemplates()
	{
		$dir = new Folder(TooltipsModule::FULL_TEMPLATES_DIR);
		$templates = $dir->find('.*\.ctp');

		return $templates;
	}

	protected function setHeader($h)
	{
		$this->header = $h;
	}

	protected function addParagraph($h, $text)
	{
		$this->paragraphs[] = [
			'heading' => $h,
			'text' => $text
		];
	}

	protected function addImage($file)
	{
		$this->images[] = $file;
	}

	protected function addVideo($file, $type)
	{
		$this->videos[] = [
			'file' => $file,
			'type' => $type
		];
	}

	protected function addYoutube($videoId)
	{
		$this->youtubeIds[] = $videoId;
	}

	protected function setShowMoreBtn($dataset = null, $id = null)
	{
		$this->showMoreBtn = [
			'dataset' => $dataset,
			'id' => $id
		];
	}
}