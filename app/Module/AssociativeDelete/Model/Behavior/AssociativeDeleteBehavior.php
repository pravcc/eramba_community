<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');

class AssociativeDeleteBehavior extends ModelBehavior {

/**
 * Default config
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
    public function setup(Model $Model, $settings = []) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }
    }

/**
 * Propagate delete on associated items of selected associations.
 * 
 * @param  Model  $Model
 * @param  array $associations List of associations to propagate delete.
 * @param  int $id Id of item.
 * @return boolean Success.
 */
    public function associativeDelete(Model $Model, $associations = null, $id = null) {
        $id = ($id !== null) ? $id : $Model->id;
        $associations = ($associations !== null) ? $associations : $this->settings[$Model->alias]['associations'];
        $ret = true;

        foreach ($associations as $model) {
            $assocIds = $this->getAssociatedIds($Model, $model, $id);

            foreach ($assocIds as $assocId) {
                $isDeleted = $Model->{$model}->find('count', [
                    'conditions' => [
                        $Model->{$model}->alias . '.id' => $assocId,
                        $Model->{$model}->alias . '.deleted' => true,
                    ],
                    'recursive' => -1
                ]);

                if ($isDeleted) {
                    continue;
                }

                $Model->{$model}->delete($assocId);

                //trigger object status
                if ($Model->{$model}->Behaviors->enabled('ObjectStatus')) {
                    $ret &= (boolean) $Model->{$model}->deleteObjectStatus();
                }
            }
        }

        return $ret;
    }

    public function getAllAssociatedIds(Model $Model, $associations = null, $id = null) {
        $id = ($id !== null) ? $id : $Model->id;
        $associations = ($associations !== null) ? $associations : $this->settings[$Model->alias]['associations'];

        $data = [];

        foreach ($associations as $model) {
            $data[$model] = $this->getAssociatedIds($Model, $model, $id, true);
        }

        return $data;
    }

/**
 * Return list of associated ids, including ids of deleted items.
 *
 * @param  Model  $Model
 * @param  string $assocModel Associated model.
 * @param  int $id Id of item.
 * @return array List of ids.
 */
    public function getAssociatedIds(Model $Model, $assocModel, $id = null) {
        $id = ($id != null) ? $id : $Model->id;
        $assoc = $Model->getAssociated($assocModel);
        
        if (in_array($assoc['association'], ['belongsTo'])) {
            $WorkingModel = $Model;
            $conditions = [$WorkingModel->alias . '.id' => $id];
            $field = $WorkingModel->alias . '.' . $assoc['foreignKey'];
        }
        elseif (in_array($assoc['association'], ['hasOne', 'hasMany'])) {
            $WorkingModel = $Model->{$assocModel};
            $conditions = [
                $WorkingModel->alias . '.' . $assoc['foreignKey'] => $id,
                $WorkingModel->alias . '.deleted' => [true, false]
            ];
            $field = $WorkingModel->alias . '.id';
        }
        else {
            $WorkingModel = $Model->{$assoc['with']};
            $conditions = [$WorkingModel->alias . '.' . $assoc['foreignKey'] => $id];
            if (is_array($assoc['conditions'])) {
                $conditions = array_merge($conditions, $assoc['conditions']);
            }
            $field = $WorkingModel->alias . '.' . $assoc['associationForeignKey'];
        }

        $ids = $WorkingModel->find('list', [
            'conditions' => $conditions,
            'fields' => [
                $field, $field
            ]
        ]);

        return $ids;
    }
}

