<?php
App::uses('ModelBehavior', 'Model');
App::uses('AppModel', 'Model');

// system log management
class SystemLogBehavior extends ModelBehavior {
	private $runtime = array();
	private $queue = array();
	private $dbRecord = array();
	public static $Models = array();
	public static $isCron = false;

	// public function setup(Model $Model, $settings = array()) {
	// 	$this->runtime[$Model->alias] = array(
	// 		'systemLogQueue' => array(),
	// 		'created' => null,
	// 		'latestTitle' => array(),
	// 		'item' => null,
	// 		'notes' => array('default' => array()),
	// 		'workflowComment' => array('default' => array())
	// 	);
	// }

	// public function beforeSave(Model $Model, $options = array()) {
	// 	if ($Model->id != null) {
	// 		$this->setLatestTitle($Model);
	// 	}
	// 	else {
	// 		//$this->runtime[$Model->alias]['latestTitle'] = null;
	// 	}

	// 	return true;
	// }

	// public function afterSave(Model $Model, $created, $options = array()) {
	// 	if (isset($Model->data[$Model->name]['_workflow_comment'])) {
	// 		$comment = Purifier::clean($Model->data[$Model->name]['_workflow_comment'], 'Strict');
	// 		$this->addWorkflowComment($Model, $comment);
	// 	}

	// 	if (isset($Model->data[$Model->name]['_notes'])) {
	// 		$this->addNote($Model, $Model->data[$Model->name]['_notes']);
	// 	}

	// 	$this->addToQueue($Model, array(
	// 		'model' => $Model->alias,
	// 		'id' => $Model->id,
	// 		'type' => $created ? 1 : 2,
	// 		'options' => $options
	// 	));
	// }

	// public function beforeDelete(Model $Model, $cascade = true) {
	// 	if (isset($Model->data[$Model->name]['_workflow_comment'])) {
	// 		$comment = Purifier::clean($Model->data[$Model->name]['_workflow_comment'], 'Strict');
	// 		$this->addWorkflowComment($Model, $comment);
	// 	}

	// 	if (isset($Model->data[$Model->name]['_notes'])) {
	// 		$this->addNote($Model, $Model->data[$Model->name]['_notes']);
	// 	}

	// 	$this->addToQueue($Model, array(
	// 		'model' => $Model->alias,
	// 		'id' => $Model->id,
	// 		'type' => 3,
	// 		//'options' => $options
	// 	));

	// 	return true;
	// }

	/**
	 * Quickly save a system log.
	 */
	public function quickLogSave(Model $Model, $id, $type, $message, $user_id = null) {
		return true;

		// if (empty($id)) {
		// 	return true;
		// }

		// if (is_array($id)) {
		// 	$ret = true;
		// 	foreach ($id as $v) {
		// 		$ret &= $Model->quickLogSave($v, $type, $message, $user_id);
		// 	}

		// 	return $ret;
		// }

		// if ($user_id === null) {
		// 	$logged_id = $Model->currentUser('id');
		// 	if (!empty($logged_id)) {
		// 		$user_id = $logged_id;
		// 	}
		// 	else {
		// 		$user_id = ADMIN_ID;
		// 	}
		// }

		// if (empty($user_id)) {
		// 	return true;
		// }

		// $Model->id = $id;
		
		// $dbRecord = $this->getDbRecord($Model);
		// $currentTitle = $this->getTitleFromDbRecord($Model, $dbRecord);

		// if (!isset($Model->hasMany['SystemRecord'])) {
		// 	$Model->bindModel(array(
		// 		'hasMany' => array(
		// 			'SystemRecord' => array(
		// 				'className' => 'SystemRecord',
		// 				'foreignKey' => 'foreign_key',
		// 				'conditions' => array(
		// 					'SystemRecord.model' => $Model->name
		// 				)
		// 			)
		// 		)
		// 	));
		// }

		// $saveData = array(
		// 	'model' => $Model->name,
		// 	'model_nice' => parseModelNiceName($Model->name),
		// 	'foreign_key' => $id,
		// 	'item' => $currentTitle ? $currentTitle : '',
		// 	// 'notes' => $message,
		// 	'type' => $type,
		// 	'workflow_status' => '',
		// 	'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
		// 	'user_id' => $user_id,
		// );

		// if (is_array($message)) {
		// 	$ret = true;
		// 	foreach ($message as $note) {
		// 		// $Model->create();
		// 		$ret &= $this->quickLogSave($Model, $id, $type, $note, $user_id);
		// 	}

		// 	return $ret;
		// }

		// $saveData['notes'] = $message;
		
		// $Model->SystemRecord->create();
		// return $Model->SystemRecord->save($saveData);
	}


