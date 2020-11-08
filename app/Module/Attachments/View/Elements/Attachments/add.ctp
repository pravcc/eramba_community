<?php
App::uses('Attachment', 'Attachments.Model');

if (isset($hash)) {
    $url = Router::url([
        'plugin' => 'attachments', 'controller' => 'attachments', 'action' => 'addTmp', $hash
    ]);
}
else {
    $url = Router::url([
        'plugin' => 'attachments', 'controller' => 'attachments', 'action' => 'add', $model, $foreignKey
    ]);
}

echo $this->Html->script('LimitlessTheme.plugins/uploaders/dropzone.min');
?>
<div class="dropzone" id="dropzone-attachments">
</div>

<div class="dropzone-previews attachments-list" id="previews-container">
    <?= $this->Attachments->renderList($data) ?>
</div>

<?php if (empty($data)) : ?>
    <!-- <div class="attachments-empty-message"> -->
        <?= '';//$this->Alerts->info(__('No attachments for this record.'), ['class' => 'empty-message']) ?>
    <!-- </div> -->
<?php endif; ?>

<script type="text/javascript">
$(function() {
    // Defaults
    Dropzone.autoDiscover = false;

	var attachmentsDropzone = new Dropzone('#dropzone-attachments', {
        url: "<?= $url ?>",
        paramName: 'file',
        dictDefaultMessage: 'Drop files to upload <span>or CLICK</span>',
        maxFilesize: <?= Attachment::getMaxFileSize('mb') ?>, // MB
        previewsContainer: '#previews-container',
        addedfile: function(file)
        {
            var elem = $(this.options.previewTemplate);

            elem.find('[data-dz-name]').html(file.name);
            elem.find('.dz-image').html('<i class="icon-file-upload"></i>');
            elem.find('.dz-size').remove();

            file.previewElement = Dropzone.createElement(elem.prop('outerHTML'));

            $(this.previewsContainer).prepend(file.previewElement);

            //remove empty message
            $('.attachments-empty-message').remove();
        },
        complete: function(file)
        {
            $(file.previewElement).find('.dz-image').html('<i class="icon-cross text-danger"></i>');

            if (!file.xhr || !file.xhr.response) {
                return false;
            }

            var response = jQuery.parseJSON(file.xhr.response);

            if (response && response.element) {
                $(file.previewElement).remove();

                var elem = $.parseHTML(response.element, true);

                $(this.previewsContainer).prepend(elem);

                new YoonityJS.InitTemplate({template: elem});
            }
        },
        error: function(file, message)
        {
            var popoverError = '<?= $this->Popovers->top('<u>' . __('Show Details') . '</u>', '_error_', __('Error Message'), ['class' => 'text-underline', 'pointer' => true]) ?>';
            popoverError = popoverError.replace('_error_', message);

            var popoverName = '<?= $this->Popovers->top('_name_', '_name_', null, ['pointer' => true]) ?>';
            popoverName = popoverName.replace(/_name_/g, file.name);

            var elemId = 'dz-file-preview-' + Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);

            var elemHtml = 
                '<div id="' + elemId + '" class="dz-preview dz-file-preview" data-yjs-use-loader="false" data-yjs-request="crud/initExternals/elem::#' + elemId + '" data-yjs-event-on="init">'
                    + '<div class="dz-image">'
                        + '<i class="icon-cross3 text-danger"></i>'
                    + '</div>'
                    + '<div class="dz-details">'
                        + '<div class="dz-filename text-danger">'
                            + popoverName
                        + '</div>'
                        + '<div class="dz-size text-danger">'
                            + '<strong>FAILED</strong>: ' + popoverError
                        + '</div>' 
                    + '</div>'
                + '</div>';

            var elem = $.parseHTML(elemHtml, true);

            $(file.previewElement).remove();

            $(this.previewsContainer).prepend(elem);

            new YoonityJS.InitTemplate({template: elem});

            //notification
            new PNotify({
                title: '<?= __('Upload has failed'); ?>',
                addclass: 'bg-danger',
                text: message,
                timeout: 4000
            });
        },
    });
});
</script>