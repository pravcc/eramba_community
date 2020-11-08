<li id="news-dropdown" class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<i class="icon-envelope"></i>
		<span class="visible-xs-inline-block position-right"><?= __('News'); ?></span>
		<?php if (!empty($unreadedNewsCount)) : ?>
			<span class="badge bg-warning-400"><?php echo $unreadedNewsCount; ?></span>
		<?php endif; ?>
	</a>
	
	<div class="dropdown-menu dropdown-content width-350">
		<div class="dropdown-content-heading">
			<?= __('News'); ?>
			<ul class="icons-list">
				<li><a href="#"><i class="icon-gear"></i></a></li>
			</ul>
		</div>

		<ul class="media-list dropdown-content-body">
			<?php if (!empty($shortNews)) : ?>
				<?php foreach ($shortNews as $message) : ?>
					<li class="media">
						<div class="media-body">
							<a href="<?= Router::url(array('plugin' => null, 'controller' => 'news', 'action' => 'index')); ?>" class="media-heading">
								<span class="text-muted"><?= $message['title']; ?></span>
								<span class="media-annotation pull-right">
									<?php
									echo CakeTime::timeAgoInWords($message['date'], array(
										'accuracy' => array('day' => 'day', 'hour' => 'day')
									));
									?>
								</span>
							</a>
						</div>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>

		<div class="dropdown-content-footer">
			<?php
			echo $this->Html->link('<i class="icon-menu display-block"></i>', [
				'controller' => 'news',
				'action' => 'index'
			],
			[
				'escape' => false,
				'data-popup' => 'tooltip',
				'title' => __('View all news')
			]);
			?>
		</div>
	</div>
</li>
<?php if (!empty($unreadedNewsCount)) : ?>
	<script type="text/javascript">
	$(function() {
		function sendReadResponse() {
			$.ajax({
				url: "<?php echo Router::url(array('plugin' => null, 'controller' => 'news', 'action' => 'markAsRead')); ?>",
			}).done(function(response) {
				$('#news-dropdown .dropdown-toggle .badge').hide();
			}).always(function() {
			});
		}

		$('#news-dropdown .dropdown-toggle').on('click', function() {
			sendReadResponse();
		});
	});
	</script>
<?php endif; ?>