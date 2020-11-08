<?php
App::uses('User', 'Model');

class VisualisationUser extends User {
	public $cacheSources = false;
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		// temporary solution to make update work
		// $this->virtualFields['group_id'] = '0';
	}

	public function bindNode($user) {
		$name = key($user);
		list(, $alias) = pluginSplit($name);
		return [
			'model' => 'User',
			'foreign_key' => $user[$name]['id']
		];
    }

    // temporarily for compatibility with updater to rls 49
    // otherwise parent::parentNode();
    public function parentNode($type) {
		if (!$this->id && empty($this->data)) {
			return null;
		}

		$groups = array();
		if (isset($this->data['Group']['Group']) && is_array($this->data['Group']['Group'])) {
			$groups = $this->data['Group']['Group'];
		}
		else {
			$groups = $this->find('first', array(
				'conditions' => array(
					$this->alias . '.id' => $this->id
				),
				'fields' => [
					$this->alias . '.id'
				],
				'contain' => array(
					'Group' => array(
						'fields' => array(
							'Group.id'
						)
					)
				)
			));

			$groups = Hash::extract($groups, 'Group.{n}.id');
		}
		if (empty($groups)) {
			return null;
		}
		else {
			return array('Group' => array('id' => $groups));
		}
	}
}
