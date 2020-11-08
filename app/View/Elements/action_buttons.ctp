<ul class="table-controls">
	<?php if ( isset( $controller ) ) : ?>
	<li>
		<?php
		$attr = array(
			'class' => 'bs-tooltip',
			'escape' => false,
			'title' => __('Edit')
		);

		if (isset($ajaxEdit) && $ajaxEdit) {
			$attr['data-ajax-action'] = 'edit';
		}

		echo $this->Html->link('<i class="icon-pencil"></i>', array(
			'controller' => $controller,
			'action' => 'edit',
			$id
		), $attr);
		?>
	</li>
	<?php endif; ?>
	<?php if ( isset( $attachment ) ) : ?>
		<?php
		$extraClass = '';
		if ( isset( $attachmentCount ) && $attachmentCount ) {
			$extraClass = 'has-attachments';
		}
		?>
		<li>
			<?php
			echo $this->Html->link('<i class="icon-cloud-upload ' . $extraClass . '"></i>', array(
				'controller' => 'attachments',
				'action' => 'index',
				$attachment,
				$id
			), array(
				'class' => 'bs-tooltip',
				'escape' => false,
				'title' => __('Attachments')
			));
			?>
		</li>
	<?php endif; ?>
	<?php if ( isset( $comment ) ) : ?>
		<?php
		$extraClass = '';
		if ( isset( $commentCount ) && $commentCount ) {
			$extraClass = 'has-attachments';
		}
		?>
		<li>
			<?php
			echo $this->Html->link('<i class="icon-comments ' . $extraClass . '"></i>', array(
				'controller' => 'comments',
				'action' => 'index',
				$comment,
				$id
			), array(
				'class' => 'bs-tooltip',
				'escape' => false,
				'title' => __( 'Comments' )
			));
			?>
		</li>
	<?php endif; ?>

	<?php if (isset($notificationSystem)) : ?>
		<li>
			<?php
			echo $this->Html->link('<i class="icon-info-sign"></i> ', array(
				'controller' => 'notificationSystem',
				'action' => 'attach',
				$notificationSystem,
				$id
			), array(
				'class' => 'bs-tooltip',
				'escape' => false,
				'title' => __('Notifications')
			));
			?>
		</li>
	<?php endif; ?>

	<?php if (isset($customActions) && !empty($customActions)) : ?>
		<?php foreach ($customActions as $action) : ?>
			<li>
				<?php
				echo $this->Html->link('<i class="icon-' . $action['icon'] . '"></i>', $action['url'], $action['options']);
				?>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if (isset($workflowRecords)) : ?>
		<li>
			<?php
			echo $this->Html->link('<i class="icon-cog"></i>', array(
				'controller' => 'systemRecords',
				'action' => 'index',
				$workflowRecords,
				$id
			), array(
				'class' => 'bs-tooltip',
				'escape' => false,
				'title' => __('Records')
			));
			?>
		</li>
	<?php endif; ?>
	<?php if ( isset( $controller ) ) : ?>
	<li>
		<?php
		$attr = array(
			'class' => 'bs-tooltip',
			'escape' => false,
			'title' => __( 'Trash' )
		);
		if (isset($ajaxEdit) && $ajaxEdit) {
			$attr['data-ajax-action'] = 'delete';
		}
		echo $this->Html->link('<i class="icon-trash"></i>', array(
			'controller' => $controller,
			'action' => 'delete',
			$id
		), $attr);
		?>
	</li>
	<?php endif; ?>
</ul>