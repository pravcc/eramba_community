<?= $this->element($layout_toolbarPath); ?>

<?php
if (Configure::read('debug')) {
	echo $this->element('LimitlessTheme.toolbar/debug');
}
?>