<?php
App::uses('Attachment', 'Attachments.Model');

class LastAttachment extends Attachment
{
	public $useTable = 'attachments';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
	}

	public function setVirtualField()
	{
		$this->virtualFields['last_created'] = 'MAX(' . $this->alias . '.created)';
	}

	public function unsetVirtualField()
	{
		unset($this->virtualFields['last_created']);
	}
}
