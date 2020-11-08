<?php
if (empty($widgetTitle)) {
	$widgetTitle = __('Security Policies');
}
?>
<div class="row">
	<div class="col-xs-12">

		<div class="header-separator"></div>
		<div class="header">
			<div class="subtitle">
				<h2>
					<?php echo $widgetTitle; ?>
				</h2>
			</div>
		</div>

	</div>
</div>
<div class="row">
	<div class="col-xs-12">

		<div class="body">
			<div class="item">
				<?php if ( ! empty($data)) : ?>
					<table class="triple-column">
						<thead>
							<tr>
								<th>
									<?php echo __('Title'); ?>
								</th>
								<th>
									<?php echo __('Type'); ?>
								</th>
								<th>
									<?php echo __('Status'); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($data as $document) : ?>
							<?php
							$docLabel = __('%s Document', $document['SecurityPolicyDocumentType']['name']);
							?>
							<tr>
								<td><?php echo $document['index']; ?></td>
								<td><?php echo $docLabel; ?></td>
								<td>
									<?php
									echo $this->SecurityPolicies->getStatuses($document);
									?>
								</td>
							</tr>
							<?php endforeach ; ?>
						</tbody>
					</table>
				<?php else : ?>
					<?php
					echo $this->Html->div('alert', 
						__('No Security Policies found.')
					);
					?>
				<?php endif; ?>

			
			</div>
		</div>

	</div>
</div>