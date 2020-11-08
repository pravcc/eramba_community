<?php
$setting = $setting['ComplianceAuditSetting'];
// debug($setting);

$choices = array();
if (!empty($setting['ComplianceAuditFeedbackProfile']['ComplianceAuditFeedback'])) {
    foreach ($setting['ComplianceAuditFeedbackProfile']['ComplianceAuditFeedback'] as $item) {
        $choices[$item['id']] = $item['name'];
    }
}
// $allowedToAnswer = (empty($setting['ComplianceAuditAuditeeFeedback']) || $setting['ComplianceAuditAuditeeFeedback'][0]['user_id'] == $logged['id']);

// every auditee is allowed to answer the question
$allowedToAnswer = true;
$class = (!$allowedToAnswer) ? 'disabled' : '';
?>

<?php if (empty($setting['ComplianceAuditAuditeeFeedback']) && !isset($this->request->data['ComplianceAuditAuditeeFeedback']) && !empty($showAddLink)) : ?>
    <?php echo $this->Html->link(__('Answer'), array(
    	'plugin' => true,
        'controller' => 'thirdPartyAudits',
        'action' => 'auditeeFeedback',
        $setting['id']
    ), array(
        'class' => 'auditee-feedback-add',
        'data-setting-id' => $setting['id']
    )); ?>

<?php else : ?>

    <?php 
    // debug($setting);
    $selected = array();
    if (!isset($this->request->data['ComplianceAuditAuditeeFeedback']['choice_id'])) {
        foreach ($setting['ComplianceAuditAuditeeFeedback'] as $item) {
            $selected[] = $item['compliance_audit_feedback_id'];
        }
    }
    else {
        $selected = $this->request->data['ComplianceAuditAuditeeFeedback']['choice_id'];
    }
    ?>
    
    <?php echo $this->Form->create('ComplianceAuditAuditeeFeedback'); ?>

    <?php echo $this->Form->input('choice_id', array(
        'options' => $choices,
        'label' => false,
        'empty' => __('None selected'),
        // 'multiple' => true,
        'class' => 'multiselect-feedback hidden ' . $class,
        'disabled' => !$allowedToAnswer,
        'value' => $selected,
        'id' => 'auditee-feedback-input-' . $setting['id'],
    )); ?>

    <?php echo $this->Form->end(); ?>

    <?php 
    // $this->request->data['ComplianceAuditAuditeeFeedback']['choice_id'] = null;
    ?>

    <?php 
    if (!empty($setting['ComplianceAuditAuditeeFeedback']) && empty($success)) {
        echo $this->Form->input('auditee_feedback_' . $setting['id'], array(
            'type' => 'hidden',
            'class' => 'auditee-feedback-user',
            'data-name' => $setting['ComplianceAuditAuditeeFeedback'][0]['User']['full_name'],
            'value' => $setting['ComplianceAuditAuditeeFeedback'][0]['User']['id'],
        ));
    }
    elseif (!empty($success)) {
        echo $this->Form->input('auditee_feedback_' . $setting['id'], array(
            'type' => 'hidden',
            'class' => 'auditee-feedback-user',
            'data-name' => $logged['full_name'],
            'value' => $logged['id'],
        ));
    }
    ?>
    
<?php endif; ?>

<script type="text/javascript">
$(function() {
    function auditeeFeedback() {
        App.blockUI($("#auditee-feedback-<?php echo $setting['id']; ?>"));
        $.ajax({
            url: "<?php echo Router::url(array(
            	'plugin' => true,
		        'controller' => 'thirdPartyAudits',
		        'action' => 'auditeeFeedback',
		        $setting['id']
		    )) ?>",
            type: 'POST',
            data: $("#auditee-feedback-<?php echo $setting['id']; ?> form").serialize()
        }).done(function(response) {
            $("#auditee-feedback-<?php echo $setting['id']; ?>").html(response);
            App.unblockUI($("#auditee-feedback-<?php echo $setting['id']; ?>"));
        });
    }
    
    if (typeof $("#auditee-feedback-input-<?php echo $setting['id']; ?>").data('multiselect') == "undefined") {
        $("#auditee-feedback-input-<?php echo $setting['id']; ?>").multiselect({
            numberDisplayed: 10,
            allSelectedText: false, 
            onChange: function() {
                auditeeFeedback();
            }
        });
    }
});

<?php if (!empty($error)) : ?>
    // Temp before migration
    // noty({
    //     text: '<strong><?php echo h($error); ?></strong>',
    //     type: 'error',
    //     timeout: 4000
    // });
<?php endif; ?>

<?php if (!empty($success)) : ?>
    $("#content").trigger("Eramba.ComplianceAudits.auditeeFeedback");
    // Temp before migration
    // noty({
    //     text: '<strong><?php echo h($success); ?></strong>',
    //     type: 'success',
    //     timeout: 4000
    // });
<?php endif; ?>
</script>