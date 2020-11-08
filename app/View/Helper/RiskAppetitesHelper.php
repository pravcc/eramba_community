<?php
App::uses('AppHelper', 'View/Helper');
App::uses('AppModel', 'Model');
App::uses('RiskAppetiteThreshold', 'Model');

class RiskAppetitesHelper extends AppHelper {
    public $helpers = array('Html', 'Text');
    public $settings = array();
    
    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->settings = $settings;
    }

    public function label($threshold, $text) {
    	$color = $threshold['RiskAppetiteThreshold']['color'];
    	$text = $threshold['RiskAppetiteThreshold']['title'] . ' (' . $text . ')';

    	$html = $this->Html->div('content-box threshold-alert', $text, [
			'escape' => false,
			'style' => 'background-color:' . $color
		]);

		return $html;
    }

    /*
	 * Color schemes
	 */
	 public static function colorClasses($value = null) {
		$options = array(
			// RiskAppetiteThreshold::COLOR_DEFAULT => 'threshold-color-default',
			// RiskAppetiteThreshold::COLOR_LOW_PRIORITY => 'threshold-color-1'
			RiskAppetiteThreshold::COLOR_DEFAULT => 'default',
			RiskAppetiteThreshold::COLOR_PRIMARY => 'primary',
			RiskAppetiteThreshold::COLOR_SUCCESS => 'success',
			RiskAppetiteThreshold::COLOR_INFO => 'info',
			RiskAppetiteThreshold::COLOR_WARNING => 'warning',
			RiskAppetiteThreshold::COLOR_DANGER => 'danger',
		);
		return AppModel::enum($value, $options);
	}

}