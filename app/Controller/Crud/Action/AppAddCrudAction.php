<?php
App::uses('AddCrudAction', 'Crud.Controller/Crud/Action');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('Hash', 'Utility');

/**
 * Handles 'Add' Crud actions
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class AppAddCrudAction extends AddCrudAction {

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
                    'text' => __('Item Added', $this->getSectionLabel($subject)),
                    'element' => FLASH_OK
                ],
                'error' => [
                    'text' => __('Something went wrong, please try again'),
                    'element' => FLASH_ERROR
                ]
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
 * @return void
 */
    protected function _get() {
        $this->_commonData();

        parent::_get();
    }

/**
 * HTTP POST handler
 *
 * @return void
 */
    protected function _post() {
        $this->_commonData();

        $request = $this->_request();
        $model = $this->_model();

        $subject = $this->_trigger('beforeSave');

        $ret = true;

        // @deprecate since New Template
        // $ret &= $this->_saveAssociatedHandler();
        
        if (!empty($subject->customStopped)) {
            $this->setFlash('error');

            $this->_trigger('beforeRender', array('success' => false, 'created' => false, 'id' => false));

            return;
        }

        // Dont call the save method unless the $ret variable is true
        $ret = $ret && call_user_func(array($model, $this->saveMethod()), $request->data, $this->saveOptions());
        if ($ret) {
            // $this->setFlash('success');
            $subject = $this->_trigger('afterSave', array('success' => true, 'created' => true, 'id' => $model->id));
        }
        else {
            $this->setFlash('error');
            $subject = $this->_trigger('afterSave', array('success' => false, 'created' => false));
        }

        $request->data = Hash::merge($request->data, $model->data);
        $this->_trigger('beforeRender', $subject);
    }

/**
 * sets common data and executes common processes
 *
 * @param mixed $id
 * @return void
 */
    protected function _commonData() {
        // Set form name
        $formName = $this->_controller()->modelClass . 'SectionAddForm';
        $this->_controller()->set('formName', $formName);

        // Set URL
        $request = $this->_request();
        $formUrl = Router::url(Router::reverseToArray($request));
        $this->_controller()->set('formUrl', $formUrl);

        //
        // Init modal
        if ($this->_settings['useModal']) {
            $this->_controller()->Modals->init();
            $this->_controller()->Modals->setHeaderHeading(__('Add item') . ' (' . $this->_model()->label() . ')');
            $this->_controller()->Modals->showFooterSaveButton();
        }
        //
    }

}
