<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('Review', 'Model');
App::uses('UserFieldsBehavior', 'UserFields.Model/Behavior');

/**
 * Reviews
 */
class ReviewsBehavior extends ModelBehavior {

	protected $_runtime = [];

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'enabled' => true,
		'dateColumn' => 'review',
		'userFields' => [],
		'autoCreatedReview' => null
	);

	public $settings = [];

    public $alreadyUpdated = false;

	/**
	 * Setup
	 *
	 * @param Model $Model
	 * @param array $settings
	 * @return void
	 */
	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);

			if ($this->settings[$Model->alias]['autoCreatedReview'] === null) {
				$this->settings[$Model->alias]['autoCreatedReview'] = 'This Review was automatically created when the %s item was created.';
			}
		}

		// $this->_bindReviewModel($Model);
		$this->_addFieldValidation($Model);
		// $this->_updateFieldData($Model);
	}

	/**
	 * Returns the current model's review date column name for the table.
	 * 
	 * @param  Model  $Model Model.
	 * @return string        Review column name.
	 */
	public function getReviewColumn(Model $Model) {
		return $this->settings[$Model->alias]['dateColumn'];
	}

	protected function _updateFieldData(Model $Model) {
		// debug($Model->actsAs);exit;
		$Field = $Model->getFieldDataEntity($this->getReviewColumn($Model));
		debug($Field->isEditable());
	}

	/**
	 * Handles creating and management of reviews data after object is saved.
	 */
	public function afterSave(Model $Model, $created, $options = array()) {
		$ret = true;

		$this->_bindReviewModel($Model);

		$reviewModel = $this->_runtime[$Model->alias]['reviewModel'];
		if ($reviewModel == null) {
			return true;
		}

		$review = null;
        if (isset($Model->data[$Model->alias][$this->getReviewColumn($Model)])) {
            $review = $Model->data[$Model->alias][$this->getReviewColumn($Model)];
        }

        //
        // Add Reviewer data
        $reviewData = [];
    	$reviewData['Reviewer'] = [];
    	$userFields = $this->settings[$Model->alias]['userFields'];

    	foreach ($userFields as $userField) {
    		//
    		// User
    		$userFieldName = $userField;
    		if (!empty($Model->data[$userFieldName][$userFieldName])) {
    			$ufData = $Model->data[$userFieldName][$userFieldName];
    			foreach ($ufData as $ufd) {
    				$prefixedUF = UserFieldsBehavior::getUserIdPrefix() . $ufd['user_id'];
    				if (!in_array($prefixedUF, $reviewData['Reviewer'], true)) {
    					$reviewData['Reviewer'][] = $prefixedUF;
    				}
    			}
    		}
    		//
    		
    		//
    		// Group
    		$groupFieldName = $userField . 'Group';
    		if (!empty($Model->data[$groupFieldName][$groupFieldName])) {
    			$ufData = $Model->data[$groupFieldName][$groupFieldName];
    			foreach ($ufData as $ufd) {
    				$prefixedUF = UserFieldsBehavior::getGroupIdPrefix() . $ufd['group_id'];
    				if (!in_array($prefixedUF, $reviewData['Reviewer'], true)) {
    					$reviewData['Reviewer'][] = $prefixedUF;
    				}
    			}
    		}
    		//
    	}
        //

        if ($created) {
        	$sectionLabel = $Model->label(['singular' => true]);

            // two reviews are created for a new risk
            $ret &= $reviewModel->associateAutoCreatedReview(sprintf(
            	$this->settings[$Model->alias]['autoCreatedReview'],
            	$sectionLabel
            ), $reviewData);
        } elseif (!empty($Model->id) && !$this->alreadyUpdated) {
            // Prevent this snipped to be called again
            $this->alreadyUpdated = true;
            
            //
            // Update User Fields from $reviewData array
            $updatedReviewers = $reviewData['Reviewer'];
            $reviewModel->updateReviews(['Reviewer' => $updatedReviewers], [
                'model' => $Model->alias,
                'foreign_key' => $Model->id,
                'completed' => 0
            ]);
            //
        }

        if ($review !== null) {
            $ret &= $reviewModel->associateReview($review, $reviewData);
        }

        // when deleted, move to trash also it's associated reviews
        if (!$Model->exists($Model->id)) {
        	$related = $this->_runtime[$Model->alias]['reviewModel']->find('list', [
        		'conditions' => [
        			'foreign_key' => $Model->id
        		],
        		'recursive' => -1
        	]);

        	foreach (array_keys($related) as $id) {
        		$ret &= $this->_runtime[$Model->alias]['reviewModel']->delete($id);
        	}
        }

        return $ret;
	}

	/**
	 * Adds a date field validation rules applicable to the reviews funcitonality.
	 * 
	 * @param Model $Model Model where to add the validation.
	 */
	protected function _addFieldValidation(Model $Model) {
		// validation for the model field
		$Model->validate[$this->getReviewColumn($Model)] = [
            'notBlank' => [
                'rule' => 'notBlank',
                'required' => 'create',
                'allowEmpty' => false,
                'message' => __('This field cannot be empty')
            ],
            'date' => [
                'rule' => 'date',
                'message' => __('Enter a valid date')
            ],
            'future' => [
                'rule' => 'validateFutureDate',
                'message' => __('Choose a date in the future')
            ]
        ];
	}

	/**
	 * Method returns the correct Review associated model for the specified model.
	 * 
	 * @param  Model  $Model Current model.
	 * @return Review|null
	 */
	public function getReviewModel(Model $Model) {
		if (!empty($this->_runtime[$Model->alias]['reviewModel'])) {
			return $this->_runtime[$Model->alias]['reviewModel'];
		}

		return null;
	}

	/**
	 * Method that exposes binding of a related Review model to the current model.
	 * 
	 * @param  Model  $Model Current model.
	 * @return void
	 */
	public function bindReviewModel(Model $Model) {
		return $this->_bindReviewModel($Model);
	}

    /**
     * Permanently binds reviews.
     */
    protected function _bindReviewModel(Model $Model) {
    	$this->_runtime[$Model->alias]['reviewModel'] = null;

    	$reviewModel = Review::buildModelName($Model->alias); 
    	$reviewModelClass = ClassRegistry::init($reviewModel);

    	if (!$reviewModelClass instanceof Review) {
    		return false;
    	}

        $Model->bindModel([
            'hasMany' => [
                $reviewModel => [
                    'className' => $reviewModel,
                    'foreignKey' => 'foreign_key',
                    'conditions' => array(
                        $reviewModel . '.model' => $Model->alias
                    )
                ]
            ]
        ], false);

        $this->_runtime[$Model->alias]['reviewModel'] = $Model->{$reviewModel};

        return $this->_runtime[$Model->alias]['reviewModel'];
    }

}