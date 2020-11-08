<?php
App::uses('AdvancedFiltersAppModel', 'AdvancedFilters.Model');

class AdvancedFilterUserParam extends AdvancedFiltersAppModel
{

    public $actsAs = array(
        'FieldData.FieldData',
        'Containable'
    );

    public $belongsTo = [
        'AdvancedFilter' => [
            'className' => 'AdvancedFilters.AdvancedFilter'
        ]
    ];

    public $validate = array(
        // 'advanced_filter_id' => array(
        //     'rule' => 'notBlank',
        //     'required' => true,
        //     'allowEmpty' => false
        // ),
        'user_id' => array(
            'rule' => 'notBlank',
            'required' => true,
            'allowEmpty' => false
        )
    );

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Advanced Filters User Parameters');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ]
        ];

        $this->fieldData = [
            // 'default_index' => [
            //     'label' => __('Default Index'),
            //     'editable' => true,
            //     'type' => 'toggle',
            //     'description' => __('Enable the toggle and this filter will be shown every time the index is shown'),
            //     'renderHelper' => ['AdvancedFilters', 'defaultIndexField']
            // ]
        ];

        parent::__construct($id, $table, $ds);
    }

    /*
     * static enum: Model::function()
     * @access static
     */
     public static function types($value = null) {
        $options = array(
            self::TYPE_GENERAL => __('General'),
            self::TYPE_COLUMN_ORDER => __('Column Order'),
            self::TYPE_COLUMN_SORT => __('Column Sort'),
            self::TYPE_COLUMN_RESIZE => __('Column Resize'),
            self::TYPE_COLUMN_TEXT_WRAP => __('Column Text Wrap')
        );
        return parent::enum($value, $options);
    }
    // default is not used anymore, its here only for backwards compatibility
    const TYPE_GENERAL = 0;
    const TYPE_COLUMN_ORDER = 1;
    const TYPE_COLUMN_SORT = 2;
    const TYPE_COLUMN_RESIZE = 3;
    const TYPE_COLUMN_TEXT_WRAP = 4;
    
}