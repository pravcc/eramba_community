<?php
App::uses('ErambaHelper', 'View/Helper');
class ThirdPartiesHelper extends ErambaHelper {
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->processItemArray($item, 'ThirdParty');
		$statuses = array();

		/*if ($this->getAllowCond($allow, 'security_incident_open_count') && $item['ThirdParty']['security_incident_open_count'] > 0) {
			$statuses[$this->getStatusKey('security_incident_open_count')] = array(
				'label' => __('Ongoing Incident'),
				'type' => 'warning'
			);
		}*/

		$inherit = array(
			'SecurityIncidents' => array(
				'model' => 'SecurityIncident',
				'config' => array('ongoing_incident')
			)
		);

		if ($this->getAllowCond($allow, INHERIT_CONFIG_KEY)) {
			$statuses = am($statuses, $this->getInheritedStatuses($item, $inherit));
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow']);

		return $this->styleStatuses($statuses, $options);
	}

}