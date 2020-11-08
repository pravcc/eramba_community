<?php
App::uses('AppHelper', 'View/Helper');
App::uses('LimitlessThemeException', 'Module/LimitlessTheme/Error');
App::uses('TooltipsTrait', 'Module/LimitlessTheme/Lib/Tooltips/Trait');
App::uses('Tooltip', 'Module/LimitlessTheme/Lib/Tooltips');

class TooltipsHelper extends AppHelper
{
	use TooltipsTrait;

	public $helpers = array('Html', 'Form');

	public function __construct(View $view, $settings = array())
	{
		parent::__construct($view, $settings);
	}

	/**
	 * Tooltip Objects
	 */
	protected $tooltips;

	/**
	 * Name of the tooltip which is currently used as default (for easy access to tooltip through this helper)
	 */
	protected $currentTooltipName = '';

	public function setCurrentTooltip($name)
	{
		if (!isset($this->Tooltips[$name])) {
			return false;
		}

		$this->currentTooltipName = $name;
		return true;
	}

	/**
	 * Create new Tooltip object
	 * @param  array      $options   Set values to params of the class
	 * @return Tooltip  Returns Tooltip object
	 */
	public function createTooltip(array $options = [])
	{
		$tooltip = $this->createObject('Tooltip', 'tooltips', $options);
		$this->currentTooltipName = $tooltip->getName();

		return $tooltip;
	}

	/**
	 * Get Tooltip instance
	 * @param  string     $name                        Name of Tooltip (index by which user can reach the tooltip object)
	 * @param  array      $options                     Options for createTooltip method of this helper
	 * @param  int        $createNew                   Options are:
	 *                                                 	- 0 Do not create new tooltip
	 *                                                 	- 1 (default) Create new tooltip if requested or current tooltip not exists
	 *                                                 	- 2 Force to create new tooltip
	 * @return Tooltip                               Returns Tooltip object
	 */
	public function tooltip($name = null, array $options = [], int $createNewTooltip = 1)
	{
		$tooltip = null;
		if ($createNewTooltip < 2) {
			if (empty($name)) {
				if (isset($this->tooltips[$this->currentTooltipName])) {
					$tooltip = $this->tooltips[$this->currentTooltipName];
				}
			} elseif (isset($this->tooltips[$name])) {
				$tooltip = $this->tooltips[$name];
			}
		}

		if (empty($tooltip) && $createNewTooltip > 0) {
			$tooltip = $this->createTooltip($options);
		}

		if (empty($tooltip)) {
			throw new LimitlessThemeException(__('The tooltip (%s) you\'re trying to use doesn\'t exists', $name));
		}

		return $tooltip;
	}
}
