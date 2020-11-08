<div class="questionnaire">
	<?php if (!empty($questionnaire)) : ?>
		<?php foreach ( $questionnaire as $key => $question ) : ?>
		<div class="questionnaire-question">
			<h3 class="question-title"><?php echo $question['question']; ?></h3>
			<h4 class="question-subtitle"><?php echo $question['description']; ?></h4>

			<?php foreach ( $question['answers'] as $key2 => $answer ) : ?>
			<?php
			$checked = false;
			if ( $key2 == $userAnswers[ $key ] - 1 ) {
				$checked = true;
			}

			$append = '';
			$labelClass = 'radio questionnaire-radio disabled';
			if ($checked) {
				$labelClass .= ' radio-checked';
			}
			if (($key2 + 1) == $question['correctAnswer']) {
				$labelClass .= ' correct-answer';
				$append = '<span class="answer-append"><i class="icon icon-ok-sign"></i> ' . __('Correct answer') . '</span>';
			}
			elseif (($userAnswers[$key]) == $key2 + 1) {
				$labelClass .= ' wrong-answer';
				$append = '<span class="answer-append"><i class="icon icon-warning-sign"></i> ' . __('Wrong answer') . '</span>';
			}
			?>
			<label class="<?php echo $labelClass; ?>">
				<input type="radio" disabled="disabled" <?php echo $checked ? 'checked="checked"' : ''; ?> />

				<?php echo $answer . $append; ?>
			</label>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="text-center">
		<h2 class="awareness-title"><?php echo $training['AwarenessProgram']['thank_you_text']; ?></h2>
		<h3 class="awareness-subtitle"><?php echo $training['AwarenessProgram']['thank_you_sub_text']; ?></h3>

		<?php
		//
		// If url doesn't contain http at the beginning of the string, we add it manually so we make sure the Helper::url() won't handle it like internal url (won't add controller to url)
		$redirectUrl = $training['AwarenessProgram']['redirect'];
		$redirectUrl = strpos($redirectUrl, 'http') !== 0 ? 'http://' . $redirectUrl : $redirectUrl;
		//
		
		echo $this->Html->link(__('Complete'), $redirectUrl, array(
			'class' => 'btn btn-danger btn-lg'
		));
		?>
	<!-- 	<a href="<?php echo $training['AwarenessProgram']['redirect']; ?>" class="btn btn-danger btn-lg"><?php echo __('Complete'); ?></a> -->
	</div>
</div>