	/**
	 * Log system record.
	 * @param  int $type  1-created, 2-updated, 3-deleted, 4-login, 5-wrong login.
	 */
	private function logSystemRecord(Model $Model, $type, $options = array(), $forced = false) {
		return true;
		// $user_id = $Model->currentUser('id');

		// //record user login to system
		// if (empty($user_id) && $Model->name == 'User' && !empty($Model->id)) {
		// 	$user_id = $Model->id;
		// }

		// if (self::$isCron) {
		// 	$user_id = ADMIN_ID;
		// }

		// if (empty($user_id)) {
		// 	return true;
		// }

		// $dbRecord = $this->getDbRecord($Model);
		// $currentTitle = $this->getTitleFromDbRecord($Model, $dbRecord);
		// if (!$forced && $type === 2 && $this->runtime[$Model->alias]['latestTitle'][$Model->id] != $currentTitle) {
		// 	$item = sprintf('%s > %s', $this->runtime[$Model->alias]['latestTitle'][$Model->id], $currentTitle);
		// 	$this->addNoteToLog($Model, __('Title of the item has been changed'));
		// }
		// else {
		// 	$item = $currentTitle;
		// }

		// if (isset($options['titleSuffix'])) {
		// 	$item = $item . $options['titleSuffix'];
		// }

		// $workflowStatus = null;
		// if (isset($dbRecord[$Model->name]['workflow_status'])) {
		// 	$workflowStatus = $dbRecord[$Model->name]['workflow_status'];
		// }

		// $saveData = array(
		// 	'model' => $Model->name,
		// 	'model_nice' => parseModelNiceName($Model->name),
		// 	'foreign_key' => $Model->id,
		// 	'item' => $item ? $item : '',
		// 	'type' => $type,
		// 	'workflow_status' => $workflowStatus,
		// 	'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
		// 	'user_id' => $user_id
		// );

		// $_modelWorkflowComments = $this->getWorkflowComments($Model);

		// $_modelNotes = $this->getNotes($Model);
		
		// if (!isset($Model->hasMany['SystemRecord'])) {
		// 	$Model->bindModel(array(
		// 		'hasMany' => array(
		// 			'SystemRecord' => array(
		// 				'className' => 'SystemRecord',
		// 				'foreignKey' => 'foreign_key',
		// 				'conditions' => array(
		// 					'SystemRecord.model' => $Model->name
		// 				)
		// 			)
		// 		)
		// 	));
		// }

		// $ret = true;
		// if (!empty($_modelNotes)) {
		// 	foreach ($_modelNotes as $note) {
		// 		$saveData['notes'] = $note;
		// 		$saveData['workflow_comment'] = $_modelWorkflowComments;

		// 		$Model->SystemRecord->create();
		// 		$ret &= $Model->SystemRecord->save($saveData);
		// 		$_modelWorkflowComments = '';
		// 	}
		// }
		// elseif (!empty($_modelWorkflowComments)) {
		// 	$saveData['notes'] = '';
		// 	$saveData['workflow_comment'] = $_modelWorkflowComments;

		// 	$Model->SystemRecord->create();
		// 	$ret &= $Model->SystemRecord->save($saveData);
		// }
		// else {
		// 	$saveData['notes'] = '';
		// 	$saveData['workflow_comment'] = '';

		// 	$Model->SystemRecord->create();
		// 	$ret &= $Model->SystemRecord->save($saveData);
		// }

		// // temporary solution to not group notes in a system records list
		// // $ret = $this->saveActualLog($Model, $saveData, $_modelNotes, $_modelWorkflowComments);

		// $this->cleanupRuntime($Model);		

		// return $ret;
	}

	// private function saveActualLog($Model, $saveData = array(), $notes, $workflowComments) {
	// 	if (is_array($notes)) {
	// 		$ret = true;
	// 		if (empty($notes)) {
	// 			if (!empty($workflowComments)) {
	// 				$ret &= $this->saveActualLog($Model, $saveData, $note);
	// 			}
	// 		}
	// 		else {
	// 			foreach ($notes as $note) {
	// 				$ret &= $this->saveActualLog($Model, $saveData, $note);
	// 			}	
	// 		}
			

	// 		return $ret;
	// 	}


	// 	$saveData['notes'] = $notes;

	// 	$Model->SystemRecord->create();
	// 	return $Model->SystemRecord->save($saveData);
	// }

