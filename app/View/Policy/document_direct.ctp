<style type="text/css">
	.policy-header-wrapper {
		margin-bottom: 20px;
	}
	<?php if (!isset($documentNotAvailable)) : ?>
		.document-modal {
			border: 1px solid #d8dce2 !important;
		}
	<?php endif; ?>

	<?php if (isset($pdfDocument)) : ?>
		.col-sm-4 {
			float: left !important;
			width: 27% !important;
		}
	<?php endif; ?>
</style>

<div class="document-modal">
	<?php echo $this->element('../Policy/document'); ?>
</div>
