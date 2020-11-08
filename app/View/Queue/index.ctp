<?php
App::uses('Queue', 'Model');

$tags = array(
    Queue::STATUS_PENDING => 'label-info',
    Queue::STATUS_SUCCESS => 'label-success',
    Queue::STATUS_FAILED => 'label-danger'
);
?>
<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="btn-toolbar">
                <?php echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'headerRight'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-content">
                <?php if (!empty($queue)) : ?>
                    <table class="table table-hover table-striped table-bordered table-highlight-head">
                        <thead>
                            <tr>
                                <th><?php echo $this->Paginator->sort('Queue.id', __('ID')); ?></th>
                                <th><?php echo $this->Paginator->sort('Queue.description', __('Description')); ?></th>
                                <th><?php echo $this->Paginator->sort('Queue.status', __('Status')); ?></th>
                                <th><?php echo $this->Paginator->sort('Queue.created', __('Created')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $queue as $item ) : ?>
                                <tr>
                                    <td><?php echo $item['Queue']['id']; ?></td>
                                    <td><?php echo $item['Queue']['description']; ?></td>
                                    <td>
                                        <span class="label <?php echo $tags[$item['Queue']['status']]; ?>"><?php echo Queue::statuses($item['Queue']['status']); ?></span>
                                    </td>
                                    <td><?php echo $item['Queue']['created']; ?></td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php echo $this->element( CORE_ELEMENT_PATH . 'pagination' ); ?>

                <?php else : ?>
                    <?php
                    echo $this->Html->div(
                        'alert alert-info',
                        '<i class="icon-exclamation-sign"></i> ' . __('No emails in the queue.')
                    );
                    ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>