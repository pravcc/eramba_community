<?php
class SystemRecord extends AppModel {
	public $actsAs = array(
		'Containable', 'Search.Searchable'
	);

	public $belongsTo = array( 'User' );

	public $filterArgs = array(
		'search' => array(
			'type' => 'like',
			'field' => array(
				'SystemRecord.item',
				'SystemRecord.model_nice',
				'SystemRecord.foreign_key',
				'SystemRecord.notes',
			),
			'_name' => 'Search'
		)
	);

	public function afterSave( $created, $options = array() ) {

	}

	/**
	 * Retrieve system records associated with an item.
	 */
	public function getByItem($model, $foreign_key) {
		return $this->find('all', array(
			'conditions' => array(
				'SystemRecord.model' => $model,
				'SystemRecord.foreign_key' => $foreign_key
			),
			'fields' => array(
				'SystemRecord.notes',
				'SystemRecord.workflow_status',
				'SystemRecord.workflow_comment',
				'SystemRecord.type',
				'SystemRecord.user_id',
				'SystemRecord.created',
				'User.name',
				'User.surname'
			),
			'order' => array('SystemRecord.created' => 'DESC'),
			'limit' => 10,
			'recursive' => 0
		));
	}
}
