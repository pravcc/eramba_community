<?php if (strtolower($program['AwarenessProgram']['text_file_extension']) == 'txt') : ?>
    <style type="text/css">
        * {
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
    </style>
    <?php echo nl2br($content); ?>
<?php else: ?>
    <?php echo $content; ?>
<?php endif; ?>