<?php
$Model = ClassRegistry::init($model);
$headings = $AttributeInstance->listAttributes($Model);
?>
<table class="table table-hover table-striped table-dashboard">
	<thead>
		<tr>
			<th>
				<?php echo __('Custom Role'); ?>
			</th>

			<?php foreach ($headings as $slug) : ?>
				<th>
					<?php
					$title = $AttributeInstance->templateInstance($Model, $slug)->getTitle($Model->label());
					echo $title;
					?>
				</th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>

	<?php foreach ($Model->Behaviors->CustomRoles->getModelSettings($Model, true) as $customRole => $roleLabel) : ?>
		<tr>
			<td>
				<?php
				echo $roleLabel;
				?>
			</td>

			<?php foreach ($headings as $slug) : ?>
				<?php
				$attrs = [
					'User' => $slug,
					'CustomRoles.CustomRole' => $customRole
				];
				
				$item = $this->DashboardKpi->searchKpiByAttributes($items, $attrs);
				$value = $item['DashboardKpi']['value'];
				?>
				<td>
					<?php
					if ($value !== null) {
						echo $this->DashboardKpi->getKpiLink($model, $item);
					}
					else {
						echo $this->DashboardKpi->noValueTooltip();
					}
					?>
				</td>
			<?php endforeach; ?>
		</tr>

	<?php endforeach; ?>
	</tbody>
</table>