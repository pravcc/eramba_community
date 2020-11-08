<?php
App::uses('DashboardKpi', 'Dashboard.Model');

$Model = ClassRegistry::init($model);
$headings = $ComplianceTypeInstance->listAttributes(ClassRegistry::init('ThirdParty'));
$data = $data[$model][DashboardKpi::CATEGORY_COMPLIANCE];
?>
<table class="table table-hover table-dashboard">
	<thead>
		<tr>
			<th>
				<?php echo __('Compliance'); ?>
			</th>

			<?php foreach ($headings as $slug) : ?>
				<th>
					<?php
					$label = $ComplianceTypeInstance->templateInstance($Model, $slug)->getTitle();
					echo sprintf($label, $Model->label());
					?>
				</th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>

	<?php foreach ($compliancePackageRegulators as $attribute => $title) : ?>
		<tr>
			<td>
				<?php
				echo $title;
				?>
			</td>

			<?php foreach ($headings as $slug) : ?>
				<?php
				$attrs = [
					'ComplianceType' => $slug,
					'ComplianceManagement' => $attribute
				];
				
				$item = $this->DashboardKpi->searchKpiByAttributes($items, $attrs);
				$value = $item['DashboardKpi']['value'];
				?>
				<?php
				// class style and dataAttrs
				extract($this->DashboardKpi->getThresholdParams($item));
				?>
				<td class="<?= $class; ?>" style="<?= $style; ?>" <?= $dataAttrs; ?>>
					<?php
					if ($value !== null) {
						echo $this->DashboardKpi->getKpiLink($model, $item);
					}
					else {
						echo $this->DashboardKpi->noValueTooltip();
					}
					?>
					<?php
					echo $this->Ajax->getActionList($item['DashboardKpi']['id'], array(
						'style' => 'icons',
						'trash' => false,
						'edit' => true,
						'comments' => false,
						'records' => false,
						'attachments' => false,
						'item' => $item
					));
					?>
				</td>
			<?php endforeach; ?>
		</tr>

	<?php endforeach; ?>
	</tbody>
</table>