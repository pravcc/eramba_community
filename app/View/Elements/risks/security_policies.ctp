<?php
if (empty($widgetTitle)) {
	$widgetTitle = __('Security Policies');
}
?>
<div class="widget box widget-closed">
	<div class="widget-header">
		<h4><?php echo $widgetTitle; ?></h4>
		<div class="toolbar no-padding">
			<div class="btn-group">
				<span class="btn btn-xs widget-collapse"><i class="icon-angle-up"></i></span>
			</div>
		</div>
	</div>
	<div class="widget-content" style="display:none;">
		<?php if ( ! empty($data)) : ?>
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th><?php echo __('Document Type'); ?></th>
						<th><?php echo __('Title'); ?></th>
						<th><?php echo __('Status'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data as $document) : ?>
					<?php
					$docLabel = __('%s Document', $document['SecurityPolicyDocumentType']['name']);
					?>
					<tr>
						<td>
							<?php echo $this->Ux->getItemLink($docLabel, 'SecurityPolicy', $document['id']);?>
							<?php echo $this->SecurityPolicies->documentLink($document); ?>
						</td>
						<td><?php echo $document['index']; ?></td>
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
			echo $this->element('not_found', array(
				'message' => __('No Security Policies found.')
			));
			?>
		<?php endif; ?>
	</div>
</div>