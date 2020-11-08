<?php if (!empty($document['ReviewVersion'])) : ?>
	<ul class="list-unstyled">
		<?php foreach ($document['ReviewVersion'] as $log) : ?>
			<li class="revision">
				<?php
				$text = array();

				if (!empty($log['version'])) {
					$text[] = __('Version %s', $log['version']);
				}

				if (!empty($log['User'])) {
					$text[] = __('Updated by %s', $log['User']['name'] . ' ' . $log['User']['surname']);
				}

				$date = false;
				if (!empty($log['actual_date'])) {
					$date = ' ' . __('on %s', $log['actual_date']);
				}

				echo implode(', ', $text) . $date . '.';
				?>
				<?php if (!empty($log['description'])) : ?>
					<br />
					<small><?php echo __('<strong>Update notes:</strong> %s', $log['description']); ?></small>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<script type="text/javascript">
	jQuery(function($) {
		$('#version > div').slimScroll({
			height: '150px',
			// alwaysVisible: true,
			railVisible: true,
			railColor : '#e4e8e9',
			color: '#bbbfc0',
			size: '10px',
			opacity : 1,
			railOpacity : 1
		});
	});
	</script>
<?php else : ?>
	<?php
	echo __('No revisions found');
	?>
<?php endif; ?>