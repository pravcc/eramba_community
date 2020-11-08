<?php
App::uses('AdvancedFiltersAppModel', 'AdvancedFilters.Model');

class AdvancedFilterUserSetting extends AdvancedFiltersAppModel {

    const DEFAULT_INDEX = 1;
    const NOT_DEFAULT_INDEX = 0;

    const MAX_DEFAULT_INDEX_COUNT = 50;

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
        ),
        'default_index' => [
            'rule' => ['inList', [
                self::DEFAULT_INDEX,
                self::NOT_DEFAULT_INDEX
            ]],
            'message' => 'Wrong type is provided for default index field'
        ]
        // 'default_index' => array(
        //     'inList' => [
        //         'rule' => ['inList', [
        //             self::DEFAULT_INDEX,
        //             self::NOT_DEFAULT_INDEX
        //         ]],
        //         'message' => 'Wrong type is provided for default index field'
        //     ]
        // ),
    );

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Advanced Filters User Settings');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ]
        ];

        $this->fieldData = [
            'default_index' => [
                'label' => __('Default Index'),
                'editable' => true,
                'type' => 'toggle',
                'description' => __('Enable the toggle and this filter will be shown every time the index is shown'),
                'renderHelper' => ['AdvancedFilters', 'defaultIndexField']
            ]
        ];

        parent::__construct($id, $table, $ds);
    }

    public function beforeValidate($options = array())
    {
        if ($this->data[$this->alias]['default_index'] == self::DEFAULT_INDEX) {
        	$conditions = [
                'AdvancedFilter.model' => $this->data['AdvancedFilterUserSetting']['model'],
                'AdvancedFilterUserSetting.default_index' => self::DEFAULT_INDEX,
                'AdvancedFilterUserSetting.user_id' => $this->data['AdvancedFilterUserSetting']['user_id']
            ];

			if (!empty($this->AdvancedFilter->id)) {
				$conditions['AdvancedFilter.id'] = $this->AdvancedFilter->id;
			}
			
            $countDefaults = $this->find('count', [
                'conditions' => $conditions,
                'recurisve' => 0
            ]);

            if ($countDefaults >= self::MAX_DEFAULT_INDEX_COUNT) {
                 $this->invalidate('default_index', __('You can have maximum of %d default indexes configured', self::MAX_DEFAULT_INDEX_COUNT));
            }
        }
    }

    // save the limit value
    public function syncSetting($filterId, $userId)
    {
        $data = $this->find('first', [
            'conditions' => [
                'AdvancedFilterUserSetting.advanced_filter_id' => $filterId,
                'AdvancedFilterUserSetting.user_id' => $userId
            ],
            'fields' => [
                'AdvancedFilterUserSetting.id'
            ],
            'recurisve' => -1
        ]);


        if (empty($data)) {
            $this->create();
            $this->set([
                'advanced_filter_id' => $filterId,
                'user_id' => $userId
            ]);
            $save = $this->save();

            $id = $this->id;
        } else {
            $id = $data['AdvancedFilterUserSetting']['id'];
        }

        return $id;
    }

    // save the limit value
    public function saveLimit($filterId, $userId, $limit)
    {
        $this->syncSetting($filterId, $userId);
        
        $ret = $this->updateAll([
            'AdvancedFilterUserSetting.limit' => $limit
        ], [
            'AdvancedFilterUserSetting.advanced_filter_id' => $filterId,
            'AdvancedFilterUserSetting.user_id' => $userId
        ]);

        return (bool) $ret;
    }

    // get stored limit for a filter and user
    public function getLimit($filterId, $userId)
    {
        $this->syncSetting($filterId, $userId);

        $data = $this->find('first', [
            'conditions' => [
                'AdvancedFilterUserSetting.advanced_filter_id' => $filterId,
                'AdvancedFilterUserSetting.user_id' => $userId
            ],
            'fields' => [
                'AdvancedFilterUserSetting.limit'
            ],
            'recurisve' => -1
        ]);

        if (!empty($data)) {
            return $data['AdvancedFilterUserSetting']['limit'];
        }

        return false;
    }
    
}