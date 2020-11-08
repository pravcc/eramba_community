<?php
App::uses('AppModel', 'Model');
App::uses('BulkAction', 'BulkActions.Model');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('Hash', 'Utility');

class Review extends AppModel implements InheritanceInterface
{
    public $controllerName = 'Reviews';
    public $displayField = 'planned_date';

    protected $_relatedModel = null;

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

	public $mapping = array(
		'titleColumn' => false,
		'logRecords' => true,
        'notificationSystem' => true,
		'workflow' => false
	);

	public $workflow = array(
		// 'pullWorkflowData' => array('RiskReview', 'ThirdPartyRiskReview', 'BusinessContinuityReview', 'AssetReview', 'SecurityPolicyReview')
	);

    public $actsAs = array(
        'EventManager.EventManager',
        'Search.Searchable',
        'AdvancedQuery.AdvancedFinder',
        'AuditLog.Auditable',
        'Utils.SoftDelete',
        'Acl' => array('type' => 'controlled'),
        'Visualisation.Visualisation',
        'ObjectStatus.ObjectStatus',
        'UserFields.UserFields' => [
            'fields' => [
                'Reviewer'
            ]
        ],
        'Comments.Comments',
        'Attachments.Attachments',
        'Widget.Widget',
        'Macros.Macro',
        'SubSection' => [
            'parentField' => 'foreign_key'
        ],
        'AdvancedFilters.AdvancedFilters',
        'ModuleDispatcher' => [
            'behaviors' => [
                'NotificationSystem.NotificationSystem',
                'Reports.Report',
            ]
        ],
        'CustomLabels.CustomLabels'
    );


	public $belongsTo = array(
		'User'
	);

