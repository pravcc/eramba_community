<?php
if (!$this->request->is('ajax')) {
	$this->layout = 'custom_error';
}
?>
<?php if (empty($logged)): ?>
	<div class="text-center" style="margin-top: 40px">
		<h3><?= __('Not logged in'); ?></h3>
		<h5><?= __('It seems like your session expired. Login and try it again.'); ?></h5>
	</div>

	<div class="row" style="margin-bottom: 40px">
		<div class="col-sm-offset-4 col-sm-4 mt-20">
			<a href="<?= Router::url([
				'controller' => 'users',
				'action' => 'login',
				'plugin' => false,
				'prefix' => false,
				'admin' => false
				]); ?>" class="btn btn-primary btn-block content-group"><i class="icon-enter position-left"></i> <?= __('Login form'); ?></a>
		</div>
	</div>
<?php else: ?>
	<?php
		$msg = __('Oops, an error has occurred.');
		$subMsg = __('Something happened.');
		$autoSubMsg = true;
		$btnContainerClass = 'col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3';
		$btnColClass = 'col-sm-12';
		if ($error->getCode() == 404) {
			$msg .= ' ' . __('Page not found!');
			$subMsg = __('Requested page or item is no longer available or wrong. Check the URL and try again.');
		}

		if ($error->getCode() == 403) {
			$msg = __('Your account does not have permissions to access here');
			$subMsg = __('It seems like you don\'t have permission to access here, contact the administrator for assistance.');
			$autoSubMsg = false;
			$btnContainerClass = 'hidden';
		}

		if ($error->getCode() == 401) {
			$msg .= ' ' . __('Permissions problem!');
			$subMsg = __('It seems like you don\'t have permission to go to this location.');
		}

		if ($error->getCode() == 400) {
			$msg .= ' ' . __('We have blocked this request!');
			$subMsg = __('This could have happened for a variety of reasons, for example you have refreshed this page multiple times, you have accessed a part of the system you have no access, Etc. Close the window and start again.<br /><br />If you can reproduce this message and you are sure is an error report it as a bug.');
		}

		if ($autoSubMsg) {
			$subMsg .= ' ' . __('If the problem persist, please download information about this error by clicking on button below and send it to your administrator.');
		}
	?>
	<!-- Error title -->
	<div class="text-center content-group">
		<h4 style="margin-top: 40px"><?= $msg ?></h4>
		<h6><?= $subMsg ?></h6>
	</div>
	<!-- /error title -->

	<!-- Error content -->
	<div class="row">
		<div class="<?= $btnContainerClass ?>">
			<div class="row">
				<div class="<?= $btnColClass ?>">
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
						<button type="submit" class="btn btn-primary btn-block content-group"><i class="icon-arrow-down16 position-left"></i> <?= __('Download error'); ?></button>
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
<?php endif; ?>

