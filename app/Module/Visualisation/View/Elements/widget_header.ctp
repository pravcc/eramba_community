<?php
// enable manage button on the widget
if (!isset($manageBtn)) {
	$manageBtn = true;
}

if (isset($visualisationEnabled)) {
	$settings = $this->Visualisation->getSectionLink($visualisationModel, '<i class="icon-pencil"></i> ' . __('Settings'));
}

if (isset($visualisationEnabled) && $visualisationEnabled) : ?>
	<?php
	$tags = [
		'primary' => [],
		'info' => [],
		'danger' => [],
	];

	$prefixLabel = __('Visualisation Active');
	$totalLabel = __n('%d item accessible in total', '%d items accessible in total', $visualisationTotal, $visualisationTotal);

	// user has general perission for access in the section (admin, exempted user)
	// if ($visualisationNoRestriction === true) {
		if (isAdmin($logged)) {
			$accessLabel = __('You are in the admin group, you can see all items in this section');
			$tags['primary'][] = $accessLabel;
		}
		else {
			// $accessLabel = __('No restrictions');
			$accessLabel = false;
		}

		
		// $label = sprintf('%s - %s', $prefixLabel, $accessLabel);
	// }
	// user had his permissions checked 
	// else {
		$accessCount = $visualisationPagination['readable'];//count($visualisation['accessibleForeignKeys']);
		$noAccessCount = $visualisationPagination['nonReadable'];//count($visualisation['inaccessibleForeignKeys']);

		if (!isAdmin($logged)) {
			// $accessLabel = __n('%d item accessible in total', '%d items accessible in total', $accessCount, $accessCount);
			$noAccessLabel = __n('%d item have been hidden from you', '%d items have been hidden from you', $noAccessCount, $noAccessCount);

			// $tags['info'][] = $accessLabel;
			$tags['danger'][] = $noAccessLabel;
		}

		// $label = sprintf(
		// 	'%s - %s (%s)',
		// 	$prefixLabel,
		// 	$accessLabel,
		// 	$noAccessLabel
		// );
	// }
	?>
	<div class="widget box widget-active-filter">
		<div class="widget-header">
			<h4><?php echo __('Visualisation Active'); ?></h4>
		</div>
		<div class="widget-content">
			<div class="btn-toolbar">
				<?php
				$output = [];
				foreach ($tags as $class => $labels) {
					foreach ($labels as $text) {
						$output[] = $this->Html->tag('span', $text, [
							'class' => 'label label-' . $class
						]);
					}
				}

				echo implode(' ', $output);
				?>
			</div>
		</div>
	</div>
<?php elseif (isset($visualisationEnabled) && !$visualisationEnabled) : ?>
<div class="widget box widget-active-filter">
	<div class="widget-header">
		<h4><?php echo __('Visualisation Not Active'); ?></h4>
		<?php if (isAdmin($logged) && $manageBtn) : ?>
			<div class="toolbar no-padding">
				<div class="btn-group">
					<span class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
						<?php echo __( 'Manage' ); ?> <i class="icon-angle-down"></i>
					</span>
					<ul class="dropdown-menu manage-dropdown-menu pull-right">
						<li>
							<?php
							echo $settings;
							?>
						</li>
					</ul>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="widget-content">
		<div class="btn-toolbar">
			<?php
			echo $this->Html->tag('span', __('Visualisations are Off - as long as users have access, even non-admins will see all items listed in this section'), [
				'class' => 'label label-default'
			]);
			?>
		</div>
	</div>
</div>
<?php endif; ?>