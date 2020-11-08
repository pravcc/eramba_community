<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipColumn extends TooltipElement
{
	/**
	 * TooltipElements objects
	 */
	protected $elements = [];

	/**
	 * Tag for row
	 * @var string
	 */
	protected $tag = 'div';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$column = $this->getStartTag();
		foreach ($this->elements as $element) {
			$column .= $element->render();
		}
		$column .= $this->getEndTag();

		return $column;
	}

	/**
	 * Get TooltipElement instance
	 * @param  string           $name  Name of TooltipElement (index by which user can reach the tooltip element object)
	 * @return TooltipElement         Returns TooltipElement object
	 */
	public function getElement($name)
	{
		if (!isset($this->elements[$name])) {
			throw new LimitlessThemeException(__('The element (%s) you\'re trying to use doesn\'t exists', $name));
		}

		return $this->elements[$name];
	}

	/**
	 * Create new TooltipElement object
	 * @param  array            $options  Set values to params of the class
	 * @return TooltipElement            Returns TooltipElement object
	 */
	public function element(string $name, array $options = [])
	{
		$class = 'Tooltip' . ucfirst($name);
		return $this->createObject($class, 'elements', $options);
	}

	/**
	 * Add more element objects at once
	 * @param  array  $elements Array of elements (one array with name => options per element)
	 * @return array           Array of TooltipElement objects
	 */
	public function elements(array $elements)
	{
		$tempElements = [];
		foreach ($elements as $elementName => $elementOptions) {
			$tempElements[] = $this->element($elementName, $elementOptions);
		}

		return $tempElements;
	}

	public function heading(string $content, array $options = [])
	{
		$options['content'] = $content;
		$options['tag'] = isset($options['tag']) ? $options['tag'] : 'h6';
		$options['class'] = isset($options['class']) ? $options['class'] : 'text-semibold';
		return $this->element('heading', $options);
	}

	public function text(string $content, array $options = [])
	{
		$options['content'] = $content;
		return $this->element('text', $options);
	}

	public function image(string $file, array $options = [])
	{
		$options['class'] = isset($options['class']) ? $options['class'] : 'img-responsive';
		$image = $this->element('image', $options);
		$image->addAttribute('src', '/tooltips/images/' . $file);

		return $image;
	}

	public function video(string $file, string $type, array $options = [])
	{
		$options['class'] = isset($options['class']) ? $options['class'] : 'video-js vjs-default-skin mb-20';
		$video = $this->element('video', $options);
		$video->addAttribute('controls', '');
		$video->addAttribute('preload', 'auto');
		$video->addAttribute('width', '100%');
		$video->addAttribute('height', '400');
		$video->addAttribute('data-setup', "{}");
		$video->setSourceUrl('/tooltips/videos/' . $file);
		$video->setSourceType('video/' . $type);

		return $video;
	}

	public function youtube(string $videoId, array $options = [])
	{
		$options['class'] = isset($options['class']) ? $options['class'] : 'mb-20';
		$video = $this->element('iframe', $options);
		$video->addAttribute('width', '100%');
		$video->addAttribute('height', '400');
		$video->addAttribute('src', 'https://www.youtube.com/embed/' . $videoId . '?rel=0');
		$video->addAttribute('frameborder', '0');
		$video->addAttribute('allow', 'autoplay; encrypted-media');
		$video->addAttribute('allowfullscreen', '');

		return $video;
	}
}
