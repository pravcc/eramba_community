<?php
$hasIssues = 0;
$queryLog = $this->Eramba->getQueryLogs();		
$extraClass1 = '';
if ((int) $queryLog['count'] > 1000 || $queryLog['time'] > 1000) {
	$hasIssues++;
	$extraClass1 = 'warning';
}

$scriptExecutionTime = scriptExecutionTime()*1000;
$extraClass2 = '';
if ($scriptExecutionTime > 2500) {
	$hasIssues++;
	$extraClass2 = 'warning';
}

$memoryWarning = 55000000; //55mb
App::uses('DebugMemory', 'DebugKit.Lib');
$memory1 = DebugMemory::getCurrent();
$extraClass3 = '';
if ($memory1 > $memoryWarning) {
	$hasIssues++;
	$extraClass3 = 'warning';
}

$memory2 = DebugMemory::getPeak();		
$extraClass4 = '';
if ($memory2 > $memoryWarning) {
	$hasIssues++;
	$extraClass4 = 'warning';
}

$debug = [
	__('SQL Queries') => [
		'icon' => 'icon-database-menu',
		'issue' => (bool) $extraClass1,
		'value' => sprintf('%d queries (%sms)', $queryLog['count'], $queryLog['time'])
	],
	__('Request Time') => [
		'icon' => 'icon-sort-time-asc',
		'issue' => (bool) $extraClass2,
		'value' => sprintf('%sms', CakeNumber::precision($scriptExecutionTime, 0))
	],
	__('Current Memory') => [
		'icon' => 'icon-download10',
		'issue' => (bool) $extraClass3,
		'value' => CakeNumber::toReadableSize($memory1)
	],
	__('Peak Memory') => [
		'icon' => 'icon-align-top',
		'issue' => (bool) $extraClass4,
		'value' => CakeNumber::toReadableSize($memory2)
	],
];
?>
<li class="dropdown debug-dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"> 
		<i class="icon-warning22"></i> 
		<?php
		$class = 'badge';
		if ($hasIssues != 0) {
			$class = 'badge badge-counter position-right';
		}
		?>
		<span class="<?php echo $class; ?>"><?php echo $hasIssues; ?></span>
	</a>

	<div class="dropdown-menu dropdown-content width-350">
		<div class="dropdown-content-heading text-center">
			<?= __('Issues'); ?>
		</div>

		<ul class="media-list dropdown-content-body">
			<li class="media">
				<div class="media-body">
					<span class="text-muted">
						<?= sprintf(__n('You have %d issue', 'You have %d issues', $hasIssues), $hasIssues); ?>
					</span>
				</div>
			</li>
			<li class="media">
				<div class="media-body">
					<?php foreach ($debug as $title => $data) : ?>
						<a href="javascript:void(0);" class="media-heading">
							<span class="label label-<?= $data['issue'] ? 'danger' : 'info'; ?>">
								<i class="<?= $data['icon']; ?>"></i>
							</span> 
							<span class="text-muted"><?= $title; ?></span>
							<span class="media-annotation pull-right <?= $data['issue'] ? 'text-danger' : ''; ?>"><?= $data['value']; ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</li>

		</ul>

		<div class="dropdown-content-footer">
			<?php
			echo $this->Html->link('<i class="icon-menu display-block"></i>', '#',
			[
				'escape' => false,
				'data-popup' => 'tooltip',
				'title' => __('View all issues')
			])
			?>
		</div>
	</div>
</li>

<script type="text/javascript">
	jQuery(function($) {
		$(".debug-dropdown").detach().appendTo($(".breadcrumb-elements:first"));
	});
</script>