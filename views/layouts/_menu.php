<?php

use yii\widgets\Menu;

echo Menu::widget([
    'items' => [
        ['label' => 'Setup', 'url' => ['index']],
        ['label' => 'Sales', 'url' => ['sales-history']],
        ['label' => 'MS History', 'url' => ['ms-history']],
        ['label' => 'Commnd History', 'url' => ['command-history']],
    ]
]);

