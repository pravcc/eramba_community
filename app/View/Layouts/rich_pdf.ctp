<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
        echo $title_for_layout .(!empty($title_for_layout) ? ' | ' : ''). (defined('NAME_SERVICE') ? NAME_SERVICE : DEFAULT_NAME);
        ?>
    </title>

    <?php
        echo $this->Html->meta(
            'favicon.ico',
            '/favicon.png',
            array('type' => 'icon')
        );

        $cssFiles = [
            // GLOBAL
            "LimitlessTheme.icons/icomoon/styles",
            "LimitlessTheme.bootstrap",
            "LimitlessTheme.core",
            "LimitlessTheme.components",
            "LimitlessTheme.colors",
            "report-blocks-grid",
            "eramba"
        ];

        $jsFiles = [
            // Core
            // "LimitlessTheme.plugins/loaders/pace.min",
            "LimitlessTheme.core/libraries/jquery.min",
            "LimitlessTheme.core/libraries/bootstrap.min",
            "LimitlessTheme.plugins/loaders/blockui.min",
            // Theme
            //"LimitlessTheme.plugins/tables/datatables/datatables.min",
            "LimitlessTheme.plugins/forms/selects/select2.min",
            "LimitlessTheme.plugins/forms/styling/uniform.min",
            "LimitlessTheme.core/app",
            "LimitlessTheme.pages/datatables_basic",
            "LimitlessTheme.plugins/notifications/pnotify.min",

            // Temp velocity
            "LimitlessTheme.plugins/velocity/velocity.min",
            "LimitlessTheme.plugins/velocity/velocity.ui.min",
            "LimitlessTheme.pages/components_popups",

            "AutoComplete.auto-complete-new",
            "AutoComplete.auto-complete-associated",

            // "LimitlessTheme.core/app",
            // "LimitlessTheme.pages/animations_velocity_ui"
            // End temp velocity
        ];

        echo $this->Html->css($cssFiles, ['fullBase' => true]);
        echo $this->Html->script($jsFiles, ['fullBase' => true]);

        echo $this->Html->script("datatables-upgrade/datatables.min", ['fullBase' => true]);

        // echo $this->Html->script("https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js");
        // echo $this->Html->css("https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css");

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
    ?>
    <?= $this->Html->script("echarts.min", ['fullBase' => true]) ?>

    <!-- Load YoonityJS Framework -->
    <?= $this->Html->script("YoonityJS/YoonityJS-" . Configure::read('YoonityJS.version') . ".js?app_v=" . Configure::read('Eramba.version'), ['fullBase' => true]); ?>

    <link href="<?= $this->Html->url("/css/font/Roboto-font/Roboto-Regular.ttf", true); ?>" rel="stylesheet">
</head>
<body style="background-color: #fff">
    <div class="navbar navbar-inverse">
        <div class="navbar-header">
            <span id="logo" class="navbar-brand">
                <?php echo $this->Eramba->getLogo(DEFAULT_LOGO_WHITE_URL, true); ?>
            </span>
        </div>
    </div>
    <?= $this->fetch('content') ?>
</body>
</html>