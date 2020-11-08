<?php
App::uses('AppModel', 'Model');

class AwarenessProgramClean extends AppModel
{
    public $useTable = false;

    public $actsAs = array(
        'Containable',
        'FieldData' => [
            'useTable' => false
        ]
    );

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Awareness Program Clean');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'from' => [
                'type' => 'date',
                'label' => __('From')
            ],
            'to' => [
                'type' => 'date',
                'label' => __('To')
            ],
        ];

        parent::__construct($id, $table, $ds);
    }
}