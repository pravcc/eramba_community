<?php
App::uses('Review', 'Model');
class AssetReview extends Review {
    protected $_relatedModel = 'Asset';

	public $useTable = 'reviews';

    public $actsAs = array(
        'Containable',
        'Search.Searchable',
        'HtmlPurifier.HtmlPurifier' => array(
            'config' => 'Strict',
        ),
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report'
            ]
        ],
        'Comments.Comments',
        'Attachments.Attachments',
        'Macros.Macro',
        'Widget.Widget',
        'AdvancedFilters.AdvancedFilters'
    );

    public $belongsTo = [
        'Asset' => [
            'foreignKey' => 'foreign_key',
        ]
    ];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->mapping = $this->getMapping();
		$this->mapping['indexController'] = 'reviews';

        $this->fieldGroupData = array(
            'default' => array(
                'label' => __('General')
            ),
            'asset' => array(
                'label' => __('Asset Identification Updates')
            )
        );

        parent::__construct($id, $table, $ds);

        $this->label = __('Reviews');

        $this->advancedFilterSettings = array(
            'pdf_title' => __('Asset Reviews'),
            'pdf_file_name' => __('asset_reviews'),
            'csv_file_name' => __('asset_reviews'),
            'url' => array(
                'controller' => 'reviews',
                'action' => 'filterIndex',
                'AssetReview',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            // 'reset' => array(
            //     'controller' => 'reviews',
            //     'action' => 'filterIndex',
            //     'AssetReview',
            //     '?' => array(
            //         'advanced_filter' => 1
            //     )
            // )
            'reset' => array(
                'controller' => 'assets',
                'action' => 'index'
            ),
            'bulk_actions' => array(
                BulkAction::TYPE_EDIT
            ),
            'history' => true,
            'trash' => array(
                'controller' => 'reviews',
                'action' => 'trash',
                'AssetReview',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            'view_item' => array(
                'ajax_action' => array(
                    'controller' => 'reviews',
                    'action' => 'index',
                    'Asset'
                )
            ),
            'use_new_filters' => true
        );

        $this->advancedFilterSettings = am($this->advancedFilterSettings, $this->reviewFilterSettings);

        $this->addRiskReviewField();
	}

    public function getAdvancedFilterConfig()
    {
        $advancedFilterConfig = $this->_getAdvancedFilterConfig();

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function getReportsConfig()
    {
        return [
            'finder' => [
                'options' => [
                    'contain' => [
                        'Asset' => [
                            'AssetMediaType',
                            'AssetLabel',
                            'DataAssetInstance',
                            'AssetReview',
                            'Review',
                            'CustomFieldValue',
                            'RelatedAssets',
                            'BusinessUnit',
                            'Legal',
                            'AssetClassification' => [
                                'AssetClassificationType'
                            ],
                            'Risk',
                            'ThirdPartyRisk',
                            'SecurityIncident',
                            'ComplianceManagement',
                            'AssetOwner',
                            'AssetOwnerGroup',
                            'AssetGuardian',
                            'AssetGuardianGroup',
                            'AssetUser',
                            'AssetUserGroup'
                        ],
                        'Reviewer',
                        'ReviewerGroup'
                    ]
                ]
            ],
            'table' => [
                'model' => [
                    'Asset',
                ]
            ],
        ];
    }

    public function getUsers() {
        $this->User->virtualFields['full_name'] = 'CONCAT(User.name, " ", User.surname)';
        $data = $this->User->find('list', array(
            'fields' => array('User.id', 'User.full_name'),
        ));
        return $data;
    }

    public function getAssetReviews() {
        $data = $this->Asset->find('list', array(
            'fields' => array('Asset.id', 'Asset.name'),
        ));
        return $data;
    }

    public function getDisplayFilterFields()
    {
        $fields = [];

        if (!empty($this->displayField) && !empty($this->filterArgs[$this->displayField])) {
            $fields[] = $this->displayField;
        }

        return $fields;
    }
}
