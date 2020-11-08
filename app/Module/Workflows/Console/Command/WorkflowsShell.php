<?php
App::uses('AppShell', 'Console/Command');
App::uses('ImportToolCsv', 'ImportTool.Lib');
App::uses('ImportToolData', 'ImportTool.Lib');
App::uses('ConnectionManager', 'Model');
App::uses('ErambaCakeEmail', 'Network/Email');

class WorkflowsShell extends AppShell {
	public $uses = array(
		'Workflows.WorkflowSetting',
		'Workflows.WorkflowInstance'
	);

	public function startup() {
		parent::startup();

		App::uses('AuthComponent', 'Controller/Component');
		App::uses('ComponentCollection', 'Controller');
		App::uses('Controller', 'Controller');
		$c = new ComponentCollection();
		$c->setController(new Controller());
		$u = (new AuthComponent($c))->login([
			'id' => ADMIN_ID
		]);
	}

	/**
 * Get the option parser.
 *
 * @return void
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(
				'Workflows Shell.' .
				'')
			// ->addOption('instance', array(
			// 	'short' => 'i',
			// 	'required' => true,
			// 	'help' => __d('migrations', 'Workflow Instance ID')))
			->addSubcommand('status', array(
				'help' => __('Display a status of workflows in the system.')))
			->addSubcommand('trigger', array(
				'help' => __('List of possible stages to trigger for a workflow instance.')))
			->addSubcommand('force', array(
				'help' => __('Switch stage for a workflow instance.')))
			// ->addSubcommand('call_stage', array(
			// 	'help' => __d('migrations', 'Calls a stage_ID.')))
			// ->addSubcommand('approve_stage', array(
			// 	'help' => __d('migrations', 'Approves a stage_ID.')))
			->addArgument('instance', [
				'required' => false,
				'help' => 'Skip interactive prompts and specify workflow instance ID argument formatted as alias `Risk.13` or just the `ID` number.'
			]);
			// ->addArgument('StageID', ['required' => true]);
			// ->addArgument('stage_step_ID', ['required' => false]);
	}

/**
 * Override main
 *
 * @return void
 */
	public function main() {
		$this->out($this->getOptionParser()->help());
	}

	protected function showSettings() {
		$data = $this->WorkflowSetting->find('all', [
			'recursive' => -1
		]);

		$this->out(__('List of sections that have Workflows') . ':', 2);

		$ids = [];
		$this->out('ID. Name (status)');
		$this->hr();
		foreach ($data as $key => $item) {
			$id = $item['WorkflowSetting']['id'];
			$ids[$id] = $item['WorkflowSetting']['model'];

			$section = ClassRegistry::init($item['WorkflowSetting']['model'])->label();
			$this->out(sprintf('%d. %s (%s)', $id, $section, WorkflowSetting::statuses($item['WorkflowSetting']['status'])));
		}
		$this->hr();

		return $ids;
	}

	protected function showInstances($model) {
		$data = $this->WorkflowInstance->findList($model);

		$this->out(__('List of Workflow Instances') . ':', 2);
		$this->out('ID. Workflow Instance Item');
		$this->hr();
		$ids = [];
		foreach ($data as $id => $foreignKey) {
			$ids[$id] = $id;
			$Instance = $this->WorkflowInstance->getInstance($model, $foreignKey, false);

			$this->out(sprintf(
				'%d. %s',
				end($ids),
				$Instance->Object->object_model_label . ', ' . $Instance->Object->object_item_label
			));
		}
		$this->hr();

		return $ids;
	}

	protected function parseInstanceArg() {
		$instanceArg = $this->args[0];

		// if ID is provided, we parse it to the node path
		if (is_numeric($instanceArg)) {
			$data = $this->WorkflowInstance->find('first', [
				'conditions' => ['WorkflowInstance.id' => $instanceArg],
				'recursive' => -1
			]);

			$instanceArg = sprintf('%s.%s', $data['WorkflowInstance']['model'], $data['WorkflowInstance']['foreign_key']);
		}
		
		list($model, $foreignKey) = explode('.', $instanceArg);
		if (!$this->WorkflowInstance->itemExists($model, $foreignKey)) {
			$this->error('Wrong argument supplied.', 'Workflow instance record does not exist.');

		}

		$Instance = $this->WorkflowInstance->getInstance($model, $foreignKey, false);

		return $Instance;
	}

