<div class="modal-content modal-content-custom">
	<div class="modal-header bg-danger">
		<h5 class="modal-title"><?= $warningHeading; ?></h5>
		<button type="button" class="close" 
			data-yjs-request="app/closeModal" 
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-event-on="click">&times;</button>
	</div>
	<div class="modal-body">
		<?= $warningMessage; ?>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-danger" 
			data-yjs-request="app/closeModal" 
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-event-on="click"><?= $warningButton; ?></button>
	</div>
</div>