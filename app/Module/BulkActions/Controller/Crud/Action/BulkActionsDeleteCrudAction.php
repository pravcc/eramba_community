<?php
App::uses('AppDeleteCrudAction', 'Controller/Crud/Action');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('BulkActionsTrait', 'BulkActions.Controller/Crud/Trait');

/**
 * Handles 'Delete' Crud actions
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class BulkActionsDeleteCrudAction extends AppDeleteCrudAction {

    use CrudActionTrait;
    use BulkActionsTrait;
    
/**
 * Startup method
 *
 * Called when the action is loaded
 *
 * @param CrudSubject $subject
 * @param array $defaults
 * @return void
 */
    /*public function __construct(CrudSubject $subject, array $defaults = array()) {
        $defaults = am([
            // 'messages' => [
            //     'success' => [
            //         'text' => __('%s was successfully deleted.', $this->getSectionLabel($subject)),
            //         'element' => FLASH_OK
            //     ],
            //     'error' => [
            //         'text' => __('Error while deleting the data. Please try it again.'),
            //         'element' => FLASH_ERROR
            //     ]
            // ],
            'view' => 'BulkActions./Elements/section/delete'
        ], $defaults);

        parent::__construct($subject, $defaults);
    }*/

    protected function _configureAction()
    {
        $controller = $this->_controller();
        $request = $this->_request();
        $model = $this->_model();

        parent::_commonData();
        parent::_setData();

        $controller->Modals->setHeaderHeading(__('Bulk Delete'));
        $controller->Modals->changeConfig('footer.buttons.deleteBtn.text', __('Bulk Delete'));
        $controller->Modals->changeConfig('footer.buttons.deleteBtn.options.data-yjs-datasource-url', Router::url(Router::reverseToArray($request)));

        $this->view('BulkActions.../Elements/delete');

        // $Collection = $model->getFieldCollection();
        // foreach ($Collection as $Field) {
        //     // $conds = !$Field->isAssociated();
        //     // $conds &= $Field->isAssociated() && $Field->getAssociationKey() !== 'hasMany';
        //     if ($Field->getAssociationKey() == 'hasMany') {
        //         $Collection->remove($Field->getFieldName());
        //     }
        // }

        // $controller->_FieldDataCollection = $Collection;
    }

/**
 * HTTP DELETE handler
 *
 * @throws NotFoundException If record not found
 * @param string $id
 * @return void
 */
    protected function _delete($id = null) {
        $this->_configureAction();
        // $this->_commonData($id);

        // if (!$this->_validateId($id)) {
        //     return false;
        // }

        $request = $this->_request();
        $model = $this->_model();

        // $data = $this->_findRecord($id, 'first');
        // if (empty($data)) {
        //     return $this->_notFound($id);
        // }

        // $this->_setData($data); 

        foreach ($this->_readIds() as $id) {
            $subject = $this->_trigger('beforeDelete', compact('id'));

            if ($subject->stopped) {
                $this->setFlash('error');
                return;
            }

            if ($model->delete($id)) {
                // $this->setFlash('success');
                $subject = $this->_trigger('afterDelete', array('id' => $id, 'success' => true));
            } else {
                $this->setFlash('error');
                $subject = $this->_trigger('afterDelete', array('id' => $id, 'success' => false));
            }
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

        // if (!$this->_validateId($id)) {
        //     return false;
        // }

        // $data = $this->_findRecord($id, 'first');
        // if (empty($data)) {
        //     return $this->_notFound($id);
        // }

        // $this->_setData($data);

        $this->_trigger('beforeRender');
    }

/**
 * HTTP GET handler
 *
 * @param mixed $id
 * @return void
 */
    protected function _get($id = null) {
        $this->_configureAction();
        // $this->_commonData($id);

        return $this->_detail($id);
    }


}
