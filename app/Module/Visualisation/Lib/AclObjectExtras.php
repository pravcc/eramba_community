<?php
/**
 * Visualisation Library Class.
 */

App::uses('AclExtras', 'AclExtras.Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('VisualisationSetting', 'Visualisation.Model');
App::uses('AppAclComponent', 'Controller/Component');
App::uses('AclShell', 'Console/Command');

/**
 * Shell for working with database objects inside ACL.
 *
 * @package		Visualisation.Lib
 */
class AclObjectExtras extends AclExtras {
	public $AppAcl;
	/**
	 * Start up And load Acl Component / Aco model / Aro model
	 *
	 * @return void
	 **/
	public function startup($controller = null) {
		if (!$controller) {
			$controller = new Controller(new CakeRequest());
		}
		parent::startup($controller);

		$this->Aro = $this->Acl->Aro;
		$this->Aco = $this->Acl->Aco;
		$this->AclShell = new AclShell();

		$collection = new ComponentCollection();
		$this->AppAcl = new AppAclComponent($collection);
		$this->AppAcl->startup($controller);
	}

	public function out($msg) {
		$isCli = PHP_SAPI === 'cli';

		if ($isCli && $this->Shell instanceof Shell) {
			return $this->Shell->out($msg);
		}
		elseif (is_object($this->controller->Flash)) {
			// $this->controller->Flash->info($msg);
		}
	}

	public function parseIdentifier($identifier) {
		if ($this->AclShell === null) {
			$this->AclShell = new AclShell();
		}
		
		return $this->AclShell->parseIdentifier($identifier);
	}

	// parse object node array formatted ['model' => ['id' => $value]] into format for $this->_checkNode() - Model::$ID
	public function parseNodeToCheck($node) {
		if (is_array($node)) {
			$values = array_keys($node);
			$model = $values[0];
			$id = $node[$model]['id'];

			return "{$model}::{$id}";
		}

		return $node;
	}

	protected function _checkNode($path, $alias, $parentId = null, $class = 'Aco') {
		$model = $foreignKey = null;

		// 'models' or 'Section.5' or 'Section.' are for $path
		if (!is_array($path)) {
			$path = $this->parseIdentifier($path);
		}
		
		if (is_array($path)) {
			$path = array_values($path);

			list($model, $foreignKey) = $path;
			$path = compact('model', 'foreignKey');
		}

		// model, foreign key related acl
		if ($model !== null) {
			$path = implode('.', $path);

			$node = $this->{$class}->find('all', [
				'conditions' => [
					$class . '.model' => $model,
					$class . '.foreign_key' => $foreignKey
				],
				'recursive' => -1
			]);
		}
		else {
			// path related acl 
			$node = $this->{$class}->node($path);
		}

		if (isset($node[0])) {
			$node = $node[0];
		}

		if (!$node) {
			$this->{$class}->create([
				'parent_id' => $parentId,
				'model' => $model,
				'foreign_key' => $foreignKey,
				'alias' => $alias
			]);

			$node = $this->{$class}->save();
			$node[$class]['id'] = $this->{$class}->id;
			$this->out(__('Created %s node: <success>%s</success>', $class, $path), 1, Shell::VERBOSE);
		} else {
		}


		if (!$node) {
			$this->out(__('Failed processed node %s', $class, $path));
		}
		
		return $node;
	}


	/**
	 * Get the models used with Visualisation and new Custom Roles.
	 * 
	 * @return array Model names.
	 */
	public function getModelsToSync() {
		$liveModels = ClassRegistry::init('Visualisation.VisualisationSetting')->getModelAliases();
		$additionalModels = [
			'BusinessContinuityPlan',
			// 'BusinessContinuityTask',
			'BusinessUnit',
			'Legal',
			'RiskException',
			'PolicyException',
			'ComplianceException',
			'Project',
			'ThirdParty',
			'SecurityIncident'
		];

		return array_unique(array_merge($liveModels, $additionalModels));
	}
	/**
	 * Check a node for existance, create it if it doesn't exist.
	 *
	 * @param string $class    Aco or Aro.
	 * @param string $path
	 * @param string $alias
	 * @param int $parentId
	 * @return array Node array
	 */
	protected function _checkPathNode($class, $path, $alias, $parentId = null) {
		if ($class === 'Aco') {
			return $this->_checkNode($path, $alias, $parentId);
		}

		$node = $this->{$class}->node($path);
		if (!$node) {
			$this->{$class}->create(array('parent_id' => $parentId, 'model' => null, 'alias' => $alias));
			$node = $this->{$class}->save();
			$node[$class]['id'] = $this->{$class}->id;
			$this->out(__('Created %s node: <success>%s</success>', $class, $path), 1, Shell::VERBOSE);
		} else {
			$node = $node[0];
		}
		return $node;
	}

	// check node existence using find method without thowing exception
	public function validateInheritedObject($class, $object) {

		// in case of a section root node, we allow by default
		if ($object[1] === null) {
			return true;
		}

		$Model = ClassRegistry::init($object[0]);
		$Model->Behaviors->disable('SoftDelete');
		
		$Model->id = $object[1];

		$node = $this->{$class}->find('all', [
			'conditions' => [
				$class . '.model' => $object[0],
				$class . '.foreign_key' => $object[1]
			],
			'recursive' => -1
		]);

		if (!$node) {
			$parentNode = $Model->parentNode('Aco');
			$parentId = $parentNode[$Model->parentModel()]['id'];

			$Model->{$Model->parentModel()}->Behaviors->disable('SoftDelete');

			$realObject = $Model->{$Model->parentModel()}->find('count', [
				'conditions' => [
					$Model->{$Model->parentModel()}->escapeField() => $parentId
				],
				'recursive' => -1
			]);

			// nothing exists will just skip and continue
			if (!$realObject) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check a object node for existance, create it if it doesn't exist.
	 *
	 * @param string $class    Aco or Aro.
	 * @param array  $object   Database object in a format: [$model, $foreign_key]
	 * @param string $parentId Parent ID for the current node.
	 * @return array Node array
	 */
	protected function _checkObjectNode($class, $object = [], $parentId = null) {
		$Model = ClassRegistry::init($object[0]);
		// $path = [$object[0] => [$_M->primaryKey => $object[1]]];
		
		if ($Model instanceof InheritanceInterface) {
			$Model->id = $object[1];
			
			// temp find to validate the parent object actually exists in database because inherited data might be not correct about the parent (i.e reviews)
			if ($this->validateInheritedObject($class, $object) === false) {
				trigger_error(sprintf('Object %s.%s does not actually exists in your database, node skipped.', $object[0], $object[1]));

				// act as without any issue to continue
				return true;
			}
			// $hasNode = $this->hasNodeForObject($class, $object);
			// debug($hasNode);exit;
			// $Model->{$Model->parentModel()}
			$parentNode = $this->{$class}->node($Model->parentNode('Aco'));

			$parentId = $parentNode[0][$class]['id'];
			$parentModel = $parentNode[0][$class]['model'];
			$parentForeignKey = $parentNode[0][$class]['foreign_key'];
		}

		$node = $this->{$class}->find('all', [
			'conditions' => [
				$class . '.model' => $object[0],
				$class . '.foreign_key' => $object[1]
			],
			'recursive' => -1
		]);

		if (!$node) {
			$this->{$class}->create(array('parent_id' => $parentId, 'model' => $object[0], 'foreign_key' => $object[1]));
			$node = $this->{$class}->save();
			$node[$class]['id'] = $this->{$class}->id;
			$this->out(__(
				'Created %s node: <success>%s</success>',
				$class,
				sprintf('%s.%s', $object[0], $object[1])),
				1,
				Shell::VERBOSE
			);
		} else {
			$node = $node[0];
		}

		return $node;
	}

}