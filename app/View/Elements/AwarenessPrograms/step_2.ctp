<?= $this->FieldData->input($FieldDataCollection->ldap_connector_id, [
	'readonly' => !empty($edit) ? true : false
]) ?>
<?= $this->FieldData->input($FieldDataCollection->AwarenessProgramLdapGroup, [
	'readonly' => !empty($edit) ? true : false
]) ?>
<?= $this->FieldData->input($FieldDataCollection->AwarenessProgramIgnoredUser, [
	'readonly' => !empty($edit) ? true : false
]) ?>
<?= $this->FieldData->input($FieldDataCollection->ldap_check) ?>
<?php
if ($this->request->is(['post', 'put']) && empty($this->validationErrors['AwarenessProgram']['ldap_check']) && !empty($this->request->data['AwarenessProgram']['ldap_check'])) {
	echo $this->Alerts->success(__('LDAP check passed, you may continue the Awareness wizard.'));
}
?>