	/**
	 * Purges remaining runtime model data after system record save.
	 */
	// 
	// private function cleanupRuntime(Model $Model) {
	// 	$this->runtime[$Model->alias]['notes']['default'] = array();
	// 	$this->runtime[$Model->alias]['notes'][$Model->id] = array();
	// 	$this->runtime[$Model->alias]['latestTitle'][$Model->id] = array();
	// 	$this->runtime[$Model->alias]['workflowComment']['default'] = array();
	// 	$this->runtime[$Model->alias]['workflowComment'][$Model->id] = array();
	// 	unset(self::$Models[$Model->alias]);
	// }
	/**
	 * Retrieve current item data based on $id variable in $Model.
	 */
	// protected function getDbRecord(Model $Model) {
	// 	$data = $Model->find('first', array(
	// 		'conditions' => array(
	// 			$Model->alias . '.id' => $Model->id
	// 		),
	// 		'recursive' => -1
	// 	));

	// 	$this->dbRecord[$Model->alias] = $data;

	// 	return $data;
	// }

	/**
	 * Parse dbRecord array and pull title value.
	 */
	// protected function getTitleFromDbRecord(Model $Model, $dbRecord) {
	// 	if ($dbRecord && isset($dbRecord[$Model->name]) && isset($dbRecord[$Model->name][$Model->mapping['titleColumn']])) {
	// 		return $dbRecord[$Model->name][$Model->mapping['titleColumn']];
	// 	}
	// 	else {
	// 		return null;
	// 	}

	// 	return null;
	// }

	// protected function getTitleFromData(Model $Model) {
	// 	if (!empty($Model->mapping['titleColumn'])) {
	// 		if (isset($Model->data[$Model->alias][$Model->mapping['titleColumn']])) {
	// 			return $Model->data[$Model->alias][$Model->mapping['titleColumn']];
	// 		}
	// 	}

	// 	return false;
	// }

	/**
	 * Store title of an item before it was saved.
	 */
	// private function setLatestTitle(Model $Model) {
	// 	if (!isset($this->runtime[$Model->alias]['latestTitle'][$Model->id])) {
	// 		$dbRecord = $this->getDbRecord($Model);
	// 		$this->runtime[$Model->alias]['latestTitle'][$Model->id] = $this->getTitleFromDbRecord($Model, $dbRecord);
	// 		//$this->addNoteToLog($Model, __('Title of the item has been changed'));
	// 	}
	// }

	/**
	 * Handler for adding note or array of notes for next record.
	 *
	 * @param mixed $data Note or array of notes.
	 */
	public function addNoteToLog(Model $Model, $data) {
		return true;
	// 	if (is_array($data)) {
	// 		foreach ($data as $key => $val) {
	// 			if (is_array($val)) {
	// 				//future placeholder for possible note options.
	// 			}
	// 			else {
	// 				$this->addNote($Model, $val);
	// 			}
	// 		}
	// 	}
	// 	else {
	// 		$this->addNote($Model, $data);
	// 	}

	}

	/**
	 * Add a single note to existing array of notes.
	 *
	 * @param string $note  Text of a note.
	 */
	// private function addNote(Model $Model, $note) {
	// 	$id = 'default';
	// 	if (!empty($Model->id)) {
	// 		$id = $Model->id;
	// 	}

	// 	if (!isset($this->runtime[$Model->alias]['notes'][$id])) {
	// 		$this->runtime[$Model->alias]['notes'][$id] = array();
	// 	}

	// 	$this->runtime[$Model->alias]['notes'][$id][] = $note;
	// }

	/**
	 * Retrieve notes previously inserted into runtime by model.
	 */
	// private function getNotes(Model $Model) {
	// 	$notesData = $this->runtime[$Model->alias]['notes'];

	// 	if (!isset($notesData[$Model->id])) {
	// 		$notesData[$Model->id] = array();
	// 	}

	// 	$notes = array_unique(array_merge($notesData[$Model->id], $notesData['default']));
	// 	// $notes = $this->formatText($notes);
	// 	return $notes;
	// }

	/**
	 * Handler for adding comment or array of comment for workflow for next record.
	 *
	 * @param mixed $data Comment or array of comments.
	 */
	// public function addWorkflowCommentToLog(Model $Model, $data) {
	// 	if (is_array($data)) {
	// 		foreach ($data as $key => $val) {
	// 			if (is_array($val)) {
	// 				//future placeholder for possible comment options.
	// 			}
	// 			else {
	// 				$this->addWorkflowComment($Model, $val);
	// 			}
	// 		}
	// 	}
	// 	else {
	// 		$this->addWorkflowComment($Model, $data);
	// 	}
	// }

