<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');

/**
 * VisualisationBehavior
 */
class VisualisationBehavior extends ModelBehavior {

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'enabled' => true
	);

	public $settings = [];

	public $_runtime = [
		// Temporary - stored scope from the ACO model's Tree behavior
		'stored_tree_scope' => null
	];

	protected $_foreignKeyColumn = 'foreign_key';

	/**
	 * Setup
	 *
	 * @param Model $Model
	 * @param array $settings
	 * @throws RuntimeException
	 * @return void
	 */
	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
		}

		$this->_ensureBehaviors($Model);
	}

	/**
	 * Make sure all required behaviors are loaded with this functionality.
	 */
	protected function _ensureBehaviors(Model $Model) {
		// if ACL settings is already set, lets check if they are compatible with visualisations
		if (isset($Model->Behaviors->Acl->settings[$Model->alias])) {
			$type = $Model->Behaviors->Acl->settings[$Model->alias]['type'];

			if (!in_array($type, ['controlled', 'both'])) {
				trigger_error(sprintf('Model %s is not compatible with Visualisation feature', $Model->alias));
			}
		} else {
			// temporary fix
			if ($Model->alias != 'User' && $Model->alias != 'VisualisationUser') {
				// make sure that ACL is initialized even if Visualisation is not explicitly enabled
				// for object nodes to be functional
				$Model->Behaviors->load('Acl', array('type' => 'controlled'));
			}
		}
	}

	// self-share
	public function afterSave(Model $Model, $created, $options = array()) {
		$ret = true;

		if ($created) {
			$VisualisationShareUser = ClassRegistry::init('Visualisation.VisualisationShareUser');
			$ret &= $VisualisationShareUser->share($Model->currentUser('id'), [$Model->alias, $Model->id], false);
		}
	}

	/**
	 * Aco section-based parent node.
	 * Model needs to implement InheritanceInterface.
	 * This method acts as a definition of parent node for a sub-section in the app, i.e Reviews, Audits, etc.
	 * Acl sync uses this method to correctly setup parent ID in the Aco Tree.
	 */
	public function visualisationParentNode(Model $Model, $foreignKeyColumn = null) {
		if ($foreignKeyColumn === null) {
			return 'visualisation/objects';
		}
		
		if (!$Model->id && empty($Model->data)) {
			return null;
		}
		if (isset($Model->data[$Model->alias][$foreignKeyColumn])) {
			$parentId = $Model->data[$Model->alias][$foreignKeyColumn];
		}
		else {
			if ($Model->Behaviors->enabled('SoftDelete')) {
				$configSoftDelete = $Model->softDelete(null);
				$Model->softDelete(false);
			}
			$parentId = $Model->field($foreignKeyColumn);
			if ($Model->Behaviors->enabled('SoftDelete')) {
				$Model->softDelete($configSoftDelete);
			}
		}
		if (!$parentId) {
			return null;
		}
		else {
			return array($Model->parentModel() => array($Model->{$Model->parentModel()}->primaryKey => $parentId));
		}
		/*
		if (!$Model->id && empty($Model->data)) {
            return $Model->{$Model->parentModel()}->parentNode();
        }

        if (isset($Model->data[$Model->alias][$foreignKeyColumn])) {
            $foreignKey = $Model->data[$Model->alias][$foreignKeyColumn];
        }
        else {
        	$Model->softDelete(false);
            $foreignKey = $Model->field($foreignKeyColumn);
        	$Model->softDelete(true);
        }

        if (!$foreignKey) {
            return null;
        }
        else {
            return [
                $Model->parentModel() => [
                    $Model->{$Model->parentModel()}->primaryKey => $foreignKey
                ]
            ];
        }*/
	}

	public function parentSectionNode(Model $Model) {
		if ($Model instanceof InheritanceInterface) {
			return [
				$Model->parentModel() => [
					ClassRegistry::init($Model->parentModel())->primaryKey => null
				]
			];
		}

		return 'visualisation/models';
	}

	/**
	 * Model accessible ACL method to define parent node.
	 * 
	 * @return array Aco node.
	 * @deprecated Use only $Model->parentNode() and work with that independently.
	 */
	public function sectionNode(Model $Model) {
		// Get the section's root node
		$node = ClassRegistry::init('Aco')->node($Model->parentNode('Aco'));
		if ($node !== false) {
			return $node[0][ClassRegistry::init('Aco')->alias][ClassRegistry::init('Aco')->primaryKey];
		}

		return false;
	}

	// count if there are any children (section's objects) where to check permissions
	public function countControlled(Model $Model, $filter = null) {
		$count = $this->findControlledObjects($Model, 'count', $filter);

		// for debugging lets check if the node count matches the count of real objects
		if (Configure::read('debug')) {
			if ($count != $this->countRealObjects($Model)) {
				trigger_error('Nodes and real objects are not in sync! Counts are not equal and they should be.');
			}
		}

		return $count;
	}

	// get IDs of objects with acl nodes
	public function getControlled(Model $Model, $filter = null) {
		$objectIds = [];
		$cacheKey = 'aco_objects_' . $Model->alias;
		
		// if (($objectIds = Cache::read($cacheKey, 'visualisation')) === false) {
			// get direct children - section's objects
			$objects = $this->findControlledObjects($Model, 'all', $filter);
			$objectIds = Hash::extract($objects, '{n}.' . ClassRegistry::init('Aco')->alias . '.' . $this->_foreignKeyColumn);

			// Cache::write($cacheKey, $objectIds, 'visualisation');
		// }

		return $objectIds;
	}

	/**
	 * Wrapper method that finds Acos section objects based on parent (section root) node.
	 * 
	 * @param  Model   $Model    Model.
	 * @param  string  $findType Find type either 'all' or 'count'.
	 * @param  boolean $direct   TBD.
	 * @return mixed             Array of objects for $findType = 'all', or integer count of objects.
	 */
	public function findControlledObjects(Model $Model, $findType, $direct = false, $filter = null) {
		$parentNode = $this->sectionNode($Model);

		$conditions = [
			'`Aco`.`model`' => $Model->alias,
			'`Aco`.`foreign_key` IS NOT NULL'
		];

		if ($filter !== null) {
			$conditions['Aco.foreign_key'] = $filter;
		}

		$ret = ClassRegistry::init('Aco')->children([
			'id' => $parentNode,
			'conditions' => $conditions
		], $direct, [$this->_foreignKeyColumn]);

		if ($findType === 'count') {
			$ret = count($ret);
		}

		return $ret;
	}

	/**
	 * Get the column name for a SoftDelete functionality
	 * 
	 * @param  Model  $Model Model
	 * @return mixed         String with a column name, False otherwise.
	 */
	public function getSoftDeleteColumn(Model $Model) {
		if ($Model->Behaviors->enabled('SoftDelete')) {
			$keys = array_keys($Model->Behaviors->SoftDelete->settings[$Model->alias]);
			$keys = array_values($keys);

			return $keys[0];
		}

		return false;
	}

	// get the count of real objects present in the database model table, not using nodes
	public function countRealObjects(Model $Model, $filter = null) {
		$query = [
			'recursive' => -1
		];
		
		$deletedCol = $Model->getSoftDeleteColumn();
		if ($deletedCol !== false) {
			$query['conditions'] = [
				$Model->alias . '.' . $deletedCol => [true, false]
			];
		}

		if ($filter !== null) {
			$query['conditions'][$Model->alias . '.' . $Model->primaryKey] = $filter;
		}
		
		return $Model->find('count', $query);
	}
}
