<?php
App::uses('EditCrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('Hash', 'Utility');
App::uses('ObjectVersionRestore', 'ObjectVersion.Lib');

class RestoreCrudAction extends EditCrudAction
{
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
    public function __construct(CrudSubject $subject, array $defaults = array())
    {
        $defaults = Hash::merge([
            'messages' => [
                'success' => [
                    'text' => __('Object was restored successfully'),
                    'element' => FLASH_OK
                ],
                'error' => [
                    'text' => __('Something went wrong, please try again'),
                    'element' => FLASH_ERROR
                ],
            ],
            'view' => 'ObjectVersion./ObjectVersion/restore',
            'saveOptions' => array(
                'validate' => false,
                'atomic' => true,
                'deep' => true
            )
        ], $defaults);

        parent::__construct($subject, $defaults);

        $this->_controller()->_disableCsrfCheck = true;
    }

    protected function _get($auditId = null)
    {
        return $this->_put($auditId);
    }

/**
 * HTTP PUT handler
 *
 * @param mixed $auditId AuditId
 * @return void
 */
    protected function _put($auditId = null)
    {
        $controller = $this->_controller();

        $this->Audit = ClassRegistry::init('ObjectVersion.Audit');

        $audit = $this->Audit->find('first', array(
            'conditions' => array(
                'Audit.id' => $auditId
            ),
            'recursive' => -1
        ));

        if (empty($audit)) {
            throw new NotFoundException();
        }

        $Restore = $this->_restore($audit);

        if ($Restore->getResult()) {
        	if ($Restore->isRestoredDataValid()) {
                $controller->Flash->success(__('Object was successfully restored.'));
            } else {
                $controller->Flash->warning(__('Object was restored but its data failed validation. Please edit and re-save the object to manually resolve possible issues.'));
            }
            
            // in case everything went fine during the restore but revisions are identical
            if ($Restore->hasChanges() === false && $Restore->isRestoredDataValid()) {
                $controller->Flash->default(__('Current revision and the revision you chose to restore are identical, there is no need to add another one.'));
            }

            $subject = $this->_trigger('afterSave', array('id' => $Restore->getForeignKey(), 'success' => true, 'created' => false));
            $this->_trigger('afterRestore', array('id' => $Restore->getForeignKey(), 'success' => true));

            //handle associative restore
            $assocRestore = $this->_associativeRestore($audit);
            if (!$assocRestore) {
                $controller->Flash->warning(__('Some of associated object have not been restored correctly.'));
            }
        } else {
        	$controller->Flash->error(__('Error occured while trying to restore the object. Please try it again.'));

            $subject = $this->_trigger('afterSave', array('id' => $Restore->getForeignKey(), 'success' => false, 'created' => false));
            $this->_trigger('afterRestore', array('id' => $Restore->getForeignKey(), 'success' => false));
        }

        $this->_trigger('beforeRender', $subject);
    }

    protected function _restore($audit, $triggerEvents = true)
    {
        $Model = ClassRegistry::init($audit['Audit']['model']);

        $restoreClass = new ObjectVersionRestore($audit['Audit']['id']);
        $restoreClass->beforeRestore();

        $request = $this->_request();
        $request->data = $restoreClass->getData();

        if ($triggerEvents) {
            $this->_trigger('beforeSave', ['id' => $restoreClass->getForeignKey()]);
            $this->_trigger('beforeRestore', ['id' => $restoreClass->getForeignKey()]);
        }

        $restoreClass->restore($this->saveMethod(), $request->data, $this->saveOptions());

        $restoreClass->afterRestore();

        return $restoreClass;
    }

    protected function _associativeRestore($audit)
    {
        $Model = ClassRegistry::init($audit['Audit']['model']);

        $ret = true;

        if (!$Model->Behaviors->enabled('AssociativeDelete')) {
            return $ret;
        }

        $ids = $Model->getAllAssociatedIds(null, $audit['Audit']['entity_id']);

        foreach ($ids as $assocModel => $assocIds) {
            foreach ($assocIds as $assocId) {
                $deletedItemExists = $Model->{$assocModel}->find('count', [
                    'conditions' => [
                        $Model->{$assocModel}->alias . '.id' => $assocId,
                        $Model->{$assocModel}->alias . '.deleted' => true,
                    ]
                ]);

                $assocAudit = $this->Audit->find('first', array(
                    'conditions' => array(
                        'Audit.entity_id' => $assocId,
                        'Audit.model' => $Model->{$assocModel}->name
                    ),
                    'order' => [
                        'Audit.created' => 'DESC',
                    ],
                    'recursive' => -1
                ));

                if ($deletedItemExists && !empty($assocAudit)) {
                    $Restore = $this->_restore($assocAudit, false);
                    $ret &= (boolean) $Restore->getResult();

                    //trigger object status
                    if ($Model->{$assocModel}->Behaviors->enabled('ObjectStatus')) {
                        $ret &= (boolean) $Model->{$assocModel}->triggerObjectStatus();
                    }

                    $ret &= (boolean) $this->_associativeRestore($assocAudit);
                }
            }
        }

        return $ret;
    }
}
