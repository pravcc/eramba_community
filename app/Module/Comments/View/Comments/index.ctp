<?php if (!empty($data)) : ?>
	<?= $this->Comments->renderList($data) ?>
<?php else : ?>
	<?= $this->Alerts->info(__('No comments for this record.')) ?>
<?php endif; ?>