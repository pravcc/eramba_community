<?php
App::uses('AppModel', 'Model');

class DashboardAppModel extends AppModel {
	public $tablePrefix = 'dashboard_';

	public $fieldData = ['not' => ['empty']];
	public $actsAs = ['FieldData.FieldData'];
}
