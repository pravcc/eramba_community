<?php
/**
 * 
 * Template elements:
 *  - header
 *  - paragraphs
 * 
 */

$tooltip = $this->Tooltips->tooltip();

// Header of the tooltip modal
$tooltip->header()->heading($header);

// Body of the tooltip modal
$body = $tooltip->body();

// First row
$bodyRow1 = $body->row();

//
// Paragraphs
$bodyRow1Column1 = $bodyRow1->column();
foreach ($paragraphs as $paragraph) {
	$bodyRow1Column1->heading($paragraph['heading']);
	$bodyRow1Column1->text($paragraph['text']);
}
//

// Footer of the tooltip modal
$footer = $tooltip->footer();

// Buttons
$footer->setButtons($buttons);

// Render tooltip
echo $this->Tooltips->tooltip()->render();

//
// If there won't be any buttons in tooltip template, "Got it!" button will show automatically
//
?>