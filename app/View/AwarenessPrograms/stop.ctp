<?php 
App::uses('QueueTransport', 'Network/Email');
?>
<?php
echo $this->Form->create('AwarenessProgramStop', [
	'url' => ['controller' => 'awarenessPrograms', 'action' => 'stop', $this->data['AwarenessProgram']['id']],
	'novalidate' => true,
	'data-yjs-form' => $formName
]);

echo $this->Alerts->info(__('Are you sure you want to stop "%s"?', $this->data['AwarenessProgram']['title']) . '<br>' . __('You might have emails on the mail queue for this awareness, please go to System / Settings / Mail Queue and search mails under the description: Awareness Training: %s of Awareness. Otherwise this emails will be sent out.', $this->data['AwarenessProgram']['title']));

echo $this->Form->end();
?>