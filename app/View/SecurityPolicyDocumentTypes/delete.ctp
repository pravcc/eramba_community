<?php if ($isDeletable) : ?>
    <?= $this->element('section/delete'); ?>
<?php else : ?>
    <?php
    echo $this->Form->create($model, [
        'data-yjs-form' => $deleteFormName,
        'novalidate' => true
    ]);
    ?>
    <?=
    __(
        'There are policies using this tag and therefore we cant delete this item. Please use filters to select and update all policies using this tag to another tag.'
    );
    ?>
    <?php echo $this->Form->end(); ?>
<?php endif; ?>