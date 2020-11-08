<?php
App::uses('AppHelper', 'View/Helper');

class AdvancedFiltersObjectHelper extends AppHelper
{
	public $settings = [];
	public $helpers = [];

	/**
	 * Render datatable filter using an instance of AdvancedFiltersObject class.
	 */
	public function render(AdvancedFiltersObject $Instance, $options = [])
	{
		
	}

	/**
	 * Possibility to quickly render a button based on its type in this kind of way:
	 * ```
	 * echo $this->Buttons->primary("Test Button");
	 * ```
	 * 
	 * @param  string $name Type of the button
	 * @param  array $args  First argument is the text for the button, any additional arguments
	 *                      are additional options, @see ButtonsHelper::render() method
	 * @return string       Rendered button
	 * 
	 * @see 				FlashComponent::_call() for further details.
	 */
	public function __call($name, $args) {
		$options = ['type' => Inflector::underscore($name)];

		if (count($args) < 1) {
			throw new InternalErrorException('Button text missing.');
		}

		if (!empty($args[1])) {
			$options += (array)$args[1];
		}

		return $this->render($args[0], $options);
	}
}