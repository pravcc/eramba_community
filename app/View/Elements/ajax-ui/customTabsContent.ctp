<?php
if (!isset($activeModule)) {
	$activeModule = 'comments';
}
?>
<div class="tab-pane fade <?php if($activeModule == 'comments')echo 'in active'; ?>" id="comments">
	<div id="comments-content">
		<?php
		echo $this->element('ajax-ui/comments', array(
			'comments' => $comments,
			'model' => $model,
			'foreign_key' => $foreign_key
		));
		?>
	</div>
</div>

<div class="tab-pane fade <?php if($activeModule == 'records')echo 'in active'; ?>" id="records">
	<?php
	echo $this->element('ajax-ui/records', array(
		'records' => $records,
		'model' => $model,
		'foreign_key' => $foreign_key
	));
	?>
</div>

<div class="tab-pane fade <?php if($activeModule == 'attachments')echo 'in active'; ?>" id="attachments">
	<div id="attachments-content">
		<?php
		echo $this->element('ajax-ui/attachments', array(
			'data' => $attachments,
			'model' => $model,
			'foreign_key' => $foreign_key
		));
		?>
	</div>
</div>

<?php if (isset($notificationsModule) && $notificationsModule) : ?>
	<div class="tab-pane fade <?php if($activeModule == 'notifications')echo 'in active'; ?>" id="notifications">
		<div id="notifications-widget-content">
			<?php
			echo $this->element('ajax-ui/notifications', array(
				'notifications' => $notifications,
				'model' => $model,
				'foreign_key' => $foreign_key
			));
			?>
		</div>
	</div>
<?php endif; ?>