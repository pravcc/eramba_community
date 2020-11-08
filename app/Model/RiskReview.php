<?php
App::uses('Review', 'Model');
class RiskReview extends Review {
    protected $_relatedModel = 'Risk';

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
        'Attachments.Attachments',
        'Widget.Widget',
        'Macros.Macro',
        'AdvancedFilters.AdvancedFilters'
    );

    public $belongsTo = [
        'Risk' => [
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
            'risk' => array(
                'label' => __('Risk Management Updates')
            )
        );

		parent::__construct($id, $table, $ds);

        $this->label = __('Reviews');

        $this->advancedFilterSettings = array(
            'pdf_title' => __('asset Risk Reviews'),
            'pdf_file_name' => __('asset_risk_reviews'),
            'csv_file_name' => __('asset_risk_reviews'),
            'max_selection_size' => 10,
            'url' => array(
                'controller' => 'reviews',
                'action' => 'filterIndex',
                'RiskReview',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            'reset' => array(
                'controller' => 'risks',
                'action' => 'index',
            ),
            'bulk_actions' => true,
            'history' => true,
            'trash' => array(
                'controller' => 'reviews',
                'action' => 'trash',
                'RiskReview',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            'view_item' => array(
                'ajax_action' => array(
                    'controller' => 'reviews',
                    'action' => 'index',
                    'Risk'
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

    public function getRisks() {
        $data = $this->Risk->find('list', array(
            'fields' => array('Risk.id', 'Risk.title'),
            'order' => array('Risk.title' => 'ASC'),
        ));
        return $data;
    }
}



