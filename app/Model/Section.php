<?php
class Section extends AppModel {
	public $recursive = -1;
	
	public $actsAs = array(
		'Acl' => array('type' => 'controlled')
	);

	/**
	 * Variable holds instance of the Visualisation class.
	 * 
	 * @var null|Visualisation
	 */
	protected $Visualisation = null;

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Sections');
		parent::__construct($id, $table, $ds);
	}

	public function beforeSave($options = array()) {
		return true;
	}

	public function parentNode($type) {
		if ($this->Visualisation === null) {
			$this->Visualisation = new Visualisation();
		}

		return $this->Visualisation->rootNode;
	}

	// gets the id for model alias, creates one if missing
	public function getForeignKey($alias) {
		if ($this->sectionExists($alias) === false) {
			$data = array(
				'Section' => array(
					'model' => $alias
			));
			$this->create();
			$this->save($data);
		}

		$data = $this->find('first', [
			'conditions' => $this->modelConds($alias)
		]);

		return $data['Section']['id'];
	}

	// checks if the specified section exists in the database
	public function sectionExists($alias) {
		return (bool) $this->find('count', [
			'conditions' => $this->modelConds($alias)
		]);
	}

	public function modelConds($alias) {
		return [
			'Section.model' => $alias
		];
	}
}
