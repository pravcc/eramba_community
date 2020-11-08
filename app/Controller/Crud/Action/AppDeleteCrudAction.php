<?php
App::uses('DeleteCrudAction', 'Crud.Controller/Crud/Action');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');

/**
 * Handles 'Delete' Crud actions
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class AppDeleteCrudAction extends DeleteCrudAction {

    use CrudActionTrait;
    
/**
 * Startup method
 *
 * Called when the action is loaded
 *
 * @param CrudSubject $subject
 * @param array $defaults
 * @return void
 */
    public function __construct(CrudSubject $subject, array $defaults = array()) {
        $defaults = am([
            'deleteMethod' => 'delete',
            'messages' => [
                'success' => [
                    'text' => __('%s was successfully deleted.', $this->getSectionLabel($subject)),
                    'element' => FLASH_OK
                ],
                'error' => [
                    'text' => __('Error while deleting the data. Please try it again.'),
                    'element' => FLASH_ERROR
                ]
            ],
            'view' => '/Elements/section/delete'
        ], $defaults);

        parent::__construct($subject, $defaults);
    }

/**
 * HTTP DELETE handler
 *
 * @throws NotFoundException If record not found
 * @param string $id
 * @return void
 */
    protected function _delete($id = null) {
        $this->_commonData($id);

        if (!$this->_validateId($id)) {
            return false;
        }

        $request = $this->_request();
        $model = $this->_model();

        $data = $this->_findRecord($id, 'first');
        if (empty($data)) {
            return $this->_notFound($id);
        }

        $this->_setData($data);

        $subject = $this->_trigger('beforeDelete', compact('id'));
        if ($subject->stopped) {
            $this->setFlash('error');
            return;
        }

        $ret = call_user_func([$model, $this->deleteMethod()], $id);
        if ($ret) {
            // $this->setFlash('success');
            $subject = $this->_trigger('afterDelete', array('id' => $id, 'success' => true));
        } else {
            $this->setFlash('error');
            $subject = $this->_trigger('afterDelete', array('id' => $id, 'success' => false));
        }

        $this->_trigger('beforeRender', $subject);
    }

    /**
     * Extended function for parent::setFlash(), check if customDeleteMessage exists in model and if does, use it instead of default message
     */
    public function setFlash($type)
    {
        if ($type === 'error') {
            $customDeleteMessage = $this->_model()->customDeleteMessage;
            if (!empty($customDeleteMessage)) {
                $this->config('messages.error.text', $customDeleteMessage);
            }
        }

        parent::setFlash($type);
    }

    protected function _notFound($id) {
        $this->_trigger('recordNotFound', compact('id'));

        $message = $this->message('recordNotFound', compact('id'));
        $exceptionClass = $message['class'];
        throw new $exceptionClass($message['text'], $message['code']);
    }

/**
 * Find a record from the ID
 *
 * @param string $id
 * @param string $findMethod
 * @return array
 */
    protected function _findRecord($id, $findMethod = null) {
        $model = $this->_model();

        $query = array();
        $query['conditions'] = array($model->escapeField() => $id);

        if (!$findMethod) {
            $findMethod = $this->_getFindMethod($findMethod);
        }

        $subject = $this->_trigger('beforeFind', compact('query', 'findMethod'));
        return $model->find($subject->findMethod, $subject->query);
    }

/**
 * Set data to view
 *
 * @param array $data
 * @return void
 */
    protected function _setData($data = null) {
        $controller = $this->_controller();
        $model = $this->_model();

        $controller->set('showHeader', true);
        $controller->set('controller', $controller->name);
        $controller->set('model', $model->alias);
        $controller->set('displayField', (isset($model->displayField) ? $model->displayField : 'title'));
        $controller->set('recordTitle', $model->getRecordTitle($data[$model->alias][$model->primaryKey]));
        $controller->set('data', $data);
    }

/**
 * Read detail data for delete view
 *
 * @throws NotFoundException If record not found
 * @param string $id
 * @return void
 */
    protected function _detail($id = null) {
        if (!$this->_validateId($id)) {
            return false;
        }

        $data = $this->_findRecord($id, 'first');
        if (empty($data)) {
            return $this->_notFound($id);
        }

        $this->_setData($data);

        $this->_trigger('beforeRender');
    }

/**
 * HTTP GET handler
 *
 * @param mixed $id
 * @return void
 */
    protected function _get($id = null) {
        $this->_commonData($id);

        return $this->_detail($id);
    }

    protected function _commonData($id = null)
    {
        $deleteFormName = 'delete-form-' . $id;
        $this->_controller()->set('deleteFormName', $deleteFormName);
        //
        // Init modal
        $this->_controller()->Modals->init();
        $this->_controller()->Modals->setType('warning');
        if (count($this->_controller()->Modals->getBreadcrumbs()) == 0) {
            $this->_controller()->Modals->addBreadcrumb($this->_model()->label(), true);
        }
        // Find db item and set heading
        $item = $this->_findRecord($id, 'first');
        $heading = __('Delete an item');
        if (!empty($item[$this->_model()->alias][$this->_model()->displayField])) {
            $itemName = $item[$this->_model()->alias][$this->_model()->displayField];
            if (mb_strlen($itemName) > 23) {
                $itemName = mb_substr($item[$this->_model()->alias][$this->_model()->displayField], 0, 20) . '...';
            }
            $heading .= ' (' . $itemName . ')';
        }
        $this->_controller()->Modals->setHeaderHeading($heading);
        $this->_controller()->Modals->addFooterButton(__('Delete'), [
            'class' => 'btn btn-danger',
            'data-yjs-request' => "app/submitForm",
            'data-yjs-target' => "modal", 
            'data-yjs-modal-id' => $this->_controller()->Modals->getModalId(),
            'data-yjs-on-modal-success' => "close", 
            'data-yjs-datasource-url' => $this->_controller()->request->here(), 
            'data-yjs-forms' => $deleteFormName, 
            'data-yjs-event-on' => "click",
            'data-yjs-on-success-reload' => "#main-toolbar|#main-content"
        ], 'deleteBtn');
        //
    }

    /**
     * Change the delete() method.
     *
     * @param mixed $method
     * @return mixed
     */
    public function deleteMethod($method = null)
    {
        if ($method === null) {
            return $this->config('deleteMethod');
        }

        return $this->config('deleteMethod', $method);
    }


}
