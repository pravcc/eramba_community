<?php
echo $this->Html->css(['policy', 'policy-document']);
echo $this->Html->script(['policy-document', 'plugins/slimscroll/jquery.slimscroll.min']);
?>

<div class="modal-box">
	<h3><?php echo __('Document name'); ?></h3>
	<div class="document-title"><?php echo $reviewTitle; ?></div>
</div>

<?php
$missingDocumentLabel = $this->Html->tag('em', __('No Documents found.'));
?>
<div class="document-content">
	<?php if (empty($review['SecurityPolicyReview']['policy_description'])) : ?>
		<?php
		echo $missingDocumentLabel;
		?>
	<?php else : ?>
		<div id="document-content-inner">
			<?php echo $review['SecurityPolicyReview']['policy_description']; ?>
		</div>

		<script type="text/javascript">
		$(function(){
			$('#document-content-inner').slimScroll({
				height: '400px',
				// alwaysVisible: true,
				railVisible: true,
				railColor : '#e4e8e9',
				color: '#bbbfc0',
				size: '10px',
				opacity : 1,
				railOpacity : 1
			});


		});
		$("#document-content-inner table").addClass("table table-striped table-bordered");
		</script>
	<?php endif; ?>
</div>

<div class="modal-box" id="version" style="position:relative;">
	<div>
		<?=
		$this->element('securityPolicies/document_reviews', [
			'document' => $review
		]);
		?>
	</div>
</div>

