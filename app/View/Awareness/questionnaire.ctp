<div class="questionnaire">
	<?php
	echo $this->Form->create('Awareness', array(
		'url' => array('controller' => 'awareness', 'action' => 'questionnaire'),
		'novalidate' => true
	));
	?>

	<?php foreach ($questionnaire as $key => $question) : ?>
	<div class="questionnaire-question">
		<h3 class="question-title"><?php echo $question['question']; ?></h3>
		<h4 class="question-subtitle"><?php echo $question['description']; ?></h4>

		<?php
		echo $this->Form->input('Awareness.' . $key . '.answer', array(
			'type' => 'hidden',
			'value' => '',
			'label' => false,
			'div' => false,
			'legend' => false,
		));
		?>
		<?php foreach ($question['answers'] as $key2 => $answer) : ?>
		<label class="radio questionnaire-radio">
			<?php
			echo $this->Form->input('Awareness.' . $key . '.answer', array(
				'type' => 'radio',
				'options' => array(
					$key2 => $answer
				),
				'label' => false,
				'div' => false,
				'legend' => false,
				'hiddenField' => false
			));
			?>
		</label>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>

	<?php
	echo $this->Form->submit(__('Submit'), array(
		'class' => 'btn btn-danger btn-lg',
		'div' => 'text-center'
	));
	?>

	<?php echo $this->Form->end(); ?>
</div>

<script type="text/javascript">
jQuery(function($) {
	$(".questionnaire-radio input[type=radio]").on("change", function(e) {
		var question = $(this).parents(".questionnaire-question");
		var checked = question.find("input[type=radio]:checked");
		console.log(checked);

		question.find(".questionnaire-radio.radio-checked").removeClass("radio-checked");
		checked.parent(".questionnaire-radio").addClass("radio-checked");
	});

	$(".questionnaire-radio input[type=radio]:checked").trigger("change")
});
</script>