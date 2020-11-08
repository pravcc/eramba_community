<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('AuthComponent', 'Controller/Component');

class AttachmentsBehavior extends ModelBehavior
{
    /**
     * Default config.
     *
     * @var array
     */
    protected $_defaults = [
    ];

    public $settings = [];

    /**
     * Setup.
     *
     * @param Model $Model
     * @param array $settings
     * @throws RuntimeException
     * @return void
     */
    public function setup(Model $Model, $settings = [])
    {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }
    }

    public function bindAttachments($Model) {
        if ($Model->getAssociated('Attachment') === null) {
            $Model->bindModel([
                'hasMany' => [
                    'Attachment' => [
                        'className' => 'Attachments.Attachment',
                        'foreignKey' => 'foreign_key',
                        'conditions' => [
                            'Attachment.model' => $Model->modelFullName()
                        ],
                    ]
                ]
            ], false);
        }
    }

    public function bindLastAttachment($Model) {
        if ($Model->getAssociated('LastAttachment') === null) {
            $Model->bindModel([
                'hasOne' => [
                    'LastAttachment' => [
                        'className' => 'Attachments.LastAttachment',
                        'foreignKey' => 'foreign_key',
                        'conditions' => [
                            'LastAttachment.model' => $Model->modelFullName()
                        ],
                        'order' => [
                            'LastAttachment.created' => 'DESC'
                        ],
                    ]
                ]
            ], false);
        }
    }

    public function getAttachmentSaveData(Model $Model, $foreignKey, $file)
    {
        $data = [
            'Attachment' => [
                'model' => $Model->modelFullName(),
                'foreign_key'
            ]
        ];
        $data['Attachment']['model'] = $Model->modelFullName();
        $data['Attachment']['user_id'] = AuthComponent::user('id');

        $Attachment = ClassRegistry::init('Attachments.Attachment');

        return $Attachment->save($data, $validate, $fieldList);
    }

    public function attachmentsCount(Model $Model, $foreignKey)
    {
        $Attachment = ClassRegistry::init('Attachments.Attachment');

        return $Attachment->find('count', [
            'conditions' => [
                'Attachment.model' => $Model->modelFullName(),
                'Attachment.foreign_key' => $foreignKey
            ],
            'contain' => []
        ]);
    }

}
