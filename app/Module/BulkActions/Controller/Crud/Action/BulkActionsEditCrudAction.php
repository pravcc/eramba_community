<?php
App::uses('AppEditCrudAction', 'Controller/Crud/Action');
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('Hash', 'Utility');
App::uses('BulkActionsListener', 'ImportTool.Controller/Crud/Listener');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('BulkActionsTrait', 'BulkActions.Controller/Crud/Trait');
App::uses('FieldDataHelper', 'FieldData.View/Helper');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

/**
 * Handles 'BulkActions' functionality
 */
class BulkActionsEditCrudAction extends AppEditCrudAction {

    use CrudActionTrait;
    use BulkActionsTrait;

/**
 * HTTP GET handler
 *
 * @return void
 */
    protected function _get($id = null) {
        $this->_commonData();

        $this->_configureAction();

        $request = $this->_request();
        $model = $this->_model();

        $model->create();
        $request->data = $model->data;
        $this->_trigger('beforeRender', array('success' => false));
    }

    protected function _configureAction()
    {
        $controller = $this->_controller();
        $request = $this->_request();
        $model = $this->_model();

        // $sectionRoute = $model->getMappedRoute([
        //     'action' => 'edit'
        // ]);

        // $sectionRoute['?'] = [
        //     BulkActionsListener::REQUEST_PARAM => true
        // ];

        $controller->Modals->setHeaderHeading($controller->title);
        $controller->Modals->changeConfig('footer.buttons.saveBtn.text', __('Bulk Edit'));
        $controller->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-datasource-url', Router::url(Router::reverseToArray($request)));

        $Collection = new FieldDataCollection([], $model);
        foreach ($model->getFieldCollection() as $Field) {
            if ($Field->isInlineEditable() && !$Field->isType(FieldDataEntity::FIELD_TYPE_HIDDEN)) {
                $Collection->add($Field);
            }
        }

        $controller->_FieldDataCollection = $Collection;
    }

/**
 * HTTP POST handler
 *
 * @return void
 */
    protected function _post($id = null) {

        $this->_commonData();
        $this->_configureAction();

        $controller = $this->_controller();
        $request = $this->_request();
        $model = $this->_model();

        $this->_setSaveOptions();

        $ret = true;
        $editedIds = [];
        $initialRequestData = $request->data;

        $saveOptions = $this->saveOptions();
        if (!empty($saveOptions['fieldList'])) {
            foreach ($this->_readIds() as $id) {
                $request->data = $this->_readModifiedData($initialRequestData);
                $request->data = $this->_injectPrimaryKey($request->data, $id, $model);

                // Handle HABTM associations (whether to merge or replace data when field is of "multiple" type)
                $request->data = $this->prepareSaveData($initialRequestData, $request->data, $id);
                
                $this->_trigger('beforeSave', compact('id', 'initialRequestData'));
                
                $ret &= $success = call_user_func(array($model, $this->saveMethod()), $request->data, $this->saveOptions());

                $subject = $this->_trigger('afterSave', array('success' => $success, 'created' => true, 'id' => $model->id));

                if ($ret) {
                    $editedIds[] = $model->id;
                }
            }
        }

        if (!$ret) {
            $this->_request()->data = $initialRequestData;
        }

        $this->_trigger('afterBulkEdit', array('success' => $ret, 'id' => $editedIds));

        if ($ret) {
            // $this->setFlash('success');
        }

        $this->_trigger('beforeRender', ['success' => $ret]);
    }

    /**
     * Check if fields should be cleared, replaced or appended by data from bulk edit
     */
    protected function prepareSaveData($initialRequestData, $requestData, $id)
    {
        $model = $this->_model();

        $fieldSettings = !empty($initialRequestData['BulkAction']['field_settings']) ? $initialRequestData['BulkAction']['field_settings'] : [];

        //
        // Get old data of edited item if any field should be appended
        $item = [];
        if (in_array(FieldDataHelper::BULK_ACTION_FIELD_SETTING_APPEND, $fieldSettings)) {
            $item = $model->find('first', [
                'conditions' => [
                    $model->alias . '.id' => $id
                ]
            ]);
        }
        //
        
        foreach ($fieldSettings as $field => $setting) {
            if ($setting == FieldDataHelper::BULK_ACTION_FIELD_SETTING_CLEAR) {
                $fieldType = $model->getFieldDataEntity($field)->config('type');
                $newData = "";
                if ($fieldType == FieldDataEntity::FIELD_TYPE_DATE) {
                    continue;
                } elseif ($fieldType == FieldDataEntity::FIELD_TYPE_NUMBER) {
                    $newData = 0;
                } else {
                    $newData = "";
                }

                $requestData[$model->alias][$field] = $newData;
            } elseif ($setting == FieldDataHelper::BULK_ACTION_FIELD_SETTING_REPLACE) {
                // Do nothing becouse data are already set in request data
            } elseif ($setting == FieldDataHelper::BULK_ACTION_FIELD_SETTING_APPEND) {
                if ($model->getFieldDataEntity($field)->isHabtm()) {
                    $oldData = Hash::extract($item, $field . '.{n}.id');
                    foreach ($oldData as $d) {
                        if (!isset($requestData[$model->alias][$field]) ||
                            !is_array($requestData[$model->alias][$field])) {
                            $requestData[$model->alias][$field] = [];
                        }
                        $requestData[$model->alias][$field][] = $d;
                    }
                }
            }
        }

        return $requestData;
    }

    protected function _readModifiedData($rawData)
    {
        $request = $this->_request();
        $model = $this->_model();

        $data = $rawData;

        $modifiedData = [];
        $saveOptions = $this->saveOptions();
        if (!empty($saveOptions['fieldList'])) {
            foreach ($saveOptions['fieldList'][$model->alias] as $inputName) {
                $modifiedData = Hash::insert($modifiedData, $inputName, Hash::get($data, $model->alias . '.' . $inputName));
            }
        }

        return [
            $model->alias => $modifiedData
        ];
    }

    protected function _setSaveOptions()
    {
        $request = $this->_request();
        $model = $this->_model();

        $fieldSettings = !empty($request->data['BulkAction']['field_settings']) ? $request->data['BulkAction']['field_settings'] : [];
        $changedFields = [];
        foreach ($fieldSettings as $field => $setting) {
            if ($setting != FieldDataHelper::BULK_ACTION_FIELD_SETTING_LEAVE_AS_IS) {
                $changedFields[] = $field;
            }
        }

        $fieldList = [];
        if (!empty($changedFields)) {
            $fieldList = [
                $model->alias => $changedFields
            ];
        }

        $saveOptions = [
            'fieldList' => $fieldList
        ];

        $this->saveOptions($saveOptions);
    }

}
