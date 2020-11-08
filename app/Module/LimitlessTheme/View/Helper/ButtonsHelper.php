<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Hash', 'Utility');

class ButtonsHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Html'];

	/**
	 * Render a button.
	 * 
	 * @param  string $text    Button text
	 * @param  array  $options Additional options for the button.
	 * @return string          Rendered button
	 */
	public function render($text, $options = [])
	{
		$options = Hash::merge([
			'id' => null,
			'type' => 'default',
			'class' => ['btn', "btn-{$options['type']}"],
			'href' => null,
			'data' => [],
			'disabled' => false,
		], $options);

		$element = (empty($options['href'])) ? 'button' : 'a';

		$dataAttributes = $this->buildData($options['data']);

		return $this->Html->tag($element, $text, [
			'id' => $options['id'],
			'class' => $options['class'],
			'escape' => false,
			'href' => (is_array($options['href'])) ? Router::url($options['href']) : $options['href'],
			'disabled' => $options['disabled']
		] + $dataAttributes);
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