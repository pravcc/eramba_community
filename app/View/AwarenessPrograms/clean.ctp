<?php 
App::uses('QueueTransport', 'Network/Email');
?>
<?php
echo $this->Form->create('AwarenessProgramClean', [
	'url' => ['controller' => 'awarenessPrograms', 'action' => 'clean', $this->data['AwarenessProgram']['id']],
	'novalidate' => true,
	'data-yjs-form' => $formName
]);

echo $this->Alerts->info(__('Choose date interval during which records for the Awareness Program should be cleaned. Leave blank to clean all records.'));

?>

<?php
echo $this->FieldData->input(ClassRegistry::init('AwarenessProgramClean')->getFieldDataEntity('from'));
echo $this->FieldData->input(ClassRegistry::init('AwarenessProgramClean')->getFieldDataEntity('to'));
?>

<?php
echo $this->Form->end();
?>