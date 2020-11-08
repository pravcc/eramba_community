<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');

class AuditCalendarFormEntryCrudAction extends CrudAction
{
	use CrudActionTrait;

	protected function _handle($model)
	{
		$controller = $this->_controller();
		$request = $this->_request();

		$AssocModel = ClassRegistry::init($model);
		$AssocFieldData = $AssocModel->getFieldCollection();

		$fieldName = $request->query['fieldName'];
		$fieldsClass = $request->query['fieldsClass'];
		$fieldsCount = $request->query['fieldsCount'];
		$controller->set('fieldName', $fieldName);
		$controller->set('fieldsClass', $fieldsClass);
		$controller->set('fieldsCount', $fieldsCount);
		$controller->set($AssocFieldData->getViewOptions('AuditCalendarCollection'));
		
		return $controller->render('../Risks/audit_calendar_fields');
	}
}
