<ul class="comments-list">
	<?php foreach ($comments as $comment) : ?>
		<?php
		$extraClass = '';
		if (isset($addedId) && $comment['Comment']['id'] == $addedId) {
			$extraClass = 'class="transition-obj transition-fade masked"';
		}
		?>
		<li <?php echo $extraClass; ?>>
			<small class="pull-right">
				<?php
				echo CakeTime::timeAgoInWords($comment['Comment']['created'], array(
					'end' => '1 day',
					'format' => 'Y-m-d'
				));
				?>
			</small>

			<h5><?php echo $comment['User']['full_name']; ?></h5>
			<?php
			echo $this->Html->link('<i class="icon-trash"></i>', array(
				'controller' => 'comments',
				'action' => 'delete',
				$comment['Comment']['id'],
				time()
			), array(
				'class' => 'bs-tooltip pull-right',
				'data-ajax-action' => 'delete',
				'escape' => false,
				'title' => __('Trash')
			));?>
			<p><?php echo $this->Eramba->getEmptyValue(h($comment['Comment']['message'])); ?></p>
		</li>
	<?php endforeach; ?>
</ul>