	protected function showInstanceDetail() {
		$Instance = $this->parseInstanceArg();

		$this->out(sprintf(
			'Workflow Instance, ID %d., alias %s',
			$Instance->WorkflowInstance->id,
			$Instance->model . '.' . $Instance->Object->id
		));

		$this->hr();

		$this->out('Section: ' . $Instance->Object->object_model_label);
		$this->out('Item: ' . $Instance->Object->object_item_label);

		$this->out(__('Current stage: %s', $Instance->getStage()->name));

		if (!$Instance->isLastStage()) {
			$this->out(__('Next default stage: %s', $Instance->getDefaultStep()->WorkflowNextStage->name));
		}

		if ($Instance->isStatusPending()) {
			$this->out(__('Pending stage: %s', $Instance->getPendingStage()->name));
		}

		if ($Instance->hasRollback()) {
			$expires = $Instance->stageExpires();
			$timeout = CakeTime::timeAgoInWords(strtotime("+{$expires} hours"), array(
				'accuracy' => array('minute' => 'minute')
			));

			$this->out(__('Rollback: %s', $Instance->getRollbackStep()->WorkflowNextStage->name));
			$this->out(__('Timeout: %s', $timeout));
		}

		$this->out();
		$this->hr();

		return $Instance;
	} 

	public function status() {
		if (empty($this->args)) {
			$sectionIds = $this->showSettings();

			$response = $this->in(__('Write a Section ID to show it\'s objects, or put nothing to quit.'), array_keys($sectionIds), 'q');
			if (strtolower($response) === 'q') {
				return false;
			}
			$model = $sectionIds[$response];

			$instanceIds = $this->showInstances($sectionIds[$response]);
			$response = $this->in(__('Put ID of a Workflow Instance for more details.'), array_keys($instanceIds), 'q');
			if (strtolower($response) === 'q') {
				return false;
			}

			$this->args[0] = $instanceIds[$response];
		}

		return $this->showInstanceDetail();
	}

	public function trigger() {
		$Instance = $this->status();

		$this->out('Possible Next Stages:');

		$ids = [];
		foreach ($Instance->WorkflowStage->NextStage as $NextStage) {
			$ids[$NextStage->id] = [$NextStage->id, $NextStage->WorkflowStageStep->id];

			$this->out(sprintf(
				'%d. %s (%s type)',
				$NextStage->id,
				$NextStage->name,
				WorkflowStageStep::stepTypes($NextStage->WorkflowStageStep->step_type)
			));
		}

		if ($Instance->isStatusPending()) {
			$this->err('There is already a trigger request pending at the moment');
			return false;
		}

		$response = $this->in(__('Put ID of the stage to proceed and trigger.'), array_keys($ids), 'q');
		if (strtolower($response) === 'q') {
			return false;
		}

		$this->out('Moment...');
		$trigger = $ids[$response];
		$ret = $this->WorkflowInstance->call_stage($Instance->WorkflowInstance->id, $trigger[0], $trigger[1]);
		if ($ret) {
			$this->out('Trigger stage was successful.');
		}
		else {
			$this->err('Error occured!');
		}
	}

	public function force() {
		$Instance = $this->status();

		$this->out('Stages Available:');

		$ids = [];
		foreach ($this->WorkflowInstance->WorkflowStage->findByModel($Instance->model) as $id => $name) {
			$ids[$id] = $id;

			$this->out(sprintf(
				'#%d. %s',
				$id,
				$name
			));
		}

		$response = $this->in(__('Put ID of the stage to switch it.'), array_keys($ids), 'q');
		if (strtolower($response) === 'q') {
			return false;
		}

		$this->out('Moment...');
		$forceId = $ids[$response];
		$ret = $this->WorkflowInstance->force_stage($Instance->WorkflowInstance->id, $forceId);
		if ($ret) {
			$this->out('Force stage was successful.');
		}
		else {
			$this->err('Error occured');
		}
	}

}