	/**
	 * Insert a workflow comment into runtime for later system record usage.
	 */
	// private function addWorkflowComment(Model $Model, $comment) {
	// 	if(empty($comment)){
	// 		$comment = __('No comment provided');
	// 	}
	// 	$id = 'default';
	// 	if (!empty($Model->id)) {
	// 		$id = $Model->id;
	// 	}

	// 	if (!isset($this->runtime[$Model->alias]['workflowComment'][$id])) {
	// 		$this->runtime[$Model->alias]['workflowComment'][$id] = array();
	// 	}

	// 	if (in_array($comment, $this->runtime[$Model->alias]['workflowComment'][$id])) {
	// 		return false;
	// 	}

	// 	$this->runtime[$Model->alias]['workflowComment'][$id][] = $comment;
	// }

	/**
	 * Retrieve workflow comments from runtime.
	 */
	// private function getWorkflowComments(Model $Model) {
	// 	$workflowData = $this->runtime[$Model->alias]['workflowComment'];

	// 	if (!isset($workflowData[$Model->id])) {
	// 		$workflowData[$Model->id] = array();
	// 	}

	// 	$workflowComments = array_unique(array_merge($workflowData[$Model->id], $workflowData['default']));
	// 	$workflowComments = $this->formatText($workflowComments);
	// 	return $workflowComments;
	// }

	/**
	 * Add an item into query for saving a system record later at once.
	 */
	// private function addToQueue(Model $Model, $params) {
	// 	if (!isset(self::$Models[$Model->alias])) {
	// 		self::$Models[$Model->alias] = &$Model;
	// 	}

	// 	$params['defaultNotes'] = $this->runtime[$Model->alias]['notes']['default'];
	// 	$this->queue[$Model->alias][] = $params;
	// 	$this->runtime[$Model->alias]['notes']['default'] = array();
	// 	$this->runtime[$Model->alias]['workflowComment']['default'] = array();
	// }

	/**
	 * @deprecated
	 */
	// public function setNotes(Model $Model, $notes) {
	// 	$this->notes = $notes;
	// }

	/**
	 * Format array of notes data to readable text.
	 */
	// private function formatText($text = array()) {
	// 	if (is_array($text)) {
	// 		$text = implode('<br />', $text);
	// 	}

	// 	return $text;
	// }

	/**
	 * Set system record from any available model.
	 * @param int $id         Item ID.
	 * @param int $type       1-created, 2-updated, 3-deleted.
	 * @param array  $options Custom options.
	 */
	public function setSystemRecord(Model $Model, $id = null, $type, $options = array(), $forceInstantLog = false) {
		// if (empty($id) && !empty($Model->id)) {
		// 	$id = $Model->id;
		// }
		// if (empty($id) && empty($Model->id)) {
		// 	return true;
		// }

		// if (isset($Model->data[$Model->name]['_workflow_comment'])) {
		// 	//$this->addWorkflowComment($Model, $Model->data[$Model->name]['_workflow_comment']);
		// }

		// if (isset($Model->data[$Model->name]['_notes'])) {
		// 	$this->addNote($Model, $Model->data[$Model->name]['_notes']);
		// }

		// $this->addToQueue($Model, array(
		// 	'model' => $Model->alias,
		// 	'id' => $id,
		// 	'type' => $type,
		// 	'options' => $options
		// ));

		// if ($forceInstantLog) {
		// 	$Model->id = $id;
		// 	return $this->logSystemRecord($Model, $type, $options, true);
		// }

		return true;
	}

	/**
	 * Manage system records creation for model items.
	 */
	public function handleSystemRecords(Model $Model) {
		return true;
		// $ret = true;

		// if (!empty($this->queue[$Model->alias])) {
		// 	$used = array();
		// 	foreach ($this->queue[$Model->alias] as $item) {
		// 		if (in_array($item['id'], $used)) {
		// 			continue;
		// 		}

		// 		$used[] = $item['id'];

		// 		$Model->id = $item['id'];
		// 		if (!empty($item['defaultNotes'])) {
		// 			$this->addNoteToLog($Model, $item['defaultNotes']);
		// 		}

		// 		$this->setLatestTitle($Model);
		// 		$ret = $this->logSystemRecord($Model, $item['type'], isset($item['options'])?$item['options']:array());
		// 	}
		// }

		// return $ret;
	}

}
