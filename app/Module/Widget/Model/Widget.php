<?php
App::uses('WidgetAppModel', 'Widget.Model');

class Widget extends WidgetAppModel
{
    public $useTable = false;

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];
    
    public $actsAs = [
    ];

    public $validate = [
    ];

    public function __construct($id = false, $table = null, $ds = null)
    {
        $this->label = __('Widget');

        $this->fieldGroupData = [
            'widget-add' => [
                'label' => '<i class="position-left icon-comment"></i>',
                'order' => 15,
                'navItemOptions' => [
                    'class' => 'pull-right',
                    'escape' => false
                ]
            ],
        ];

        $this->fieldData = [
            'widget-add' => [
                'group' => 'widget-add',
                'editable' => true,
                'renderHelper' => ['Widget', 'addField'],
            ],
        ];

        parent::__construct($id, $table, $ds);
    }
}
