<?php

App::uses('AppModel', 'Model');

class ObjectStatusAppModel extends AppModel {
	public $tablePrefix = 'object_status_';

	public function tableName() {
		return $this->tablePrefix . $this->table;
	}
}
