<?php
App::uses('Attachment', 'Attachments.Model');
App::uses('CakeText', 'Utility');

$instanceId = CakeText::uuid();

$uploadUrl = Router::url($Widget->extendUrlParamsBySubject(['plugin' => 'attachments', 'controller' => 'attachments', 'action' => 'add']));
$commentUrl = Router::url($Widget->extendUrlParamsBySubject(['plugin' => 'comments', 'controller' => 'comments', 'action' => 'add']));
$storyUrl = Router::url($Widget->extendUrlParamsBySubject(['plugin' => 'widget', 'controller' => 'widget', 'action' => 'story']));
?>
<div
    id="widget-<?= $instanceId ?>"
    data-yjs-request="widget/init"
    data-yjs-event-on="init"
    data-yjs-use-loader="false"
    data-dropzone-url="<?= $uploadUrl ?>"
    data-dropzone-max-filesize="<?= Attachment::getMaxFileSize('mb') ?>"
    data-dropzone-default-message="<?= __('Drop files to upload') ?>"
    data-dropzone-success-message="<?= __('successfully uploaded') ?>"
    data-dropzone-error-message="<?= __('Upload has failed') ?>"
>
    <div class="widget-add">

        <?php
        if ($Widget->isModalRequest()) {
            echo $this->Form->create('Comment', [
                'novalidate' => true,
                'url' => $commentUrl,
                'data-yjs-form' => $formName,
            ]);
        }
        ?>

    	<div id="comments-add-<?= $instanceId ?>">
    		<?= $this->element('Comments.Comments/add', ['FieldDataCollection' => $CommentFieldDataCollection]) ?>
    	</div>

    	<div>
    		<?= $this->Buttons->primary(__('Add Comment'), [
    			'data' => [
    				'yjs-request' => 'crud/submitForm',
    				'yjs-forms' => $formName,
    				'yjs-target' => '#comments-add-' . $instanceId,
                    'yjs-form-fields' => 'data[Comment][message]',
    				'yjs-datasource-url' => $commentUrl,
    				'yjs-event-on' => 'click',
    				'yjs-on-success-reload' => '#widget-story-list-' . $instanceId,
    			],
                'disabled' => ($this->AclCheck->check($commentUrl)) ? false : true
    		]);
    		?>

    		<button <?= (!$this->AclCheck->check($uploadUrl)) ? 'disabled="disabled"' : '' ?>" type="button" class="btn btn-success btn-ladda btn-ladda-progress ladda-button btn-widget-add-file" data-style="expand-right" data-spinner-size="20">
    			<span class="ladda-label"><?= __('Upload Attachment') ?></span>
    			<span class="ladda-spinner"></span>
    			<div class="ladda-progress"></div>
    		</button>
    	</div>

        <?php if ($this->AclCheck->check($uploadUrl)) : ?>
        	<div class="dropzone dropzone-widget-add hidden">
        	</div>
        <?php endif; ?>

        <?php
        if ($Widget->isModalRequest()) {
            $this->Form->end();
        }
        ?>
    </div>

    <script type="text/javascript">
    $(function() {
    	var showDropzone = false;
    });
    </script>

    <br>
    <hr>
    <br>

    <?php
    echo $this->Html->div('', '', [
    	'id' => 'widget-story-list-' . $instanceId,
        'class' => 'widget-story-list',
    	'data-yjs-request' => 'crud/load',
    	'data-yjs-target' => '#widget-story-list-' . $instanceId,
    	'data-yjs-datasource-url' => $storyUrl,
    	'data-yjs-event-on' => 'init',
    	'data-yjs-use-loader' => 'false',
    ]);
    ?>
</div>