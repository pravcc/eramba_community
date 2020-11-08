<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('AuthComponent', 'Controller/Component');

class CommentsBehavior extends ModelBehavior
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

    public function bindComments($Model) {
        if ($Model->getAssociated('Comment') === null) {
            $Model->bindModel([
                'hasMany' => [
                    'Comment' => [
                        'className' => 'Comments.Comment',
                        'foreignKey' => 'foreign_key',
                        'conditions' => [
                            'Comment.model' => $Model->modelFullName()
                        ],
                    ]
                ]
            ], false);
        }
    }

    public function bindLastComment($Model) {
        if ($Model->getAssociated('LastComment') === null) {
            $Model->bindModel([
                'hasOne' => [
                    'LastComment' => [
                        'className' => 'Comments.LastComment',
                        'foreignKey' => 'foreign_key',
                        'conditions' => [
                            'LastComment.model' => $Model->modelFullName()
                        ],
                        'order' => [
                            'LastComment.created' => 'DESC'
                        ],
                    ]
                ]
            ], false);
        }
    }

    public function commentsCount(Model $Model, $foreignKey)
    {
        $Comment = ClassRegistry::init('Comments.Comment');

        return $Comment->find('count', [
            'conditions' => [
                'Comment.model' => $Model->modelFullName(),
                'Comment.foreign_key' => $foreignKey
            ],
            'contain' => []
        ]);
    }

}
