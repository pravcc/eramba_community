<?php if (!empty($tags)) : ?>
<div class="tags">
	<h4 class="tags-title"><?php echo __('Tags'); ?></h4>

	<div>
		<?php foreach ($tags as $tag) : ?>
			<?php echo $this->Html->link( $tag, array(
				'controller' => 'policy',
				'action' => 'index',
				'?' => array(
					'policy_search' => $tag
				)
			), array(
				'class' => 'label label-default tag',
				'escape' => false
			) ); ?>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>
<?php
$documents = Hash::combine($documents, '{n}.SecurityPolicy.id', '{n}', '{n}.SecurityPolicyDocumentType.name');
?>
<?php foreach ($documents as $type => $docs) : ?>
	<div class="documents-box">
		<div class="documents-header policies">
			<h3><?php echo $type; ?></h3>
		</div>
		<div class="documents-content">
			<ul class="document-list list-unstyled">
				<?php foreach ($docs as $doc) : ?>
					<li>
						<?php
						echo $this->Policy->getDocumentLink(
							$doc['SecurityPolicy']['index'],
							$doc['SecurityPolicy']['id'],
							false,
							$doc['SecurityPolicy']['short_description']
						);
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endforeach; ?>

<script type="text/javascript">
jQuery(function($) {
	$('.bs-popover').popover();

	<?php if (isset($policyId) && !empty($policyId)) : ?>
		$("a[data-document-link][data-document-id=<?php echo $policyId; ?>]").trigger("click");
	<?php endif; ?>
});
</script>