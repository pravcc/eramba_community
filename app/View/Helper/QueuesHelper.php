<?php
App::uses('AppHelper', 'View/Helper');
App::uses('QueueTransport', 'Network/Email');

class QueuesHelper extends AppHelper {
	public $helpers = array('Html', 'Form');

	public function toolbar() {
		// return $this->flushBtn();
	}

	public function flushBtn() {
		return $this->Html->link( '<i class="icon-exclamation-sign"></i> ' . __('Flush Queue'), [
				'controller' => 'queue',
				'action' => 'flush'
			], array(
				'class' => 'btn btn-danger bs-popover',
				'data-title' => __('Flush Queue'),
				'data-content' => __('Instantly process %d emails from the queue in the same order as with a Cron Job.', QueueTransport::getQueueLimit()),
				'data-trigger' => 'hover',
				'data-placement' => 'right',
				'escape' => false
			)
		);
	}
}