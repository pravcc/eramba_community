<?php App::uses('AdvancedFilterUserSetting', 'Model'); ?>
<h5>
	<strong><?php echo __('Saved filters'); ?></strong>
</h5>
<br>
<?php if (!empty($savedFilters)) : ?>
	<div class="widget box">
		<table class="table">
			<thead>
				<tr>
					<th>
						<?php echo __('Name'); ?>
					</th>
					<th>
						<?php echo __('Section'); ?>
					</th>
					<th>
						<?php echo __('Number Of Results Log'); ?>
					</th>
					<th>
						<?php echo __('Full Results Log'); ?>
					</th>
					<th>
						<?php echo __('Private'); ?>
					</th>
					<th>
						<?php echo __('Default index'); ?>
					</th>
					<th>
						<?php echo __('Created'); ?>
					</th>
					<th class="text-right">
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($savedFilters as $item) : ?>
					<?php 
					$filterUrl = '';
				    // if (empty($filter['settings']['url'])) {
				    //     $filterUrl = Router::url(array('controller' => $this->request->params['controller'], 'action' => 'index', '?' => $this->AdvancedFilters->getFilterQuery($item)));
				    // }
				    // else {
				    //     $url = $filter['settings']['url'];
				    //     $url['?'] = $this->AdvancedFilters->getFilterQuery($item);
				    //     $filterUrl = Router::url($url);
				    // }
					$filterUrl = $this->AdvancedFilters->getFilterRedirectUrl($item['AdvancedFilter']['id']);
					?>
					<tr class="advanced-filter-delete-item">
						<td>
							<a href="<?php echo $filterUrl; ?>"><?php echo $item['AdvancedFilter']['name']; ?></a>
						</td>
						<td>
							<?php
							echo ClassRegistry::init($item['AdvancedFilter']['model'])->label();
							?>
						</td>
						<td>
							<?php echo getStatusFilterOption()[$item['AdvancedFilter']['log_result_count']]; ?>
						</td>
						<td>
							<?php echo getStatusFilterOption()[$item['AdvancedFilter']['log_result_data']]; ?>
						</td>
						<td>
							<?php echo getStatusFilterOption()[$item['AdvancedFilter']['private']]; ?>
						</td>
						<td>
							<?php
							$defaultIndex = (!empty($item['AdvancedFilterUserSetting']['default_index'])) ? $item['AdvancedFilterUserSetting']['default_index'] : AdvancedFilterUserSetting::NOT_DEFAULT_INDEX;
							echo getStatusFilterOption()[$defaultIndex];
							?>
						</td>
						<td>
							<?php echo date('d.m.Y', strtotime($item['AdvancedFilter']['created'])); ?>
						</td>
						<td class="text-right">
							<?php if ($item['AdvancedFilter']['user_id'] == $logged['id']) : ?>
								<a href="<?php echo $filterUrl . '#advanced-filter-edit'; ?>"><?php echo __('Edit') ?></a> | 
								<a href="<?php echo Router::url(array('plugin' => 'advanced_filters', 'controller' => 'advanced_filters', 'action' => 'deleteAdvancedFilter', $item['AdvancedFilter']['id'])); ?>" class="advanced-filter-delete" data-toggle="modal"><?php echo __('Delete'); ?></a>
							<?php else : ?>
								<small><?php echo __('Owned by %s', $item['User']['full_name']); ?></small>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<?php echo $this->element('not_found', array(
		'message' => __('No saved filters.')
	) ); ?>
<?php endif; ?>

<script type="text/javascript">
$(function() {
	$('.advanced-filter-delete').on('click', function() {
		result = confirm("<?php echo __('Are you sure ?'); ?>");
		elem = $(this);
		if (result) {
			$.ajax({
				url: $(this).attr('href'),
				dataType: 'json',
			}).done(function(response) {
				if (typeof response.success !== 'undefined') {
					elem.closest('.advanced-filter-delete-item').remove();
				}
				if (typeof response.error !== 'undefined') {
					noty({
						text: '<strong>' + response.error + '</strong>',
						type: 'error',
						timeout: 4000
					});
				}
			});
		}
		return false;
	});
});
</script>