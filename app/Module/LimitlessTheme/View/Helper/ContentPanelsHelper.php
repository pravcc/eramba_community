<?php
App::uses('AppHelper', 'View/Helper');

class ContentPanelsHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Form', 'Html'];

	public function render($options = [])
	{
		$options = array_merge([
			'heading' => null,
			'body' => null
		], $options);

		$body = $this->_body($options['body']);
		$heading = $this->_heading($options['heading']);

		return $this->Html->div('panel panel-flat', $heading . $body);
	}

	protected function _heading($heading)
	{
		$heading = '';

		if (!empty($heading)) {
			$title = $this->_title($heading);
			$heading = $this->Html->div('panel-heading', $title);
		}

		return $heading;
	}

	protected function _body($body)
	{
		return $this->Html->div('panel-body', $body);
	}

	protected function _title($title)
	{
		return $this->Html->tag('h5', $title, [
			'class' => 'panel-title'
		]);
	}
}