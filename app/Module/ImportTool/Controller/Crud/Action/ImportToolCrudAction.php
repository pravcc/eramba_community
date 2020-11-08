<?php
// App::uses('AddCrudAction', 'Crud.Controller/Crud/Action');
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('Hash', 'Utility');
App::uses('ImportToolListener', 'ImportTool.Controller/Crud/Listener');

/**
 * Handles 'ImportTool' functionality
 */
class ImportToolCrudAction extends CrudAction {

    use CrudActionTrait;

    const ACTION_SCOPE = CrudAction::SCOPE_MODEL;

    protected $_settings = array(
        'enabled' => true,
        'saveMethod' => 'saveAssociated',
        'view' => 'ImportTool.ImportTool/preview',
        'relatedModels' => true,
        'saveOptions' => array(
            'validate' => 'first',
            'atomic' => true,
            'deep' => true,
            'import' => true
        ),
        // 'api' => array(
        //     'methods' => array('put', 'post'),
        //     'success' => array(
        //         'code' => 201,
        //         'data' => array(
        //             'subject' => array('id')
        //         )
        //     ),
        //     'error' => array(
        //         'exception' => array(
        //             'type' => 'validate',
        //             'class' => 'CrudValidationException'
        //         )
        //     )
        // ),
        'redirect' => array(
            'post_add' => array(
                'reader' => 'request.data',
                'key' => '_add',
                'url' => array('action' => 'add')
            ),
            'post_edit' => array(
                'reader' => 'request.data',
                'key' => '_edit',
                'url' => array('action' => 'edit', array('subject.key', 'id'))
            )
        ),
        'messages' => array(
            'success' => array(
                'text' => 'Successfully created {name}'
            ),
            'error' => array(
                'text' => 'Could not create {name}'
            )
        ),
        'serialize' => array()
    );

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
                    'text' => __('Items Added'),
                    'element' => FLASH_OK
                ],
                'error' => [
                    'text' => __('Something went wrong, please try again'),
                    'element' => FLASH_ERROR
                ]
            ],
            // 'saveMethod' => 'save',
            // 'view' => 'ImportTool.ImportTool/preview',
            'useModal' => true,
            // 'saveAssociatedHandler' => true
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

        $controller = $this->_controller();
        // throw new ForbiddenException(__('ImportToolCrudAction does not support _get() method yet.'), 1);
        
        $controller->title = __('Preview Import Data');
        $ImportToolData = ClassRegistry::init('ImportTool.ImportTool')->getStoredImportToolData();

        if (empty($ImportToolData)) {
            $controller->Session->setFlash(__('There is nothing to preview.'), FLASH_ERROR);
            throw new NotFoundException();
        }

        $this->_configureAction();

        $controller->set('ImportToolData', $ImportToolData);
        $ImportToolData = null;

        $this->_trigger('beforeRender', array('success' => false));
    }

    protected function _configureAction()
    {
        $controller = $this->_controller();
        $model = $this->_model();

        $sectionRoute = $model->getMappedRoute([
            'action' => 'add'
        ]);

        $sectionRoute['?'] = [
            ImportToolListener::REQUEST_PARAM => true
        ];

        $controller->Modals->setHeaderHeading($controller->title);
        $controller->Modals->changeConfig('footer.buttons.saveBtn.text', __('Import'));
        $controller->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-datasource-url', Router::url($sectionRoute));
    }

/**
 * HTTP POST handler
 *
 * @return void
 */
    protected function _post() {
        $this->_commonData();

        $controller = $this->_controller();
        $request = $this->_request();
        $model = $this->_model();

        $ImportToolData = ClassRegistry::init('ImportTool.ImportTool')->getStoredImportToolData();

        if (empty($ImportToolData)) {
            return false;
        }

        $controller->set('ImportToolData', $ImportToolData);

        $ImportToolImport = new ImportToolImport($ImportToolData);
        $checkedItems = !empty($request->data['ImportTool']['checked']) ? $request->data['ImportTool']['checked'] : [];
        
        $ret = true;
        $importedIds = [];
        if (empty($checkedItems)) {
            $this->_controller()->Flash->error(__('You have to check at least one item to start the import. Please try again.'));
            $ret = false;
        } else {
            $ImportToolImport->setImportRows($checkedItems);
            $data = $ImportToolImport->getImportToolData()->getImportableDataArray();
            
            foreach ($data as $row => $item) {
                // allow item import
                $allowItem = !$ImportToolImport->getImportSpecificRows();
                $allowItem = $allowItem || ($ImportToolImport->getImportSpecificRows() && in_array($row, $ImportToolImport->getImportRows()));

                if ($allowItem) {
                    // $item = Hash::merge($item, $additionalItemData);

                    $request->data = $item;
                    $this->_trigger('beforeSave', array('import' => true));

                    $ret &= $success = call_user_func(array($model, $this->saveMethod()), $request->data, $this->saveOptions());
                    $subject = $this->_trigger('afterSave', array('success' => $success, 'created' => true, 'id' => $model->id, 'import' => true));

                    if ($ret) {
                        $importedIds[] = $model->id;
                    }
                }
            }
        }

        $this->_trigger('afterImport', array('success' => $ret, 'id' => $importedIds));

        if ($ret) {
            $this->setFlash('success');
        }

        $this->_trigger('beforeRender', ['success' => $ret]);
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
            if (count($this->_controller()->Modals->getBreadcrumbs()) == 0) {
                $this->_controller()->Modals->addBreadcrumb($this->_model()->label(), true);
            }
            $this->_controller()->Modals->setHeaderHeading(__('Add item'));
            $this->_controller()->Modals->showFooterSaveButton();
        }
        //
    }

}
