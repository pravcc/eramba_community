<?php 
App::uses('QueueTransport', 'Network/Email');
?>
<?php
echo $this->Form->create('AwarenessProgramStart', [
	'url' => ['controller' => 'awarenessPrograms', 'action' => 'start', $this->data['AwarenessProgram']['id']],
	'novalidate' => true,
	'data-yjs-form' => $formName
]);

echo $this->Alerts->info(__('Are you sure you want to start "%s"?', $this->data['AwarenessProgram']['title']) . '<br>' . __('All emails asociated with this awareness trainings are put on an email queue and flushed every hour by a daily cron. The cron pushes up to %s email, this means you need 10 hours to flush %s email. You can monitor the queue on System / Settings / Mail Queue.', QueueTransport::getQueueLimit(), (QueueTransport::getQueueLimit()*10)));

echo $this->Form->end();
?>