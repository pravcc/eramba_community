<?php
App::uses('AwarenessProgram', 'Model');

$frameSizes = AwarenessProgram::textFileFrameSizes();
$containerStyles = '';
$iframeStyles = '';

if (isset($frameSizes[$program['AwarenessProgram']['text_file_frame_size']])) {
	$config = $frameSizes[$program['AwarenessProgram']['text_file_frame_size']];

	$containerStyles = "max-width: {$config['width']};";
	$iframeStyles = "min-height: {$config['height']}; max-height: {$config['height']};";
}
?>
<div class="container main-container" style="<?= $containerStyles; ?>">
	<div class="awareness-wrapper">
		<div class="headings">
			<h2 class="awareness-title"><?php echo $program['AwarenessProgram']['welcome_text']; ?></h2>
			<h3 class="awareness-subtitle"><?php echo $program['AwarenessProgram']['welcome_sub_text']; ?></h3>
		</div>

		<div class="text-file-wrapper ">
			<iframe style="<?= $iframeStyles; ?>" frameborder="0" src="<?php echo Router::url(array('controller' => 'awareness', 'action' => 'viewText', $program['AwarenessProgram']['id'])); ?>"></iframe>
		</div>

		<div class="clearfix"></div>

		<div class="next-button-wrapper" data-toggle="tooltip" data-placement="top" title="<?php echo __('Read through the content'); ?>">
			<?php
			echo $this->Form->create('AwarenessTrainingTextFile', array(
				'url' => array('controller' => 'awareness', 'action' => 'text'),
				'class' => 'form-horizontal row-border',
				'novalidate' => true
			));

			echo $this->Form->input('text_seen', array(
				'type' => 'hidden',
				'id' => 'text-seen',
				'value' => 0
			));

			echo $this->Form->submit(__('Understood'), array(
				'class' => 'btn btn-danger btn-lg',
				'div' => false,
				'disabled' => true
			));
			
			echo $this->Form->end();
			?>
		</div>
	</div>
</div>
<script>
jQuery(function($) {
	var blocked = 1;
	$(".text-file-wrapper iframe").on("load", function(e) {
		$(".text-file-wrapper iframe").contents().on("scroll", function() {
			if($(".text-file-wrapper iframe").contents().scrollTop() + $(".text-file-wrapper iframe").height() > $(".text-file-wrapper iframe").contents().height() - 5) {
				unblock();
			}
		}).trigger("scroll");
	});

	$(".next-button-wrapper").tooltip();

	function unblock() {
		if (blocked == 0) {
			return false;
		}

		setTimeout(function() {
			$(".next-button-wrapper input[type=submit]").removeAttr("disabled");
			$("#text-seen").val(1);
			$(".next-button-wrapper").tooltip('destroy');

			blocked = 0;
			$(".text-file-wrapper iframe").contents().off("scroll");
		}, 500);
	}
	
});
</script>