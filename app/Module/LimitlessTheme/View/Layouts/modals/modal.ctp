<div class="modal-content modal-content-custom">
	<div class="modal-header <?= $modal->getHeader('class'); ?>" title="<?= $modal->getHeader('headingClean'); ?>">
		<h5 class="modal-title"><?= $modal->getHeader('heading'); ?></h5>
		<?php
			if ($modal->getHeader('buttons.close') == true) {
				echo $this->Html->tag('button', '<i class="icon-x"></i>', array_merge([
					'data-yjs-request' => 'app/closeModal',
					'data-yjs-modal-id' => $modal->getModalId(),
					'data-yjs-use-loader' => 'false'
				], $modal->getFooter('buttons.closeBtn.options', []),
				[
					'escape' => false,
					'class' => 'close',
					'data-yjs-event-on' => 'click|keyup-27-detach'
				]));
			}
		?>
	</div>
	<div class="modal-body">
		<?php
			if ($modal->getBody() !== '') {
				echo $modal->getBody();
			} else {
				echo $this->fetch('content');
			}
		?>
	</div>
	<div class="modal-footer">
		<?php
			foreach ($modal->getFooter('buttons', []) as $btn) {
				echo $this->Html->tag($btn['tag'], $btn['text'], $btn['options']);
			}
		?>
	</div>
</div>
<script>$('#main-content').trigger('Eramba.Modal.loadHtml');</script>