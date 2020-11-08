<?php
/**
 * @deprecated in favour of AdvancedFiltersHelper::getViewList() or AdvancedFiltersHelper::getViewItems().
 */
?>
<?php if (!empty($savedFilters)) : ?>
	<?php foreach ($savedFilters as $item) : ?>
		<?php 
		$filterParams = array();
		$filterParams['advanced_filter_id'] = $item['AdvancedFilter']['id'];
		foreach ($item['AdvancedFilterValue'] as $value) {
		 	if ($value['many'] == ADVANCED_FILTER_VALUE_MANY) {
				$filterParams[$value['field']] = explode(',', $value['value']);
			}
			else {
				$filterParams[$value['field']] = $value['value'];
			}
		}
		?>
		<li>
			<a href="<?php echo Router::url(array('plugin' => null, 'controller' => $this->request->params['controller'], 'action' => 'index', '?' . http_build_query($filterParams))); ?>">
				<?php echo $item['AdvancedFilter']['name']; ?>
			</a>
		</li>
	<?php endforeach; ?>
<?php endif; ?>

