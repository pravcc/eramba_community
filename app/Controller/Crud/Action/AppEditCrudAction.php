<?php
App::uses('EditCrudAction', 'Crud.Controller/Crud/Action');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('Hash', 'Utility');

/**
 * Handles 'Add' Crud actions
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class AppEditCrudAction extends EditCrudAction {

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
        $defaults = Hash::merge([
            'messages' => [
                'success' => [
                    // getSectionLabel in trait
                    'text' => __('Item Updated', $this->getSectionLabel($subject)),
                    'element' => FLASH_OK
                ],
                'error' => [
                    'text' => __('Something went wrong, please try again'),
                    'element' => FLASH_ERROR
                ],
            ],
            'view' => '/Elements/section/add',
            'useModal' => true,
            'saveOptions' => array(
                'validate' => 'first',
                'atomic' => true,
                'deep' => true
            )
        ], $defaults);

        parent::__construct($subject, $defaults);
    }

/**
 * HTTP GET handler
 *
 * @throws NotFoundException If record not found
 * @param string $id
 * @return void
 */
    protected function _get($id = null) {
        $this->_commonData($id);

        parent::_get($id);
    }

/**
 * HTTP PUT handler
 *
 * @param mixed $id
 * @return void
 */
    protected function _put($id = null) {
        $this->_commonData($id);

        if (!$this->_validateId($id)) {
            return false;
        }

        $request = $this->_request();
        $model = $this->_model();

        $existing = $this->_findRecord($id, 'count');
        if (empty($existing)) {
            return $this->_notFound($id);
        }

        $request->data = $this->_injectPrimaryKey($request->data, $id, $model);

        $subject = $this->_trigger('beforeSave', compact('id'));

        $ret = true;

        // @deprecated
        // $ret &= $this->_saveAssociatedHandler();
        
        if (!empty($subject->customStopped)) {
            $this->setFlash('error');

            $this->_trigger('beforeRender', array('id' => $id, 'success' => false, 'created' => false));

            return;
        }
        
        // Dont call the save method unless the $ret variable is true
        $ret = $ret && call_user_func(array($model, $this->saveMethod()), $request->data, $this->saveOptions());
        if ($ret) {
            // $this->setFlash('success');
            $subject = $this->_trigger('afterSave', array('id' => $id, 'success' => true, 'created' => false));
        }
        else {
            $this->setFlash('error');
            $subject = $this->_trigger('afterSave', array('id' => $id, 'success' => false, 'created' => false));
        }
        
        $this->_trigger('beforeRender', $subject);
    }

/**
 * sets common data and executes common processes
 *
 * @param mixed $id
 * @return void
 */
    protected function _commonData($id = null) {
        $this->_controller()->set('edit', true);

        // Set form name
        $formName = $this->_controller()->modelClass . 'SectionEditForm';
        $this->_controller()->set('formName', $formName);

        // Set URL
        $request = $this->_request();
        $formUrl = Router::url(Router::reverseToArray($request));
        $this->_controller()->set('formUrl', $formUrl);

        //
        // Init modal
        if ($this->_settings['useModal']) {
            $this->_controller()->Modals->init();
            if (count($this->_controller()->Modals->getBreadcrumbs()) == 0) {
                $this->_controller()->Modals->addBreadcrumb($this->_model()->label(), true);
            }
            // Find db item and set heading
            $item = $this->_findRecord($id, 'first');
            $heading = __('Edit item');
            // if (!empty($item[$this->_model()->alias][$this->_model()->displayField])) {
                // $itemName = $item[$this->_model()->alias][$this->_model()->displayField];
                $itemName = $this->_model()->getRecordTitle($id);

                if (mb_strlen($itemName) > 23) {
                    $itemName = mb_substr($item[$this->_model()->alias][$this->_model()->displayField], 0, 20) . '...';
                }
                $heading .= ' (' . $itemName . ')';
            // }
            $this->_controller()->Modals->setHeaderHeading($heading);
            $this->_controller()->Modals->showFooterSaveButton();
        }
        //
    }
    
}
