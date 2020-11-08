<div type="info" class="label border-left-info label-striped label-custom-alert cron-type-cli" id="cron-type-cli" style="display: none; text-transform: none;">
    <?php
    $cronCmd = ROOT . '/app/Console/cake cron';
    ?>
    <strong><?= __('To test the CLI access, run this:') ?></strong><br>
    <?= $cronCmd ?> test<br>
    <?= ROOT . '/app/Console/cake system_health check' ?> (Hourly, Daily and Yearly failed checks are normal if you are installing eramba for the first time)<br>
    <br>
    <strong><?= __('To Configure CRON jobs set up these:') ?></strong><br>
    @hourly su -s /bin/bash -c "<?= $cronCmd ?> job hourly" <?= get_current_user() ?><br>
    @daily su -s /bin/bash -c "<?= $cronCmd ?> job daily" <?= get_current_user() ?><br>
    @yearly su -s /bin/bash -c "<?= $cronCmd ?> job yearly" <?= get_current_user() ?><br>
    <br>
	<?= __('Warning') ?>:<br>
	<?= __('%s configuration file needs to be the same for %s as for %s.', 'php.ini', 'CLI', 'apache2') ?><br>
	<br>
    <?= __('Note') ?>:<br />
    <?= __('%s must be executable, use %s to adjust permissions so the command can run. These commands might change slightly depending on the %s flavour.', 'Console/cake', 'chmod', 'linux') ?>
</div>

<div type="info" class="label border-left-info label-striped label-custom-alert cron-type-web" id="cron-type-web" style="display: none; text-transform: none;">
	<strong> <?= __('To test the Cron run the following URL on your browser and wait until each one of them completes:') ?></strong><br>
    <?= Configure::read('App.fullBaseUrl') ?>/cron/hourly/<span class="cron-key-live"></span><br>
    <?= Configure::read('App.fullBaseUrl') ?>/cron/daily/<span class="cron-key-live"></span><br>
    <?= Configure::read('App.fullBaseUrl') ?>/cron/yearly/<span class="cron-key-live"></span><br>
    <br>
    <strong><?= __('Configure CRON jobs as:') ?></strong><br>
    @hourly /usr/bin/wget -O /dev/null --no-check-certificate <?= Configure::read('App.fullBaseUrl') ?>/cron/hourly/<span class="cron-key-live"></span><br>
    @daily /usr/bin/wget -O /dev/null --no-check-certificate <?= Configure::read('App.fullBaseUrl') ?>/cron/daily/<span class="cron-key-live"></span><br>
    @yearly /usr/bin/wget -O /dev/null --no-check-certificate <?= Configure::read('App.fullBaseUrl') ?>/cron/yearly/<span class="cron-key-live"></span><br>
    <br>
    <?= __('Note') ?>:<br />
    <?= __('We recommend NOT using %s and switch instead to %s %s jobs.', 'WEB', 'CLI', 'Crontab') ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	function toggleCronType()
	{
		var cronTypeValue = $('#SettingCRONTYPE').val();

		if (cronTypeValue == 'cli') {
			$('.cron-type-cli').show();
			$('.cron-type-web').hide();
			$('#SettingCRONURL').attr('disabled', false);
		}
		else {
			$('.cron-type-web').show();
			$('.cron-type-cli').hide();
			$('#SettingCRONURL').attr('disabled', true);
		}
	}

	$('#SettingCRONTYPE').on('change', function() {
		toggleCronType();
	}).trigger('change');

    $("#SettingCRONSECURITYKEY").on("keyup", function(e) {
    	$(".cron-key-live").text($(this).val());
    }).trigger("keyup");
});
</script>