<?php
App::uses('AppHelper', 'View/Helper');
class ObjectVersionAuditDeltaHelper extends AppHelper {
	public $helpers = array('Html', 'Ux');

	/*public function getValue(ObjectVersionAuditDelta $AuditDelta, $type = 'new') {
		$divClassArr = array('alert', 'audit-delta-item');

		if ($type == 'new') {
			$divClassArr[] = 'alert-success';
			$divClassArr[] = 'audit-delta-new-value';
			$value = $AuditDelta->getNewValue();
		}

		if ($type == 'old') {
			$divClassArr[] = 'alert-danger';
			$divClassArr[] = 'audit-delta-old-value';
			$value = $AuditDelta->getOldValue();
		}

		$divClass = implode(' ', $divClassArr);
		$divHtml = $this->Html->div($divClass, $value);

		return $divHtml;
	}*/
}