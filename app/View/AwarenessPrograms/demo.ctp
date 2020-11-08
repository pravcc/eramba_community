<?php
if (isset($ldapConnection) && $ldapConnection !== true) {
	echo $this->element('ldapConnectors/ldapConnectionStatus');
	return false;
}
?>

<?php 
App::uses('QueueTransport', 'Network/Email');
?>
<?php
echo $this->Form->create('AwarenessProgramUserDemo', [
	'url' => ['controller' => 'awarenessPrograms', 'action' => 'demo', $awarenessProgramId],
	'novalidate' => true,
	'data-yjs-form' => $formName
]);

echo $this->Alerts->info(__('Choose one user from the list of users to test this course. Demo email will be sent to that user.'));

echo $this->FieldData->input(ClassRegistry::init('AwarenessProgramUserDemo')->getFieldDataEntity('email'), [
	'options' => $usersList
]);

echo $this->Form->end();
?>
