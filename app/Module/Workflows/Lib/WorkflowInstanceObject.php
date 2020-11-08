<?php
/**
 * @package       Workflows.Lib
 */

App::uses('WorkflowObject', 'Workflows.Lib');
App::uses('WorkflowAccessObject', 'Workflows.Lib');
App::uses('WorkflowAccess', 'Workflows.Lib');

class WorkflowInstanceObject extends WorkflowObject {
	public $model;
	protected $_Access = null;

	public function __construct($data, $model) {
		parent::__construct($data);
		$this->model = $model;

		// $this->_preloadAccesses();
	}

	/**
	 * Set Access class with pre-defined values into this instance for access checks. 
	 * 
	 * @param WorkflowAccessObject $Access Instance of an access object with preloaded values.
	 */
	public function setInstanceAccess(WorkflowAccessObject $Access) {
		$this->_Access = $Access;

		return $this->_Access;
	}
	
	/**
	 * No pending request for this instance.
	 * 
	 * @return boolean True if there is no pending request.
	 */
	public function isStatusNotPending() {
		return !$this->isStatusPending();
	}

	public function isStatusPending() {
		// pending_requests value can be used but only to show it for informational purposes
		//return $this->WorkflowInstance->pending_requests > 0;
		
		return $this->PendingRequest->id !== null;
	}

	public function isLastStage() {
		return $this->getDefaultStep()->isEmpty();
	}

	public function isInitialStage() {
		return $this->getStage()->stage_type == STAGE_INITIAL;
	}

	public function hasRollback() {
		return !$this->getRollbackStep()->isEmpty();
	}

	public function getRollbackStep() {
		return $this->WorkflowStage->RollbackStep;
	}

	/**
	 * Get number of hours before this stage expires.
	 * 
	 * @return int Number of hours floor()-ed.
	 */
	public function stageExpires() {
		if (!$this->hasRollback()) {
			return false;
		}

		$expiresIn = $this->getTimeout() - $this->hoursPassedOnStage();

		return $expiresIn;
	}

	/**
	 * Get the number of hours that already passed on current stage.
	 * 
	 * @return int  Number of hours floor()-ed.
	 */
	public function hoursPassedOnStage() {
		if (!$this->hasRollback()) {
			return false;
		}

		$stageInitDate = $this->WorkflowInstance->stage_init_date;
		$stageInitRound = CakeTime::format('Y-m-d H', CakeTime::fromString($stageInitDate)) . ':00:00';
		$secondsPassed = CakeTime::fromString('now') - CakeTime::fromString($stageInitRound);

		$passed = $secondsPassed / 60 / 60;
		$passed = floor($passed);

		return $passed;
	}

	/**
	 * Get the rounded percentage number for the current expiration period.
	 * 
	 * @return int Percentage number.
	 */
	public function expiresPercentage() {
		return CakeNumber::precision(($this->hoursPassedOnStage()/$this->getTimeout())*100, 0);
	}

	/**
	 * Get the timeout for a rollback step if defined.
	 * 
	 * @return int Number of hours.
	 */
	public function getTimeout() {
		if (!$this->hasRollback()) {
			return false;
		}

		return $this->getRollbackStep()->timeout;
	}

	public function getDefaultStep() {
		return $this->WorkflowStage->DefaultStep;
	}

	public function getStage() {
		return $this->WorkflowStage;
	}

	public function getPendingStage() {
		return $this->PendingRequest->WorkflowStage;
	}

	// next default stage
	public function getNextStage() {
	}

	/**
	 * Is $userId a workflow owner for the section where current instance is initiated on.
	 *
	 * @return boolean True if user is a workflow owner, False otherwise.
	 */
	public function isWorkflowOwner($userId) {
		return $this->_Access->check(
			$userId,
			['WorkflowSetting', $this->WorkflowSetting->id],
			WorkflowAccess::ACCESS_OWNER
		);
	}

	/**
	 * Can $userId call a stage on this current instance.
	 * 
	 * @return boolean True if user can call stage, False otherwise.
	 */
	public function canCallStage($userId) {
		// there is nothing to call on the last stage or
		// calling a stage is not available while there is already a call request pending on this instance
		if ($this->isLastStage() || $this->isStatusPending()) {
			return false;
		}

		$hasAccess = $this->_Access->check(
			$userId,
			['WorkflowStageStep', $this->getDefaultStep()->id],
			WorkflowAccess::ACCESS_CALL
		);

		$hasAccess = $hasAccess || $this->_Access->check(
			$userId,
			['WorkflowStage', $this->getStage()->id],
			WorkflowAccess::ACCESS_OWNER
		);

		return $hasAccess || $this->isWorkflowOwner($userId);
	}

	/**
	 * Can $userId approve a stage on this current instance.
	 *
	 * @param  mixed   $stageId  Null to check the currently pending stage, or Stage ID where to do this check on.
	 * @return boolean True if user can approve a stage, False otherwise.
	 */
	public function canApproveStage($userId, $stageId = null) {
		if ($this->isLastStage() || !$this->isStatusPending()) {
			return false;
		}

		// lets do the check on the currently pending stage if $stageId is not provided
		if ($stageId === null) {
			$stageId = $this->getPendingStage()->id;
		}

		$hasAccess = $this->_Access->check(
			$userId,
			['WorkflowStage', $stageId],
			WorkflowAccess::ACCESS_OWNER
		);

		return $hasAccess || $this->isWorkflowOwner($userId);
	}

	/**
	 * Method checks if current user already approved a pending stage if there is any.
	 * 
	 * @return boolean  True if approved.
	 */
	public function currentUserApproved() {
		if ($this->isStatusPending() && !$this->PendingRequest->UserApproval->isEmpty()) {
			return true;
		}

		return false;
	}

	/**
	 * Count of a users that are appointed as approvers on $stageId.
	 * 
	 * @param  mixed  $stageId Null to count users on the currently pending stage,
	 *                         or provide Stage ID where to do the counting. 
	 * @return int             Count of users.
	 */
	public function countApprovers($stageId = null) {
		if ($stageId === null && !$this->isStatusPending()) {
			trigger_error(__('Counting of approvers is not possible without provided $stageId because there is no pending request to read this information from.'));

			return false;
		}

		if ($stageId === null) {
			$stageId = $this->getPendingStage()->id;
		}

		$countAccess = $this->_Access->count(
			['WorkflowStage', $stageId],
			WorkflowAccess::ACCESS_OWNER
		);

		return $countAccess;
	}


}
