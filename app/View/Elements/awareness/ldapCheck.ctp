<?php
if (isset($errorMessage)) :
	echo $this->Alerts->render($errorMessage);
else :
	echo $this->Form->create('AwarenessProgramCheck', [
		'url' => ['controller' => 'awarenessPrograms', 'action' => 'ldapCheck'],
		'novalidate' => true,
		'data-yjs-form' => $formName
	]);

	$ProgramCheck = ClassRegistry::init('AwarenessProgramCheck');
	echo $this->FieldData->input($ProgramCheck->getFieldDataEntity('ldap_connector_id'), [
		'value' => $ldapConnectorId
	]);
	echo $this->FieldData->input($ProgramCheck->getFieldDataEntity('ldap_groups'), [
		'value' => json_encode($ldapGroups)
	]);
	echo $this->FieldData->input($ProgramCheck->getFieldDataEntity('ldap_user'), [
		'options' => $ldapUsers
	]);

	echo $this->Form->end();

endif;
?>
<?php
if (isset($success)) {
	if ($success) {
		echo $this->Alerts->success(__('LDAP check passed.'), [
			// 'class' => ['ldap-check-message-box', 'fade', 'in']
		]);
	}
	else {
		echo $this->Alerts->danger(__('The LDAP check failed, you can not create awareness trainings until this control works. Please review our documentation to understand what could be wrong on your settings.'));
	}
}
?>