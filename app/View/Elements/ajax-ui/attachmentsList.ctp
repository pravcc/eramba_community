<?php if (!empty($attachments)) : ?>
	<table class="table table-hover table-striped">
		<thead>
			<tr>
				<th><?php echo __('Filename'); ?></th>
				<th><?php echo __('Uploaded'); ?></th>
				<th class="align-center"><?php echo __('Action'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($attachments as $file) : ?>
				<tr>
					<td>
						<?php
						echo basename($file['Attachment']['name']);
						?>
					</td>
					<td><?php echo $file['Attachment']['created']; ?></td>
					<td class="align-center">
						<ul class="table-controls">
							<li>
								<?php
								echo $this->Attachments->downloadLink($file);
								?>
							</li>
							<li>
								<?php
								echo $this->Html->link('<i class="icon-trash"></i>', array(
									'plugin' => null,
									'controller' => 'attachments',
									'action' => 'delete',
									$file['Attachment']['id'],
									time()
								), array(
									'class' => 'bs-tooltip',
									'data-ajax-action' => 'delete',
									'escape' => false,
									'title' => __('Trash')
								));

								/*echo $this->Js->link('<i class="icon-trash"></i>', array(
									'controller' => 'attachments',
									'action' => 'deleteAjax',
									$file['Attachment']['id']
								), array(
									'update' => '#attachments-content-files',
									//'before' => '',
									//'complete' => '',
									'class' => 'bs-tooltip',
									'title' => __('Trash'),
									'buffer' => false,
									'escape' => false
								));*/
								?>
							</li>
						</ul>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<div class="alert alert-info"><?php echo __('No attachments found.'); ?></div>
<?php endif; ?>

<script type="text/javascript">
jQuery(function($) {
	/*$("#comment-form").on("submit", function(e) {
		e.preventDefault();

		$.ajax({
			type: "POST",
			url: $(this).prop("action"),
			data: $(this).serialize()
		}).done(function(html) {
			var ele = $(html);
			$("#comments-content").html(ele);
			ele.find(".transition-obj.masked").focus();
			ele.find(".transition-obj.masked").removeClass("masked");

			App.setWidgets();
		});
	});*/
	Eramba.Ajax.UI.attachEvents();
});
</script>

<?php /*if (isset($okMessage)) : ?>
	<?php echo $this->element('messages/ajax-flash-ok', array('okMessage' => $okMessage)); ?>
<?php endif; ?>

<?php if (isset($errorMessage)) : ?>
	<?php echo $this->element('messages/ajax-flash-ok', array('errorMessage' => $errorMessage)); ?>
<?php endif;*/ ?>