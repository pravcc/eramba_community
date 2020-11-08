<?php
class InactivityBase extends NotificationsBase {

	public function __construct($options = array()) {
		parent::__construct($options);

		$this->title = __('Inactivity Notification');
		$this->description = __('Used to remind that an object has not recieved any comments, attachments and has not been modified during the period specified for this interval');

		$periodContain = array(
			'conditions' => array(
				'created >=' => $this->_periodDateFrom 
			),
			'fields' => array('id', 'created'),
			'order' => array('created' => 'ASC')
		);

		$this->contain = array(
			'Comment' => $periodContain,
			'Attachment' => $periodContain
		);
	}

	public function parseData($item) {
		// not commented
		$cond1 = empty($item['Comment']);

		// no file uploaded
		$cond2 = empty($item['Attachment']);

		// and not modified in the interval
		$cond3 = isset($item[$this->model]['modified']);
		$cond3 = $cond3 && ($item[$this->model]['modified'] <= $this->_periodDateFrom);

		// triggers when there is no comment and attachment added and object hasnt been modified
		// in the past $this->_periodDateFrom
		if ($cond1 && $cond2 && $cond3) {
			return true;
		}

		return false;
	}
}
