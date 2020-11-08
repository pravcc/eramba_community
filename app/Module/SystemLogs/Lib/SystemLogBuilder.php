<?php
App::uses('ModuleBase', 'Lib');

/**
 * Builder for SystemLogsBehavior.
 */
class SystemLogBuilder extends ModuleBase
{
	protected $_action = null;
	protected $_model = null;
	protected $_foreignKey = null;
	protected $_subModel = null;
	protected $_parentForeignKey = null;
	protected $_message = null;
	protected $_result = null;
	protected $_userId = null;

	public function __construct($model = null, $action = null, $foreignKey = null, $result = null, $message = null, $subModel = null, $subForeignKey = null, $userId = null)
	{
		$this->_action = $action;
		$this->_model = $model;
		$this->_foreignKey = $foreignKey;
		$this->_subModel = $subModel;
		$this->_subForeignKey = $subForeignKey;
		$this->_message = $message;
		$this->_result = $result;
		$this->_user = $userId;

		return $this;
	}

	/**
	 * Set action.
	 * 
	 * @param  int $action System log action.
	 * @return SystemLogBuilder
	 */
	public function action($action)
	{
		$this->_action = $action;

		return $this;
	}

	/**
	 * Set subject - SystemLog.model, SystemLog.foreign_key.
	 * 
	 * @param  Model $model Subject Model.
	 * @param  int $foreignKey Subject Id.
	 * @return SystemLogBuilder
	 */
	public function subject($model, $foreignKey = null)
	{
		$this->_model = $model;
		
		if ($foreignKey !== null) {
			$this->_foreignKey = $foreignKey;
		}

		return $this;
	}

	/**
	 * Set foreign key.
	 * 
	 * @param  int $foreignKey Subject Id.
	 * @return SystemLogBuilder
	 */
	public function foreignKey($foreignKey)
	{
		$this->_foreignKey = $foreignKey;

		return $this;
	}

	/**
	 * Set secondary subject - SystemLog.sub_model, SystemLog.sub_foreign_key.
	 * 
	 * @param  Model $model Secondary Subject Model.
	 * @param  int $foreignKey Secondary Subject Id.
	 * @return SystemLogBuilder
	 */
	public function subSubject($model, $foreignKey = null)
	{
		$this->_subModel = $model;

		if ($foreignKey !== null) {
			$this->_subForeignKey = $foreignKey;
		}
		else {
			$this->attachSubSubject($this->_subModel);
		}

		return $this;
	}

	/**
	 * Auto attach of Secondary Subject Id.
	 * 
	 * @param  Model $model Secondary Subject Model.
	 * @return SystemLogBuilder
	 */
	public function attachSubSubject($model)
	{
		$this->_subModel = $model;

		$assoc = $this->_model->getAssociated($this->_subModel->alias);

		if (!empty($this->_model) && !empty($this->_foreignKey) && !empty($assoc['association'])) {
			if ($assoc['association'] == 'belongsTo') {
				$this->_subForeignKey = $this->_model->field($assoc['foreignKey'], ['id' => $this->_foreignKey]);
			}
		}

		return $this;
	}

	/**
	 * Set message or message params.
	 * 
	 * @param  mixed $message Message or message params.
	 * @return SystemLogBuilder
	 */
	public function message($message)
	{
		$this->_message = $message;

		return $this;
	}

	/**
	 * Set log result data.
	 * 
	 * @param  string|int $result Result data.
	 * @return SystemLogBuilder
	 */
	public function result($result)
	{
		$this->_result = $result;

		return $this;
	}

	/**
	 * Set user id.
	 * 
	 * @param int $userId User id.
	 * @return SystemLogBuilder
	 */
	public function userId($userId)
	{
		$this->_userId = $userId;

		return $this;
	}

	/**
	 * Execute log action.
	 * 
	 * @return boolean Success.
	 */
	public function log()
	{
		$log = $this->_model->systemLog(
			$this->_action,
			$this->_foreignKey,
			$this->_result,
			$this->_message,
			$this->_subModel,
			$this->_subForeignKey,
			$this->_userId
		);

		return $log;
	}
}
