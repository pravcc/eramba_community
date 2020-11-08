<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetiteColorPicker extends AbstractMigration
{
    protected function convertColorsToNewFormat($direction)
    {
        $results = $this->fetchAll("SELECT `id`, `color` FROM `risk_appetite_thresholds` WHERE 1");
        $colors = [
            0 => '#555555', // Default
            1 => '#3968c6', // Primary
            2 => '#51a351', // Success
            3 => '#2f96b4', // Info
            4 => '#f89406', // Warning
            5 => '#bd362f' // Danger
        ];
        if ($direction == 'up') {
            //
        } elseif ($direction == 'down') {
            $colors = array_flip($colors);
        }
        foreach ($results as $result) {
            $newColor = isset($colors[$result['color']]) ? $colors[$result['color']] : reset($colors);
            $set = '';
            if (is_string($newColor)) {
                $set = "`color`='{$newColor}'";
            } else {
                $set = "`color`={$newColor}";
            }
            $this->execute("UPDATE `risk_appetite_thresholds` SET {$set} WHERE `id`={$result['id']}");
        }
    }

    public function up()
    {
        $this->table('risk_appetite_thresholds')
            ->changeColumn('color', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->update();

        $this->convertColorsToNewFormat('up');
    }

    public function down()
    {
        $this->convertColorsToNewFormat('down');

        $this->table('risk_appetite_thresholds')
            ->changeColumn('color', 'integer', [
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }
}