	public $validate = array(
		'planned_date' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a date'
		),
		'actual_date' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'allowEmpty' => true,
                'on' => 'create'
            ),
            'updateEmpty' => array(
                'rule' => 'notBlank',
                'required' => 'update',
                'on' => 'update',
                'allowEmpty' => false,
                'message' => 'This field cannot be empty'
            ),
            'date' => array(
                'rule' => 'date',
                'message' => 'Enter a valid date'
            ),
            'past' => array(
                'rule' => 'validatePastDate',
                'message' => 'Choose a date in the present or past'
            )
		)
	);

    const STATUS_COMPLETE = REVIEW_COMPLETE;
    const STATUS_NOT_COMPLETE = REVIEW_NOT_COMPLETE;

    public static function statuses($value = null) {
        $options = array(
            self::STATUS_COMPLETE => __('Completed'),
            self::STATUS_NOT_COMPLETE => __('Not Completed'),
        );
        return parent::enum($value, $options);
    }

	public function __construct($id = false, $table = null, $ds = null)
    {
        //
        // Init helper Lib for UserFields Module
        $UserFields = new UserFields();
        //
        
        $this->label = __('Reviews');

        // $this->fieldGroupData = array(
        //     'default' => array(
        //         'label' => __('General')
        //     ),
        //     'security-policy' => array(
        //         'label' => __('Security Policy Updates')
        //     )
        // );

        $this->fieldData = (!empty($this->fieldData)) ? $this->fieldData : array();
        // $this->fieldGroupData = (!empty($this->fieldGroupData)) ? $this->fieldGroupData : array();

        $this->fieldData = am(array(
            'model' => array(
                'label' => __('Related Model'),
                'editable' => false,
                'hidden' => true
            ),
            'foreign_key' => array(
                'label' => __('Related Item ID'),
                'editable' => true,
                'macro' => [
                    'name' => 'related_item_id',
                ],
                'empty' => __('Choose one ...'),
                'renderHelper' => ['Reviews', 'foreignKeyField']
            ),
            'planned_date' => array(
                'label' => __('Planned date'),
                'editable' => false,
            ),
            'actual_date' => array(
                'label' => __('Actual date'),
                'editable' => true,
                'renderHelper' => ['Reviews', 'actualDateField']
            ),
            'Reviewer' => $UserFields->getFieldDataEntityData($this, 'Reviewer', [
                'label' => __('Reviewer'), 
                'description' => __('Select one or more users or groups that have worked on this review record.'),
                'quickAdd' => true,
                'inlineEdit' => true,
                'after' => 'actual_date'
            ]),
            'description' => array(
                'label' => __('Description'),
                'editable' => true,
                'renderHelper' => ['Reviews', 'descriptionField']
            ),
            'completed' => array(
                'label' => __('Completed'),
                'type' => 'toggle',
                'editable' => true,
                'renderHelper' => ['Reviews', 'completedField']
                // 'hidden' => true
            ),
            'version' => array(
                'label' => __('Version'),
                'editable' => false,
                'hidden' => ($this->_relatedModel == 'SecurityPolicy') ? false : true
            ),
        ), $this->fieldData);

		parent::__construct($id, $table, $ds);
     
        $this->reviewFilterSettings = array(
            'bulk_actions' => array(
                BulkAction::TYPE_DELETE
            )
        );

        if ($this->_relatedModel !== null) {
            if (empty($this->belongsTo[$this->_relatedModel])) {
                $this->bindModel(array(
                    'belongsTo' => array(
                        $this->_relatedModel => array(
                            'foreignKey' => 'foreign_key',
                        )
                    )
                ), false);
            }

            $this->_group = $this->{$this->parentModel()}->_group;
        }

        $this->Aco = ClassRegistry::init('Aco');
	}

    protected function _getAdvancedFilterConfig()
    {
        $advancedFilterConfig = $this->createAdvancedFilterConfig()
            ->group('general', [
                'name' => __('General')
            ])
                ->nonFilterableField('id');

        if (!empty($this->_relatedModel)) {
            $RelatedModel = ClassRegistry::init($this->_relatedModel);
            $advancedFilterConfig->multipleSelectField('foreign_key', [$RelatedModel, 'getList'], [
                'label' => $RelatedModel->label(['singular' => true]),
                'showDefault' => true,
            ]);
        }

        $advancedFilterConfig
            ->dateField('planned_date', [
                'showDefault' => true
            ])
            ->dateField('actual_date', [
                'showDefault' => true
            ])
            ->userField('Reviewer', 'Reviewer', [
                'showDefault' => true
            ])
            ->textField('description', [
                'showDefault' => true
            ])
            ->multipleSelectField('completed', [$this, 'getStatusFilterOption'])
            ->objectStatusField('ObjectStatus_expired', 'expired')
            ->objectStatusField('ObjectStatus_current_review', 'current_review');

        $this->otherFilters($advancedFilterConfig);

        return $advancedFilterConfig;
    }

    public function getDisplayFilterFields()
    {
        return ['parent', 'planned_date'];
    }

    public function getObjectStatusConfig() {
        return [
            'expired' => [
                'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'storageSelf' => false,
                'trigger' => [
                    [
                        'model' => $this->{$this->_relatedModel},
                        'trigger' => 'ObjectStatus.trigger.expired_reviews'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'current_review' => [
                'title' => __('Current'),
                'callback' => [$this, 'statusCurrentReview'],
                'type' => 'success',
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'current_review_trigger' => [
                'trigger' => [
                    [
                        'model' => $this->{$this->_relatedModel},
                        'trigger' => 'ObjectStatus.trigger.current_review_trigger'
                    ],
                ],
                'hidden' => true
            ],
        ];
    }

    protected function _getReviewNotificationConfig()
    {
        return [
            'review_expiration_-1day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => -1,
                'label' => __('Scheduled Review in (-1 day)'),
                'description' => __('Notifies 1 day before an Review begins')
            ],
            'review_expiration_-5day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => -5,
                'label' => __('Scheduled Review in (-5 days)'),
                'description' => __('Notifies 5 days before an Review begins')
            ],
            'review_expiration_-10day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => -10,
                'label' => __('Scheduled Review in (-10 days)'),
                'description' => __('Notifies 10 days before an Review begins')
            ],
            'review_expiration_-20day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => -20,
                'label' => __('Scheduled Review in (-20 days)'),
                'description' => __('Notifies 20 days before an Review begins')
            ],
            'review_expiration_-30day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => -30,
                'label' => __('Scheduled Review in (-30 days)'),
                'description' => __('Notifies 30 days before an Review begins')
            ],
            'review_expiration_+1day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => 1,
                'label' => __('Scheduled Review in (+1 day)'),
                'description' => __('Notifies 1 day after an Review begins')
            ],
            'review_expiration_+5day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => 5,
                'label' => __('Scheduled Review in (+5 days)'),
                'description' => __('Notifies 5 days after an Review begins')
            ],
            'review_expiration_+10day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => 10,
                'label' => __('Scheduled Review in (+10 days)'),
                'description' => __('Notifies 10 days after an Review begins')
            ],
            'review_expiration_+20day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => 20,
                'label' => __('Scheduled Review in (+20 days)'),
                'description' => __('Notifies 20 days after an Review begins')
            ],
            'review_expiration_+30day' => [
                'type' => NOTIFICATION_TYPE_WARNING,
                'className' => 'ReviewsPlanner.ReviewExpiration',
                'days' => 30,
                'label' => __('Scheduled Review in (+30 days)'),
                'description' => __('Notifies 30 days after an Review begins')
            ]
        ];
    }

    public function getNotificationSystemConfig()
    {
        $notif = [
            'macros' => true,
            'notifications' => [
                'object_reminder' => $this->_getModelObjectReminderNotification('ReviewsPlanner.Review'),
                'comments' => [
                    'type' => NOTIFICATION_TYPE_DEFAULT,
                    'className' => 'Comments.Comments',
                    'label' => __('Comment Uploaded')
                ],
                'attachments' => [
                    'type' => NOTIFICATION_TYPE_DEFAULT,
                    'className' => 'Attachments.Attachments',
                    'label' => __('Attachment Uploaded')
                ],
                'digest' => [
                    'type' => NOTIFICATION_TYPE_DEFAULT,
                    'className' => 'Widget.Digest',
                    'key' => 'value',
                    'label' => __('Digest of Comments & Attachments')
                ],
                'advanced_filters' => [
                    'type' => NOTIFICATION_TYPE_REPORT,
                    'className' => 'AdvancedFilters.AdvancedFilters',
                    'label' => __('Send Scheduled Filters')
                ],
                'reports' => [
                    'type' => NOTIFICATION_TYPE_REPORT,
                    'className' => 'Reports.Reports',
                    'label' => __('Send Scheduled Report')
                ]
            ]
        ];

        $notif['notifications'] = array_merge($notif['notifications'], $this->_getReviewNotificationConfig());

        return $notif;
    }

    public function statusExpired($conditions = []) {
        return (boolean) parent::statusExpired([
            "{$this->alias}.completed" => REVIEW_NOT_COMPLETE,
            "DATE({$this->alias}.planned_date) < DATE(NOW())"
        ]);
    }

    public function statusCurrentReview()
    {
        $status = false;

        $data = $this->advancedFind('first', [
            'conditions' => [
                "{$this->alias}.actual_date <= DATE(NOW())",
                "{$this->alias}.completed" => self::STATUS_COMPLETE,
                "{$this->alias}.model" => $this->_relatedModel,
                "{$this->alias}.foreign_key" => $this->advancedFind('first', [
                    'conditions' => [
                        "{$this->alias}.id" => $this->id
                    ],
                    'fields' => [
                        "{$this->alias}.foreign_key"
                    ],
                ])
            ],
            'fields' => [
                "{$this->alias}.id"
            ],
            'order' => [
                "{$this->alias}.actual_date" => 'DESC',
                "{$this->alias}.created" => 'DESC'
            ]
        ])->get();

        if (!empty($data) && $data[$this->alias]['id'] == $this->id) {
            $status = true;
        }

        return $status;
    }

    public function getReportsConfig()
    {
        return [
            'table' => [
                'model' => [
                    $this->_relatedModel,
                ]
            ],
        ];
    }

    public function getMacrosConfig()
    {
        return [
            'assoc' => [
                $this->_relatedModel,
            ],
        ];
    }

    /**
     * Get index url params for current model.
     * 
     * @return array Url params.
     */
    public function getSectionIndexUrl($params = []) {
        return parent::getSectionIndexUrl([
            'controller' => 'reviews',
            'action' => 'filterIndex',
            $this->alias,
            '?' => [
                'advanced_filter' => 1
            ]
        ]);
    }

    // public function bindNode($item) {
    //     debug($item);exit;
    //     return array('model' => 'Group', 'foreign_key' => $user['User']['group_id']);
    // }

    /**
     * Get the parent model name, required for InheritanceInterface class.
     */
    public function parentModel() {
        return $this->_relatedModel;
    }

    public function parentNode($type) {
        return $this->visualisationParentNode('foreign_key');
    }

    // risk review field into field data collection for bulk editing while keeping the original logic
    protected function addRiskReviewField() {
        // review field
        // $Review = $this->{$this->parentModel()}->getFieldDataEntity('review');
        // if (!$this->hasFieldDataEntity('review')) {
        //     $this->getFieldCollection()->add('review', $Review)->toggleEditable(true);
        // }
    }

    public function beforeValidate($options = array()) {
        $ret = true;

        $ret &= parent::beforeValidate($options);

        // here its possible to update a related model object's data if the data are provided while saving a review
        // all is automatically associated
        if (isset($this->data[$this->_relatedModel])) {
            $relatedData = $this->data[$this->_relatedModel];

            if (isset($this->data[$this->alias]['foreign_key'])) {
                $foreignKey = $this->data[$this->alias]['foreign_key'];
            }
            else {
                $fk = $this->visualisationParentNode('foreign_key');
                $foreignKey = $fk[$this->_relatedModel]['id'];
            }

            $relatedModel = $this->{$this->_relatedModel};

            $relatedData['id'] = $foreignKey;
            $relatedModel->id = $foreignKey;
            $relatedModel->set($relatedData);

            $relatedOptions = [
                'fieldList' => array_keys($relatedData)
            ];

            $validates = $relatedModel->validates($relatedOptions);
            if (!$validates) {
                $this->invalidate($this->_relatedModel, true);
            }

            // Unset related model data so saveAssociated function won't try to validate it again
            unset($this->data[$this->_relatedModel]);
            
            // $prevValidation = $relatedModel->validationErrors;
            // $ret &= $relatedModel->save(null, $relatedOptions);
            // $relatedModel->validationErrors = $prevValidation;
        }

        // dont let people complete a review if there are previous incomplete reviews
        if (isset($this->data[$this->alias]['completed']) && $this->data[$this->alias]['completed']) {
            $model = $this->_relatedModel;

            if (isset($this->data[$this->alias]['id'])) {
                $review = $this->find('first', [
                    'conditions' => [
                        'id' => $this->data[$this->alias]['id']
                    ],
                    'recursive' => -1
                ]);

                $foreignKey = $review[$this->alias]['foreign_key'];

            } else {
                $foreignKey = $this->data[$this->alias]['foreign_key'];
            }

            $incompleteReviews = $this->find('count', [
                'conditions' => [
                    'completed' => 0,
                    'model' => $model,
                    'foreign_key' => $foreignKey,
                    'planned_date <' => $this->data[$this->alias]['planned_date']
                ],
                'recursive' => -1
            ]);

            if ($incompleteReviews) {
                ClassRegistry::init($this->alias)->invalidate('completed', __('There are previous incomplete reviews. Complete those reviews first.'));
                $this->invalidate('completed', __('There are previous incomplete reviews. Complete those reviews first.'));
            }
        }

        return $ret;
    }

    public function beforeFind($query) {
        if ($this->_relatedModel !== null) {
            $query['conditions'][$this->alias . '.model'] = $this->_relatedModel;
        }
        return $query;
    }

	public function afterSave($created, $options = array()) {
		$ret = true;

        $data = $this->{$this->_relatedModel}->data;
        $exists = $this->{$this->_relatedModel}->exists($this->{$this->_relatedModel}->id);
        if (!empty($data) && $exists) {
            // trigger a save of the related object for statuses udpate?
            $relatedOptions = [
                'fieldList' => array_keys($data[$this->_relatedModel]),
                'validate' => false
            ];

            $ret &= $this->{$this->_relatedModel}->save($data, $relatedOptions);
        }

		return $ret;
	}

    public function triggerAssociatedObjectStatus($id) {
        $data = $this->find('first', [
            'conditions' => [
                'Review.id' => $id,
                'Review.deleted' => [true, false]
            ],
            'recursive' => -1
        ]);

        if (empty($data)) {
            return;
        }

        $AssocModel = ClassRegistry::init($data['Review']['model']);
        if ($AssocModel->Behaviors->enabled('ObjectStatus.ObjectStatus')) {
            $AssocModel->triggerObjectStatus('expired_reviews', $data['Review']['foreign_key']);
        }
    }

	protected function getMapping() {
	  return $this->mapping;
	}

    // same as associateReview() method but wrapper for auto-created completed today's review for a created related object.
    public function associateAutoCreatedReview($description = '', $data = []) {
        $today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

        return $this->associateReview($today, am([
            'actual_date' => $today,
            'completed' => self::STATUS_COMPLETE,
            'description' => $description
        ], $data), false);
    }

    /**
     * The same as addReview() method but with model and foreign_key value automatically assigned
     * from $this->_relatedModel instace, assuming its having Model->id value during save operation for example.
     *  
     * @return boolean                True on success, False otherwise.
     */
    public function associateReview($plannedDate, $data = [], $skipDuplicate = true)
    {
        $Model = $this->{$this->_relatedModel};

        return $this->addReview(
            $Model->id,
            $plannedDate,
            $data,
            $skipDuplicate
        );
    }

    /**
     * Wrapper method that creates a new review record based on a Review model alias where its being saved,
     * for example SecurityPolicyReview->addReview() creates a review for SecurityPolicy model.
     *
     * note: migratin security policy new reviews into this should be tested properly because of its different save.
     *
     * @param  boolean $skipDuplicate Checks if record with the same planned_date already exists.
     *                                Skip saveing additional one if it does.
     */
    public function addReview($foreignKey, $plannedDate, $data = [], $skipDuplicate = true)
    {
        $saveData = [
            'model' => $this->_relatedModel,
            'foreign_key' => $foreignKey,
            'user_id' => null,
            'planned_date' => $plannedDate,
            'actual_date' => null,
            'completed' => self::STATUS_NOT_COMPLETE,
            'version' => null,
            // 'workflow_status' => WORKFLOW_APPROVED
            'Reviewer' => []
        ];

        if (is_array($data) && !empty($data)) {
            $saveData = am($saveData, $data);
        }

        if ($skipDuplicate === true && $this->reviewDuplicate($saveData['foreign_key'], $saveData['planned_date'])) {
            return true;
        }

        //
        // Set Reviewer
        if (empty($saveData['Reviewer'])) {
            if ($this->{$this->_relatedModel}->Behaviors->loaded('Reviews') &&
            !empty($this->{$this->_relatedModel}->Behaviors->Reviews->settings[$this->_relatedModel]['userFields'])) {
                $reviewerFields = $this->{$this->_relatedModel}->Behaviors->Reviews->settings[$this->_relatedModel]['userFields'];
                
                $relatedModelData = $this->{$this->_relatedModel}->find('first', [
                    'conditions' => [
                        $this->_relatedModel . '.id' => $foreignKey
                    ]
                ]);
                foreach ($reviewerFields as $rField) {
                    if (array_key_exists($rField, $relatedModelData)) {
                        $users = Hash::extract($relatedModelData, $rField . '.{n}.id');
                        foreach ($users as $user) {
                            if (!in_array($user, $saveData['Reviewer'], true)) {
                                $saveData['Reviewer'][] = $user;
                            }
                        }
                    }
                }
            }
        }
        //

        // actual date is wrongly validated as required when created
        $fieldList = array_keys($saveData);
        // unset($fieldList['actual_date']);
        $this->create();

        // this is here to handle correctly submission on bulks and /add forms
        // $this->id = $foreignKey;
        $this->set($saveData);
        $ret = $this->saveAssociated([
            $this->buildModelName($this->_relatedModel) => $saveData
        ], [
            'fieldList' => $fieldList
        ]);

        return $ret;
    }

    // check for a duplicated review entry by required planned_date field
    public function reviewDuplicate($foreignKey, $plannedDate) {
        return (bool) $this->find('count', [
            'conditions' => [
                $this->alias . '.foreign_key' => $foreignKey,
                $this->alias . '.planned_date' => $plannedDate
            ],
            'recursive' => -1
        ]);
    }

    /**
     * Builds a correct conventional Review model name out of a parent's object model name.
     * 
     * @param  string $model Model name.
     * @return string        Review model name.
     */
    public static function buildModelName($parentModel) {
        return $parentModel . 'Review';
    }

    /**
     * returns related model
     */
    public function getRelatedModel() {
        return $this->_relatedModel;
    }

    /**
     * Get last completed review.
     */
    public function getLastCompletedReview($foreignKey) {
        $data = $this->find('first', [
            'conditions' => [
                "{$this->alias}.foreign_key" => $foreignKey,
                "{$this->alias}.completed" => REVIEW_COMPLETE,
                "{$this->alias}.version IS NOT NULL",
                "{$this->alias}.actual_date IS NOT NULL"
            ],
            'order' => [
                "{$this->alias}.actual_date" => 'DESC',
                "{$this->alias}.version" => 'DESC'
            ],
            'recursive' => -1
        ]);

        return $data;
    }

    public function timeline($id)
    {
        $rawData = $this->find('all', [
            'conditions' => [
                "{$this->alias}.foreign_key" => $id
            ],
            'fields' => [
                "{$this->alias}.planned_date",
                "{$this->alias}.completed",
            ],
            'recursive' => -1
        ]);

        $data = [];

        foreach ($rawData as $item) {
            $x = date('Y-m', strtotime($item[$this->alias]['planned_date']));
            $y = $item[$this->alias]['completed'];

            if (isset($data[$x][$y])) {
                $data[$x][$y]++;
            }
            else {
                $data[$x][$y] = 1;
            }
        }

        return $data;
    }

    public function hasSectionIndex()
    {
        return true;
    }

    public function buildRecordTitle(ItemDataEntity $Item)
    {
        $Item->getModel()->bindObjectModel($this->alias);
        
        $ReviewItem = $Item->{$this->alias};
        $parentDisplayField = $this->{$this->parentModel()}->displayField;
        $parentLabel = $ReviewItem->{$this->parentModel()}->{$parentDisplayField};

        return sprintf('%s (%s)', $ReviewItem->planned_date, $parentLabel);
    }

    public function getCurrentReview($parentId)
    {
        $ObjectStatus = ClassRegistry::init('ObjectStatus.ObjectStatus');

        return $this->find('first', [
            'conditions' => [
                "{$this->alias}.model" => $this->_relatedModel,
                "{$this->alias}.foreign_key" => $parentId
            ],
            'contain' => [],
            'joins' => [
                [
                    'table' => $ObjectStatus->tableName(),
                    'alias' => 'ObjectStatus',
                    'type' => 'INNER',
                    'conditions' => [
                        'ObjectStatus.model' => $this->alias,
                        "ObjectStatus.foreign_key = {$this->alias}.id",
                        'ObjectStatus.name' => 'current_review',
                        'ObjectStatus.status' => 1,
                    ]
                ]
            ]
        ]);
    }

    public function updateReviews($data, $conditions)
    {
        //
        // Add this model prefix to conditions
        $conds = [];
        foreach ($conditions as $key => $val) {
            $conds[$this->alias . '.' . $key] = $val;
        }
        //
        
        $reviews = $this->find('all', [
            'conditions' => $conds
        ]);
        
        $reviewsIds = Hash::extract($reviews, '{n}.' . $this->alias . '.id');
        
        $ret = true;
        foreach ($reviewsIds as $id) {
            // Prepare model for another review
            $this->clear();

            $fieldList = array_keys($data);

            // Add ID to data
            $data['id'] = $id;

            $ret &= $this->saveAssociated([
                $this->buildModelName($this->_relatedModel) => $data
            ], [
                'deep' => true,
                'fieldList' => $fieldList,
            ]);
        }

        return $ret;
    }
}
