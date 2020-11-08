<?php
App::uses('TooltipElement', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipFooter extends TooltipElement
{
	/**
	 * TooltipButtons objects
	 */
	protected $buttons = [];

	protected $tag = 'div';
	protected $class = 'modal-footer';

	public function __construct(string $name = '')
	{
		parent::__construct($name);
	}

	public function render()
	{
		$footer = $this->getStartTag();
		if (empty($this->buttons)) {
			$this->gotItBtn();
		}
		foreach ($this->buttons as $button) {
			$footer .= $button->render();
		}
		$footer .= $this->getEndTag();

		return $footer;
	}

	public function setButtons($buttons)
	{
		foreach ($buttons as $button) {
			if (!empty($button['previous'])) {
				// Previous tooltip button
				$this->prevBtn($button['previous']);
			}
			if (!empty($button['next'])) {
				// Next tooltip button
				$this->nextBtn($button['next']);
			}
			if (!empty($button['gotIt'])) {
				// Got it tooltip button
				$this->gotItBtn($button['gotIt']);
			}
		}
	}

	/**
	 * Get TooltipButton instance
	 * @param  string         $name   Name of TooltipButton (index by which user can reach the tooltip button object)
	 * @return TooltipButton
	 */
	public function getButton($name)
	{
		if (!isset($this->buttons[$name])) {
			throw new LimitlessThemeException(__('The button (%s) you\'re trying to use doesn\'t exists', $name));
		}

		return $this->buttons[$name];
	}

	/**
	 * Create new TooltipButton object
	 * @param  array          $options   Set values to params of the class
	 * @return TooltipButton              Returns TooltipButton object - existing or newly created
	 */
	public function button(array $options = [])
	{
		return $this->createObject('TooltipButton', 'buttons', $options);
	}

	public function gotItBtn(string $tooltipPath = '', array $options = [])
	{
		$options = array_merge([
			'content' => __('Got It!'),
			'class' => 'btn btn-primary'
		], $options);
		$button = $this->button($options);

		if (!empty($tooltipPath)) {
			$button->addAttribute('data-yjs-request', 'crud/load');
			$button->addAttribute('data-yjs-target', 'modal');
			$button->addAttribute('data-yjs-modal-id', 1);
			$button->addAttribute('data-yjs-datasource-url', 'post::' . $tooltipPath);
			$button->addAttribute('data-yjs-event-on', 'click');
			$button->addAttribute('data-yjs-on-modal-success', 'close');
		} else {
			$button->addAttribute('data-dismiss', 'modal');
		}

		return $button;
	}

	public function prevBtn(string $tooltipPath, array $options = [])
	{
		$options = array_merge([
			'content' => __('Previous'),
			'class' => 'btn btn-default'
		], $options);
		$button = $this->button($options);

		$button->addAttribute('data-yjs-request', 'crud/load');
		$button->addAttribute('data-yjs-target', 'modal');
		$button->addAttribute('data-yjs-modal-id', 1);
		$button->addAttribute('data-yjs-datasource-url', 'get::' . $tooltipPath);
		$button->addAttribute('data-yjs-event-on', 'click');

		return $button;
	}

	public function nextBtn(string $tooltipPath, array $options = [])
	{
		$options = array_merge([
			'content' => __('Next'),
			'class' => 'btn btn-primary'
		], $options);
		$button = $this->button($options);

		$button->addAttribute('data-yjs-request', 'crud/load');
		$button->addAttribute('data-yjs-target', 'modal');
		$button->addAttribute('data-yjs-modal-id', 1);
		$button->addAttribute('data-yjs-datasource-url', 'get::' . $tooltipPath);
		$button->addAttribute('data-yjs-event-on', 'click');

		return $button;
	}
}