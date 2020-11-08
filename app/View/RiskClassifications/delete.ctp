<?php if (!$isUsed) : ?>
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
        'Classification <strong>%s</strong> - <strong>%s?</strong> is used by %d Risk(s) and cannot be deleted until you reclassify associated Risks.',
        ClassRegistry::init($model)->label(['singular' => true]),
        $recordTitle,
        $isUsed
    );
    ?>
    <?php echo $this->Form->end(); ?>
<?php endif; ?>