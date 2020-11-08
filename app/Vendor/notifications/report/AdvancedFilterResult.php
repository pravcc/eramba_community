<?php
class AdvancedFilterResult extends NotificationsBase {
	public $filename = 'AdvancedFilterResult.php';
	public $internal = 'advanced_filter_result';
	public $model = null;
	public $isReportType = true;
	public $customEmailTemplate = true;

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Advanced Filter Results');
		$this->description = __('TBD');
	}
}
