<?php
App::uses('Component', 'Controller');
App::uses('Review', 'Model');

/**
 * Reviews helper component.
 */
class ReviewsManagerComponent extends Component {
	public $components = ['Crud'];
	public $settings = [];

	/**
	 * Variable holds conventional Review model name required for this feature to work correctly.
	 * 
	 * @var null|string
	 */
	protected $_reviewModel = null;

	protected $_controller = null;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (empty($this->settings)) {
			$this->settings = array(
			);
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);
	}

	/**
	 * Handle correct model name configuration for this class and also for CRUD component.
	 * Reviews are required to have either Model name or ID as a first request parameter.
	 * 
	 * Model name in indexes, add, trash.
	 * ID in edit, delete.
	 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;

		if (!isset($controller->request->params['pass'][0])) {
			trigger_error('ReviewsController $request->params is missing a model name for the parent model which is required for CRUD, Visualisation and entire Reviews functionality to work properly.');

			return true;
		}

		$modelParam = $controller->request->params['pass'][0];

		// in case we have review ID provided as a parameter we transform it into a model name for further use
		if (is_numeric($modelParam)) {
			$controller->loadModel('Review');
			$review = $controller->Review->find('first', [
				'conditions' => [
					'Review.id' => $modelParam
				],
				'fields' => [
					'Review.model'
				],
				'recursive' => -1
			]);

			$modelParam = $review['Review']['model'];
		}

		// temporary hotfix for a case where string suffix 'Review' is already a part of the model name
		if (strpos($modelParam, 'Review') === false) {
			$modelParam = Review::buildModelName($modelParam);
		}

		$this->_reviewModel = $modelParam;
		$this->Crud->useModel($this->_reviewModel);
	}

	/**
	 * Get the conventional model name for the current Reviews instance.
	 * 
	 * @return string Review model name.
	 */
	public function getReviewModel() {
		return $this->_reviewModel;
	}

	/**
	 * Get the related model name for the current Reviews instance.
	 * 
	 * @return string related model name.
	 */
	public function getRelatedModel() {
		return $this->_controller->{$this->getReviewModel()}->getRelatedModel();
	}
}