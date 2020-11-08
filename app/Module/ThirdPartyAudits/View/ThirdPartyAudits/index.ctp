<?php if (!empty($data)) : ?>
	<div class="widget box">
		<div class="widget-header">
	        <h4><?php echo __('Analyze'); ?></h4>
	    </div>
		<div class="widget-content">
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th><?php echo __('Third Party Audit'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data as $key => $item) : ?>
					<tr>
						<td><?php echo $this->Html->link($item['ComplianceAudit']['name'], $this->ComplianceAudits->getAnalyzeUrl($item['ComplianceAudit']['id'])); ?></td>
					</tr>
					<?php endforeach ; ?>
				</tbody>
			</table>
		</div>
	</div>
<?php else : ?>
	<?php
	echo $this->Ux->getAlert(__('There are no audits for you to analyze at the moment.'), [
		'type' => 'info'
	]);
	?>
<?php endif; ?>