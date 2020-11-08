<?php
class Cron extends AppModel {
	/**
	 * Maximum number in seconds on how long would take for "Cron is running at the moment" to release.
	 */
	const CRON_RUNNING_TOLERANCE = 60;

	public $useTable = 'cron';
	protected $allowedTypes = array(self::TYPE_HOURLY, self::TYPE_DAILY, self::TYPE_YEARLY);

	public $actsAs = array(
		'FieldData.FieldData',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array()
		),
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters'
	);

	public $hasMany = [
		'CronTask' => [
			'className' => 'Cron.CronTask'
		]
	];

	public $mapController = 'cron';

	/**
	 * The requestId is a unique ID generated once per CRON job to allow multiple record changes to be grouped by request
	 *
	 * @var string
	 */
	protected static $_requestId = null;

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Crons');
		$this->_group = parent::SECTION_GROUP_SYSTEM;

        $this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];
		
		$this->fieldData = array(
			'type' => [
				'label' => __('Type'),
				'editable' => false,
				'description' => __('Select type'),
				'options' => [$this, 'getCronTypes']
			],
			'execution_time' => [
				'label' => __('Execution time'),
				'editable' => false,
				'description' => __('Execution time')
			],
			'status' => [
				'label' => __('Status'),
				'editable' => false,
				'description' => __('Select status'),
				'options' => [$this, 'getCronStatuses']
			],
			'created' => [
				'label' => __('Created'),
				'editable' => false
			],
			'url' => [
				'label' => __('URL'),
				'editable' => false
			],
			'message' => [
				'label' => __('Message'),
				'editable' => false
			]
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Cron'),
			'pdf_file_name' => __('crons'),
			'csv_file_name' => __('crons'),
			'actions' => false,
			'url' => array(
				'controller' => 'cron',
				'action' => 'index',
				'?' => array(
					'advanced_filter' => 1
				)
			),
			'reset' => array(
				'controller' => 'cron',
				'action' => 'index',
			),
			'include_timestamps' => false,
			'use_new_filters' => true
		);

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->dateField('created', [
					'showDefault' => true,
					'label' => __('Date')
				])
				->selectField('type', [$this, 'types'], [
					'showDefault' => true
				])
				->numberField('execution_time', [
					'showDefault' => true
				])
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true
				])
				->textField('message', [
					'showDefault' => true
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	/**
	 * Get request ID
	 *
	 * @return null|string The request ID.
	 */
	public static function requestId() {
		return self::$_requestId;
	}

	// set unique request it from cron crud action
	public static function setRequestId($uuid) {
		self::$_requestId = $uuid;
	}

	/**
	 * @deprecated use the static method below instead
	 */
	function getCronTypes() {
		return self::types();
	}

	// possible types of cron jobs
	public static function types($value = null) {
		$options = array(
			self::TYPE_HOURLY => __('Hourly'),
			self::TYPE_DAILY => __('Daily'),
			self::TYPE_YEARLY => __('Yearly')
		);
		return parent::enum($value, $options);
	}
	const TYPE_HOURLY = 'hourly';
	const TYPE_DAILY = 'daily';
	const TYPE_YEARLY = 'yearly';

	/**
	 * @deprecated use the static method below instead
	 */
	function getCronStatuses() {
		return self::statuses();
	}

	// possible statuses that cron job can result in
	public static function statuses($value = null) {
		$options = array(
			self::STATUS_SUCCESS => __('Success'),
			self::STATUS_ERROR => __('Error'),
			self::STATUS_PENDING => __('Pending'),
		);
		return parent::enum($value, $options);
	}
	const STATUS_SUCCESS = 'success';
	const STATUS_ERROR = 'error';
	const STATUS_PENDING = 'pending';

	public function summarizeCron($id)
	{
		$hasIssues = $this->CronTask->find('count', [
			'conditions' => [
				'cron_id' => $id,
				'status' => CronTask::STATUS_ERROR
			],
			'recursive' => -1
		]);

		if ($hasIssues) {
			$status = Cron::STATUS_ERROR;
			$listMessages = $this->CronTask->find('list', [
				'conditions' => [
					'cron_id' => $id,
					'status' => CronTask::STATUS_ERROR
				],
				'fields' => [
					'message'
				],
				'recursive' => -1
			]);

			$message = implode(PHP_EOL, $listMessages);
		}
		else {
			$status = Cron::STATUS_SUCCESS;
			$message = __('Your CRON Job has been completed successfully');
		}

		$executionTimeTotal = $this->CronTask->find('first', [
			'conditions' => [
				'cron_id' => $id
			],
			'fields' => [
				'SUM(CronTask.execution_time) as sum_execution_time'
			],
			'recursive' => -1
		]);

		$this->set([
			'id' => $id,
			'status' => $status,
			'execution_time' => $executionTimeTotal[0]['sum_execution_time'],
			'completed' => CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now')),
			'message' => $message
		]);
		$this->save();

		$data = $this->find('first', [
			'conditions' => [
				'id' => $id
			],
			'recursive' => -1
		]);

		$log = 'Cron type "'.$data['Cron']['type'].'" has been finished with status - ' . Cron::statuses($status) . ' and took ' . $executionTimeTotal[0]['sum_execution_time'] . 's in total';
		CakeLog::write('Cron', $log);
	}

	public function handleTasks(Controller $Controller, $id)
	{
		$data = $this->find('first', [
			'conditions' => [
				'id' => $id
			],
			'recursive' => -1
		]);

		$requestId = $data['Cron']['request_id'];

		$allTasks = CronModule::$jobs[$data['Cron']['type']];

		// check all listeners
		$taskList = $this->CronTask->find('list', [
			'conditions' => [
				'CronTask.cron_id' => $id,
				'CronTask.task' => $allTasks
			],
			'fields' => [
				'task'
			],
			'recursive' => -1
		]);

		$nextTasks = array_diff($allTasks, $taskList);

		// all tasks finished, update main cron with the final results
		if (empty($nextTasks)) {
			$this->summarizeCron($id);
		}
		// else run the next task
		else {
			$nextTask = reset($nextTasks);

			CronModule::taskRequest($Controller, $requestId, $nextTask);
		}
	}

	public function isCronTaskRunning($type) {
		// do not manage this during hourly cron
		// if ($type == self::TYPE_HOURLY) {
		// 	return false;
		// }

		// for debugging purposes it is possible to init a cron job anytime without restriction
		if (Configure::read('Eramba.CRON_DISABLE_VALIDATION')) {
			return false;
		}

		$conds = $this->_getCheckConds($type);
		$conds['Cron.status'] = self::STATUS_PENDING;

		$cron = $this->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $cron;

		// $cacheStr = 'cron_type_' . $type;
		// if (($data = Cache::read($cacheStr, 'cron')) === false) {
		// 	return false;
		// }

		// if ($this->getSecondsToReleaseCron($type, $data) < 0) {
		// 	return false;
		// }
		
		// return $data;
	}

	public function getSecondsToReleaseCron($type, $data) {
		return $data - strtotime('now') + self::CRON_RUNNING_TOLERANCE;
	}

	public function setCronTaskAsRunning($type, $running = true) {
		return $this->saveCronTaskRecord($type, self::STATUS_PENDING);
		// do not manage this during hourly cron
		// if ($type == self::TYPE_HOURLY) {
		// 	return true;
		// }

		// $cacheStr = 'cron_type_' . $type;

		// if (empty($running)) {
		// 	return Cache::delete($cacheStr, 'cron');
		// }

		// return Cache::write($cacheStr, strtotime('now'), 'cron');
	}

	protected function _getCheckConds($type)
	{
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		$hourAgo = CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('-50 minutes'));

		$conds = array(
			'Cron.type' => $type
		);

		if ($type == self::TYPE_HOURLY) {
			$conds['Cron.created >'] = $hourAgo;
		}

		if ($type == self::TYPE_DAILY) {
			$conds['DATE(Cron.created)'] = $today;
		}

		if ($type == self::TYPE_YEARLY) {
			$conds['YEAR(Cron.created)'] = CakeTime::format('Y', CakeTime::fromString('now'));
		}

		return $conds;
	}

	/**
	 * Checks if a certain cron task already exists according to a condition.
	 */
	public function cronTaskExists($type) {
		// for debugging purposes it is possible to init a cron job anytime without restriction
		if (Configure::read('Eramba.CRON_DISABLE_VALIDATION')) {
			return false;
		}

		$conds = $this->_getCheckConds($type);
		$conds['Cron.status'] = 'success';

		$data = $this->find('count', array(
			'conditions' => $conds,
			'recursive' => -1
		));

		return $data;
	}

	/**
	 * Save info record about a cron task that was run.
	 */
	public function saveCronTaskRecord($type = self::TYPE_DAILY, $status = self::STATUS_SUCCESS, $message = null) {
		if (!in_array($type, $this->allowedTypes)) {
			return false;
		}

		$data = array(
			'type' => $type,
			'execution_time' => scriptExecutionTime(),
			'status' => $status,
			'request_id' => self::requestId(),
			'url' => Router::fullBaseUrl(),
			'message' => $message
		);

		$this->create();
		$this->set($data);
		$ret = $this->save(null, false);
		// $ret &= $this->setCronTaskAsRunning($type, false);

		return $ret;
	}

	public function getFailedJobIds() {
		return $this->find('list', array(
			'conditions' => array(
				'Cron.status' => self::STATUS_ERROR
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));
	}
}
