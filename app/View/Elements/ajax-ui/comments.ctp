<?php
echo $this->Form->create('Comment', array(
	'url' => array('plugin' => null, 'controller' => 'comments', 'action' => 'addAjax', $model, $foreign_key),
	'class' => 'form-vertical comment-form sidebar-widget-form',
	'id' => 'comment-form',
	'novalidate' => true
));
?>

<div>
	<div class="form-group">
		<label class="control-label"><?php echo __('Post a comment'); ?>:</label>
		<?php
		echo $this->Form->input('message', array(
			'placeholder' => __('Enter a comment...'),
			'label' => false,
			'div' => false,
			'class' => 'form-control',
			'rows' => 4
		));
		?>
	</div>

	<div class="form-group">
		<?php
		echo $this->Form->submit(__('Add Comment'), array(
			'class' => 'btn btn-primary',
			'div' => false
		));
		?>
	</div>
</div>

<?php echo $this->Form->end(); ?>

<?php if (!empty($comments)) : ?>
	<div id="comments-list-wrapper">
		<?php
		echo $this->element('ajax-ui/commentsList', array(
			'comments' => $comments
		));
		?>
	</div>

	<?php
	if (!$noMoreComments) {
		echo $this->Html->link(__('Load more (%s)', $paginateLabel) . ' <i class="pull-right icon-angle-right"></i>', array(
			'controller' => 'comments',
			'action' => 'listComments',
			$model,
			$foreign_key
		), array(
			'class' => 'more ajax-load-comments',
			'data-ajax-action' => 'load-comments',
			'escape' => false
		));
	}
	?>
	<!-- <a class="more" href="javascript:void(0);">Load more (6) <i class="pull-right icon-angle-right"></i></a> -->
<?php else : ?>
	<div class="alert alert-info"><?php echo __('No comments found.'); ?></div>
<?php endif; ?>