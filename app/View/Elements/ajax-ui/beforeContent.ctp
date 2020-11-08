<?php
echo $this->Ux->renderFlash();
?>

<?php if (isset($showHeader)) : ?>
	<div class="modal-header">
		<?php
		echo $this->Ajax->cancelBtn(null, null, array('title' => '×', 'class' => 'close'));
		?>
		<h4 class="modal-title"><?php echo $title_for_layout; ?></h4>
	</div>
<?php endif; ?>

<?php
$customJs = array();
// $customJs[] = "if (Eramba.Ajax.UI.modal != null) {Eramba.Ajax.UI.modal.setSize('modal-responsive');}";
// if (isset($ajaxSuccess) && $ajaxSuccess) {
// 	$customJs[] = "Eramba.Ajax.UI.success = true;";
// }

// if (isset($pageLimit) && $pageLimit) {
// 	$customJs[] = "Eramba.Ajax.UI.setPageLimit(" . $pageLimit . ");";
// }

if (!empty($customJs)) {
	echo $this->Html->scriptBlock(implode(' ', $customJs));
}
?>