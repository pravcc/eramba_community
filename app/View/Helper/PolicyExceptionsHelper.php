<?php
App::uses('AppHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('PolicyException', 'Model');

class PolicyExceptionsHelper extends AppHelper
{
	public $helpers = ['Html', 'FieldData.FieldData'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		$this->helpers[] = 'ErambaTime';
		$this->helpers[] = 'NotificationSystem';

		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function getStatusArr($item, $allow = '*', $model = 'PolicyException') {
		$item = $this->processItemArray($item, $model);
		$statuses = array();

		if ($this->getAllowCond($allow, 'status') && $item[$model]['status'] == POLICY_EXCEPTION_CLOSED) {
			$statuses[$this->getStatusKey('status')] = array(
				'label' => __('Closed'),
				'type' => 'success'
			);
		}
		
		if ($this->getAllowCond($allow, 'expired') && $item[$model]['expired'] == ITEM_STATUS_EXPIRED) {
			$statuses[$this->getStatusKey('expired')] = array(
				'label' => __('Exception Expired'),
				'type' => 'danger'
			);
		}
		else {
			if ($this->getAllowCond($allow, 'status') && $item[$model]['status'] == POLICY_EXCEPTION_OPEN) {
				$statuses[$this->getStatusKey('status')] = array(
					'label' => __('Open'),
					'type' => 'success'
				);
			}
		}

		return $statuses;
	}

	public function getStatuses($item, $model = 'PolicyException', $options = array()) {
		$options = $this->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow'], $model);

		return $this->styleStatuses($statuses, $options);
	}

	public function closureDateField(FieldDataEntity $Field)
	{
		$FieldDataCollection = ClassRegistry::init('PolicyException')->getFieldCollection();

		$out = $this->FieldData->input($FieldDataCollection->closure_date_toggle, [
			'class' => [
				'policy-exception-closure-date-toggle'
			]
		]);

        $out .= $this->FieldData->input($Field, [
        	'class' => [
        		'policy-exception-closure-date'
        	]
        ]);

        $out .= $this->Html->scriptBlock("
			jQuery(function($) {
				$(\".policy-exception-closure-date-toggle\").on(\"change\", function(e) {
					if ($(this).is(\":checked\")) {
						$(\".policy-exception-closure-date-toggle\").attr(\"checked\", true);
						$(\".policy-exception-closure-date\").attr(\"disabled\", \"disabled\");
					}
					else {
						$(\".policy-exception-closure-date-toggle\").attr(\"checked\", false);
						$(\".policy-exception-closure-date\").removeAttr(\"disabled\");
					}
				}).trigger(\"change\");
			});
		");

        return $out;
	}

}
