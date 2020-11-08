<?php
if (!$this->request->is('ajax')) {
	$this->layout = 'custom_error';
}
?>
<?php
$msg = __('Oops, an internal error has occurred.');
$subMsg = __('This could have happened for a variety of reasons. Please download information about this error by clicking on button below and send it to your administrator.');
?>

<!-- Error title -->
<div class="text-center content-group">
	<h4 style="margin-top: 40px"><?= $msg ?></h4>
	<h6><?= $subMsg ?></h6>
</div>
<!-- /error title -->

<!-- Error content -->
<div class="row">
	<div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3">
		<div class="row">
			<div class="col-sm-12">
				<form id="#error-info-form" method="post" action="<?= Router::url([
						'plugin' => false,
						'controller' => 'settings',
						'action' => 'zipErrorLogFiles'
					]); ?>">
					<input type="hidden" name="error-code" value="<?= $error->getCode(); ?>">
					<input type="hidden" name="error-message" value="<?= $message; ?>">
					<input type="hidden" name="error-time" value="<?= date('Y-m-d H:i:s', time()); ?>">
					<input type="hidden" name="error-url" value="<?= $url; ?>">
					<input type="hidden" name="error-stacktrace" value="<?= htmlspecialchars($this->element('exception_stack_trace')); ?>">
					<button type="submit" class="btn btn-primary btn-block content-group"><i class="icon-arrow-down16 position-left"></i> <?= __('Download file with error info'); ?></button>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="row mb-20">
	<div class="col-lg-8 col-lg-offset-2 col-sm-12">
		<h3><?= __('Details') . ':'; ?></h3>
		<table id="error-details" cellspacing="10" cellpadding="10">
			<tr>
				<td><?= __('File') . ':'; ?></td>
				<td><?= $error->getFile(); ?></td>
			</tr>
			<tr>
				<td><?= __('Line') . ':'; ?></td>
				<td><?= $error->getLine(); ?></td>
			</tr>
			<tr>
				<td><?= __('Status code') . ':'; ?></td>
				<td><?= $error->getCode(); ?></td>
			</tr>
			<tr>
				<td><?= __('Message') . ':'; ?></td>
				<td><?= $message; ?></td>
			</tr>
			<tr>
				<td><?= __('Description') . ':'; ?></td>
				<td><?php
					printf(
						__d('cake', 'The requested address %s was not found on this server or you don\'t have access to go there.'),
						"<strong>'{$url}'</strong>");
					?>
				</td>
			</tr>
		</table>
		<style>
			#error-details td {
				vertical-align: top;
				padding: 5px 10px;
			}

			#error-details td:nth-child(odd) {
				white-space: nowrap;
				font-weight: bold;
			}
		</style>
	</div>
</div>
<?php if (Configure::read('debug') > 0): ?>
<hr>
<div class="row mb-20">
	<div class="col-sm-12">
		<?= $this->element('exception_stack_trace'); ?>
	</div>
</div>
<?php endif; ?>
<!-- /error wrapper -->



