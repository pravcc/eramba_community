<?php if (empty($pdf)) : ?>
	<div style="position:absolute; left:0; top:0; width:100%; height:100%;">
<?php endif; ?>
	<div class="document-modal">
		<?php echo $this->element('../SecurityPolicyReviews/review'); ?>
	</div>
<?php if (empty($pdf)) : ?>
	</div>
<?php endif; ?>