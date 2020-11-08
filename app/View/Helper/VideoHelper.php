<?php
class VideoHelper extends AppHelper {
	public $helpers = array('Html', 'Js');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	/**
	 * @deprecated e1.0.6.016 Video helpers are being replaced by Help/Feedback functionality which will be introduced in a close future.
	 */
	public function getVideoLink($model) {
		// return $this->Html->div("btn-group group-merge", $this->Js->link( '<i class="icon-play-sign"></i>' . __('Help'),
		// 	HELP_REQUEST . $model
		// 	, array(
		// 	'class' => 'btn',
		// 	'escape' => false,
		// 	// 'target' => '_blank',
		// 	'buffer' => false,
		// 	'success' => '$("body").append(data);'
		// )));
		return false;
	}
}