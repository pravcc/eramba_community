<?php
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('Review', 'Model');
App::uses('SecurityPolicy', 'Model');
App::uses('Attachment', 'Attachments.Model');
App::uses('SecurityPolicyReviewsHelper', 'View/Helper');

class SecurityPolicyReview extends Review {
    protected $_relatedModel = 'SecurityPolicy';

    protected $_updates = false;

	public $useTable = 'reviews';

    public $actsAs = array(
        'Containable',
        'Search.Searchable',
        'HtmlPurifier.HtmlPurifier' => array(
            'config' => 'Strict',
        ),
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report',
            ]
        ],
        'Comments.Comments',
        'Visualisation.Visualisation',
        'Attachments.Attachments',
        'Widget.Widget',
        'Macros.Macro',
        'AdvancedFilters.AdvancedFilters'
    );

    public $belongsTo = [
        'SecurityPolicy' => [
            'foreignKey' => 'foreign_key',
        ]
    ];

    // public $hasOne = array(
    //     'SecurityPolicyReviewCustom'
    // );

	public function __construct($id = false, $table = null, $ds = null) {
		$this->mapping = $this->getMapping();
		$this->mapping['indexController'] = 'reviews';

        $this->fieldGroupData = array(
            'default' => array(
                'label' => __('General')
            ),
            'security-policy' => array(
                'label' => __('Security Policy Updates')
            ),
            'security-policy-content' => array(
                'label' => __('Security Policy Content')
            )
        );

        $this->fieldData = [
            'planned_date' => [
                'label' => __('Planned date'),
                'description' => __('This is the date when the Policy was supposed to get a review. If this is an ad-hoc review (you clicked on "Add new") then this date will be empty and most likely should be completed with todays day.'),
                'editable' => false,
            ],
             'actual_date' => array(
                'label' => __('Actual date'),
                'editable' => true,
                'description' => __('This is the date when the Policy actually got updated. If this is an ad-hoc review (you clicked on "Add new") then this date will be empty and most likely should be completed with todays day.'),
                'renderHelper' => ['Reviews', 'actualDateField']
            ),
            'completed' => array(
                'label' => __('Completed'),
                'type' => 'toggle',
                'editable' => true,
                'description' => __('Unless you click on this checkbox the review will not be considered completed.<br>
                    <br>
IMPORTANT: is very useful to keep reviews evidence, you may store them as attachment to this Review by using the right of this form (if you created an ad-hoc review you will need to first save this form and then attach using the "attachments" icon.)'),
                'renderHelper' => ['Reviews', 'completedField']
                // 'hidden' => true
            ),
            'use_attachments' => array(
                'label' => __('Document Content'),

            ),
            'url' => array(
                'label' => __('Content Url'),
                'editable' => false,
            ),
            'policy_description' => array(
                'label' => __('Content Editor'),
                'editable' => false,
            ),
            'attachment' => array(
                'label' => __('Content Attachment'),
                'editable' => false,
            ),
        ]; 

        // $this->fieldData = array(
        //    'attachment' => [//this is only virtual field
        //         'label' => __('Attachment'),
        //         'description' => __('TBD'),
        //         'group' => 'security-policy-content',
        //         'editable' => true,
        //         'renderHelper' => ['SecurityPolicyReviews', 'attachmentField']
        //     ],
        // );

		parent::__construct($id, $table, $ds);

        $this->label = __('Reviews');

        $this->advancedFilterSettings = array(
            'pdf_title' => __('Security Policy Reviews'),
            'pdf_file_name' => __('security_policy_reviews'),
            'csv_file_name' => __('security_policy_reviews'),
            'url' => array(
                'controller' => 'reviews',
                'action' => 'filterIndex',
                'SecurityPolicyReview',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            'reset' => array(
                'controller' => 'securityPolicies',
                'action' => 'index',
            ),
            'bulk_actions' => true,
            'history' => true,
            'trash' => array(
                'controller' => 'reviews',
                'action' => 'trash',
                'SecurityPolicyReview',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            'view_item' => array(
                'ajax_action' => array(
                    'controller' => 'reviews',
                    'action' => 'index',
                    'SecurityPolicy'
                )
            ),
            'use_new_filters' => true
        );

        $this->advancedFilterSettings = am($this->advancedFilterSettings, $this->reviewFilterSettings);

        // $this->addReviewField();
	}

    public function getAdvancedFilterConfig()
    {
        $advancedFilterConfig = $this->_getAdvancedFilterConfig();

        $advancedFilterConfig
            ->group('general')
                ->dateField('SecurityPolicy-next_review_date', [
                    'showDefault' => true,
                    'insertOptions' => [
                        'after' => 'version'
                    ]
                ])
            ->group('PolicyContent', [
                'name' => __('Policy Content'),
                'insertOptions' => [
                    'after' => 'general'
                ]
            ])
                ->multipleSelectField('SecurityPolicy-security_policy_document_type_id', [ClassRegistry::init('SecurityPolicy'), 'getPoliciesDocumentTypes'], [
                    'showDefault' => true
                ])
                ->numberField('version', [
                    'showDefault' => true
                ])
                ->multipleSelectField('use_attachments', 'getPoliciesDocumentContentTypesWithoutUse', [
                    'label' => __('Document Content Type'),
                    'showDefault' => true
                ])
                ->textField('url', [
                    'label' => __('Content URL'),
                ])
                ->nonFilterableField('attachment', [
                    'label' => __('Content Attachment'),
                    'fieldData' => 'Attachment.name'
                ]);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function getReportsConfig()
    {
        return [
            'finder' => [
                'options' => [
                    'contain' => [
                        'SecurityPolicy' => [
                            'AssetLabel',
                            'LdapConnector',
                            'SecurityPolicyDocumentType',
                            'Tag',
                            'SecurityPolicyReview',
                            'Review',
                            'LogSecurityPolicy',
                            'SecurityPolicyLdapGroup',
                            'CustomFieldValue',
                            'SecurityService',
                            'ComplianceManagement' => [
                                'CompliancePackageItem' => [
                                    'CompliancePackage' => [
                                        'CompliancePackageRegulator'
                                    ]
                                ]
                            ],
                            'Project',
                            'Risk',
                            'ThirdPartyRisk',
                            'BusinessContinuity',
                            'RiskIncident',
                            'RiskTreatment',
                            'ThirdPartyRiskIncident',
                            'ThirdPartyRiskTreatment',
                            'BusinessContinuityIncident',
                            'BusinessContinuityTreatment',
                            'RelatedDocuments',
                            'PolicyException',
                            'AwarenessProgram',
                            'DataAsset' => [
                                'DataAssetInstance' => [
                                    'Asset'
                                ]
                            ],
                            'Owner',
                            'OwnerGroup',
                            'Collaborator',
                            'CollaboratorGroup'
                        ],
                        'User',
                        'CustomFieldValue',
                        'Reviewer',
                        'ReviewerGroup'
                    ]
                ]
            ],
            'table' => [
                'model' => [
                    'SecurityPolicy',
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
            'seed' => [
                [$this, 'customMacros']
            ]
        ];
    }

    public function customMacros($Collection)
    {
        $groupSettings = $this->getMacroGroupModelSettings();

        $Macro = new Macro($this->getMacroAlias('compliance_items_list'), __('List of Related Compliance Items'), null, ['SecurityPolicyReviewsHelper', 'complianceItemsList']);
        $Collection->add($Macro);

        $Macro = new Macro($this->getMacroAlias('risk_items_list'), __('List of Related Risk Items'), null, ['SecurityPolicyReviewsHelper', 'riskItemsList']);
        $Collection->add($Macro);

        $Macro = new Macro($this->getMacroAlias('data_flow_items_list'), __('List of Related Data Flow Items'), null, ['SecurityPolicyReviewsHelper', 'dataAssetItemsList']);
        $Collection->add($Macro);
    }

    public function associateAutoCreatedReview($description = '', $data = []) {
        $today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

        $useAttachments = $this->SecurityPolicy->field('use_attachments');
        $saveData = [
            'actual_date' => $today,
            'completed' => self::STATUS_COMPLETE,
            'description' => $description,
            'version' => $this->SecurityPolicy->field('version'),
            'use_attachments' => $useAttachments
        ];

        if ($useAttachments == SecurityPolicy::CONTENT_TYPE_EDITOR) {
            $saveData['policy_description'] = $this->SecurityPolicy->field('description');
        }

        if ($useAttachments == SecurityPolicy::CONTENT_TYPE_URL) {
            $saveData['url'] = $this->SecurityPolicy->field('url');
        }
        
        return $this->associateReview($today, am($saveData, $data), false);
    }

    protected function addReviewField() {
        // review field
        $Review = $this->{$this->parentModel()}->getFieldDataEntity('next_review_date');

        if (!$this->hasFieldDataEntity('next_review_date')) {
            $this->getFieldCollection()
                ->add('next_review_date', $Review)
                ->toggleEditable(true);
        }

        $Version = $this->{$this->parentModel()}->getFieldDataEntity('version');
        $this->getFieldCollection()
            ->add('version', $Version)
            ->toggleEditable(true);
    }

    public function beforeValidate($options = array()) {
        $data = $this->{$this->_relatedModel}->data;

        if (isset($this->data[$this->_relatedModel]['version'])) {
            $this->data['SecurityPolicyReview']['version'] = $this->data[$this->_relatedModel]['version'];
        }

        if (isset($this->data[$this->_relatedModel]['use_attachments'])) {
            $documentType = $this->data[$this->_relatedModel]['use_attachments'];
            $this->data['SecurityPolicyReview']['use_attachments'] = $documentType;

            if ($documentType == SecurityPolicy::CONTENT_TYPE_EDITOR) {
                $this->data['SecurityPolicyReview']['policy_description'] = $this->data[$this->_relatedModel]['description'];
            }

            if ($documentType == SecurityPolicy::CONTENT_TYPE_URL) {
                $this->data['SecurityPolicyReview']['url'] = $this->data[$this->_relatedModel]['url'];
            }

            if ($documentType == SecurityPolicy::CONTENT_TYPE_ATTACHMENTS) {
                $request = CakeSession::read('SecurityPolicyReview.Attachment.hash');
                $Attachment = ClassRegistry::init('Attachments.Attachment');
                $count = $Attachment->find('count', [
                    'conditions' => [
                        'Attachment.hash' => $request,
                        'Attachment.type' => Attachment::TYPE_TMP
                    ],
                    'recursive' => -1
                ]);

                if (!$count) {
                    $this->SecurityPolicy->invalidate('attachment', __('You have to upload at least one attachment.'));
                }
            }
        }

        $ret = parent::beforeValidate($options);

        return true;
    }

    public function afterSave($created, $options = array()) {
        parent::afterSave($created, $options);

        if (!empty($this->data['SecurityPolicyReview']['foreign_key'])) {
            $this->SecurityPolicy->updateDocumentVersion($this->data['SecurityPolicyReview']['foreign_key']);

            // if ($created) {
            //     $this->cloneAttachments();
            // }
        }
    }

    // public function cloneAttachments() {
    //     if ($this->data['SecurityPolicyReview']['use_attachments'] == SecurityPolicy::CONTENT_TYPE_ATTACHMENTS) {
            
    //     }
    // }

    public function getSecurityPolicy() {
        return $this->SecurityPolicy->getListWithType();
    }
}
