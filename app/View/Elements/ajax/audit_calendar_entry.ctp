<?php
// this file is used in more places where form data convention differ, until its the same everywhere we differentiate it with this if statement
if (isset($useNewCalendarConvention) && $useNewCalendarConvention) {
	$selected = null;
	if ( isset( $day ) && isset( $month ) ) {
		$selected = array(
			'day' => $day,
			'month' => $month
		);
	}

	$after = '</div>';
	$after .= '<i class="icon icon-remove remove-parent" onClick="removeParent(this);" title="' . __( 'Remove' ) . '"></i>';
	?>
	<?php echo $this->Form->input( $model . '.' . $formKey, array(
		'type' => 'date',
		'dateFormat' => 'DM',
		'label' => false,
		'separator' => '</div><div class="select-wrapper">',
		'before' => '<div class="select-wrapper">',
		'after' => $after,
		'class' => 'form-control',
		'selected' => $selected
	) ); ?>

	<?php
	if ($this->Form->isFieldError($model . '_' . $formKey)) {
		echo $this->Form->error($model . '_' . $formKey);
	}
}

// this is the previous obsolete version, soon to be removed
else {
	$selected = null;
	if ( isset( $day ) && isset( $month ) ) {
		$selected = array(
			'day' => $day,
			'month' => $month
		);
	}

	if ( ! isset( $field ) ) {
		$field = 'audit_calendar';
	}

	$after = '</div>';
	//if ( $formKey != 0 ) {
	$after .= '<i class="icon icon-remove remove-parent" onClick="removeParent(this);" title="' . __( 'Remove' ) . '"></i>';
	//}
	?>
	<?php echo $this->Form->input( $model . '.' . $field . '.' . $formKey, array(
		'type' => 'date',
		'dateFormat' => 'DM',
		'label' => false,
		'separator' => '</div><div class="select-wrapper">',
		'before' => '<div class="select-wrapper">',
		'after' => $after,
		'class' => 'form-control',
		'selected' => $selected
	) ); ?>

	<?php
	if ($this->Form->isFieldError($field . '_' . $formKey)) {
		echo $this->Form->error($field . '_' . $formKey);
	}
}
?>