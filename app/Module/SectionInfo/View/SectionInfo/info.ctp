<?php
App::uses('Hash', 'Utillity');
App::uses('SectionMapChart', 'SectionInfo.Lib/Reports/Chart');

$Model = ClassRegistry::init($model);
$config = $Model->getSectionInfoConfig();
?>
<div class="section-info">

    <div class="row section-info-desc">
        <div class="col-sm-12">
            <h3>
                <?= $Model->label(['singular' => true]) ?>
            </h3>
            <?php /*
            <?php if (!empty($config['description'])) : ?>
                <p class="text-grey">
                    <?= $config['description'] ?>
                </p>
            <?php endif; ?>
            */ ?>
        </div>
    </div>

    <?php if (!empty($config['map'])) : ?>
        <div class="section-info-map">
            <?php
            echo $this->Html->script("echarts.min");

            $chart = new SectionMapChart($this);

            $Model = ClassRegistry::init($model);

            $chart->map($Model->alias, $config['map'], $itemsCount);

            echo $chart->render();
            ?>
        </div>
    <?php endif; ?>

</div>