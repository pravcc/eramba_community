<?php
echo $this->Html->css(['policy', 'policy-document']);
echo $this->Html->script(['policy-document', 'plugins/slimscroll/jquery.slimscroll.min']);
		
if (isset($documentNotAvailable) && $documentNotAvailable) :
	echo $this->Html->div(
		'alert alert-danger fade in',
		'<i class="icon-exclamation-sign"></i> ' . __('You don\'t have a permission to view this document.'), array(
			'style' => 'margin-bottom: 0;'
		)
	);

	return true;
endif;
?>

<div class="modal-box">
	<h3><?php echo __('Document name'); ?></h3>
	<div class="document-title"><?php echo $document['SecurityPolicy']['index']; ?></div>
</div>
<div class="modal-box">
	<h3><?php echo __('Description'); ?></h3>
	<div>
		<?php
		if (!empty($document['SecurityPolicy']['short_description'])) {
			echo $document['SecurityPolicy']['short_description'];
		}
		else {
			echo '-';
		}
		?>
	</div>
</div>
<div class="modal-bg">
	<div class="row">
		<div class="col-sm-4">
			<div class="modal-padding">
				<h3><?php echo __('Owner'); ?></h3>
				<div><?= $this->UserField->showUserFieldRecords($document['Owner']); ?></div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="modal-padding">
				<h3><?php echo __('Version'); ?></h3>
				<div><?php echo $document['SecurityPolicy']['version']; ?></div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="modal-padding">
				<h3><?php echo __('Reviewer'); ?></h3>
				<div>
					<?= $this->UserField->showUserFieldRecords($document['Collaborator']); ?>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="modal-padding">
				<h3><?php echo __('Published Date'); ?></h3>
				<div><?php echo $document['SecurityPolicy']['published_date']; ?></div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="modal-padding">
				<h3><?php echo __('Last Review'); ?></h3>
				<div>
					<?php
					if (!empty($document['CurrentReview'])) {
						echo $document['CurrentReview']['SecurityPolicyReview']['actual_date'];
					} else {
						echo '-';
					}
					?>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="modal-padding">
				<h3><?php echo $this->Reviews->getReviewDate($document)['label']; ?></h3>
				<div>
					<?php
					echo $this->Reviews->getReviewDate($document)['date'];
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$missingDocumentLabel = $this->Html->tag('em', __('No Documents found.'));
?>
<div class="document-content">
	<?php if ($document['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_CONTENT) : ?>
		<?php if (empty($document['SecurityPolicy']['description'])) : ?>
			<?php
			echo $missingDocumentLabel;
			?>
		<?php else : ?>
			<div id="document-content-inner">
				<?php echo $document['SecurityPolicy']['description']; ?>
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
	<?php elseif ($document['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_ATTACHMENTS) : ?>
		<?php
		$lastCompletedReview = $this->Reviews->getLastCompletedReview($document);
		if (empty($lastCompletedReview['Attachment'])) :
			echo $this->Html->tag('em', __('Make sure you have included attachments to the latest review for this Policy (Manage/Reviews).'));
		else : ?>
			<h3 class="custom-title"><?php echo __('Documents'); ?></h3>

			<div id="document-content-inner">
				<ul>
					<?php foreach ($lastCompletedReview['Attachment'] as $attachment) : ?>
						<li>
							<?php
							echo $this->Html->link(basename($attachment['name']), array(
								'action' => 'downloadAttachment',
								$attachment['id']
							), array(
								'class' => 'bs-tooltip',
								'escape' => false,
								'title' => __('Download')
							));
							?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
				<script type="text/javascript">
			$(function(){
				$('#document-content-inner').slimScroll({
					height: '150px',
					// alwaysVisible: true,
					railVisible: true,
					railColor : '#e4e8e9',
					color: '#bbbfc0',
					size: '10px',
					opacity : 1,
					railOpacity : 1
				});
			});
			</script>
		<?php endif; ?>
	<?php elseif ($document['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_URL) : ?>
		<?php
		$url = $document['SecurityPolicy']['url'];
		if (!empty($url)) {
			$link = $this->Html->link(__('View'), $url, array(
				'target' => '_blank',
				'escape' => false
			));

			printf('%s (%s)', $link, $url);
		}
		else {
			echo $missingDocumentLabel;
		}
		?>
	<?php endif; ?>
</div>
<pagebreak />
<div class="modal-box" id="version" style="position:relative;">
	<div>
		<?=
		$this->element('securityPolicies/document_reviews', [
			'document' => $document
		]);
		?>
	</div>
</div>
<?php
$relatedDocuments = Hash::combine($document['RelatedDocuments'], '{n}.id', '{n}', '{n}.SecurityPolicyDocumentType.name');
$relatedDocuments = array_chunk($relatedDocuments, 3, true);
?>
<?php if (!empty($relatedDocuments)) : ?>
	<div class="modal-bg">
		<?php foreach ($relatedDocuments as $relatedRow) : ?>
			<div class="row">
				<?php foreach ($relatedRow as $relatedType => $related) : ?>
					<div class="col-sm-4">
						<div class="modal-padding">
							<h3><?php echo __('Related %s', $relatedType); ?></h3>
							<div>
								<ul class="list-unstyled">
									<?php foreach ($related as $doc) : ?>
										<li>
											<?php
											if (isset($externalDocument) && $externalDocument) {
												echo $doc['index'];
											}
											else {
												echo $this->Policy->getDocumentLink($doc['index'], $doc['id']);
											}
											?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if (AppModule::loaded('CustomFields')) : ?>
	<div class="modal-bg">
		<?php
		echo $this->element('CustomFields.' . CUSTOM_FIELDS_DISPLAY_ELEMENT_PATH . 'accordion', array(
			'item' => $document, // single database item in a variable
			'layout' => 'pdf_policy'
		));
		?>
	</div>
<?php endif; ?>

<?php if (empty($this->request->query['from_app']) && empty($externalDocument)) : ?>
	<div class="clearfix">
		<?php
		$pdfUrl = ['admin' => false, 'plugin' => false, 'controller' => 'policy', 'action' => 'documentPdf', $document['SecurityPolicy']['id']];

		echo $this->Html->link(__('Download PDF'), $pdfUrl, [
			'class' => 'btn btn-primary pull-right',
			'style' => 'margin:10px;'
		]);
		?>
	</div>
<?php endif; ?>