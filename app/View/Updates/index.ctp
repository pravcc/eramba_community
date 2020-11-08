<?php 
if (Configure::read('Eramba.offline')) {
	echo $this->Html->div('alert alert-info bg-info label-custom-alert', __('Eramba is running in offline mode, updating using web interface is not possible.<br><br>To update your application, open terminal and from /app directory use command:<br><em>Console/cake update update {path_to_update_package}</em>'));
	return true;
}
?>
<div id="updates-wrapper">
	<?php echo $this->element('updates/updateWidget'); ?>
</div>

<script type="text/javascript">
$(function() {

	function update(elem) {
		elem.attr('disabled', true);
		$('#updates-wrapper .progress').slideDown(300);

		$.ajax({
			url: elem.attr('href'),
		}).done(function(response) {
			$('#updates-wrapper .progress').slideUp(300, function() {
				$('#updates-wrapper').html(response);
			});
		}).always(function() {
		});
	}

	$('#updates-wrapper').on('click', '.btn-update', function() {
		update($(this));
		return false;
	});
});
</script>