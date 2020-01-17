<?php

/* @var $order \app\models\Repository\Order */

?>
<div>
    <?php foreach ($dates as $key => $date) {
        echo $this->render('_day_block', [
            'i'            => $key,
            'date'         => $date,
            'menu'         => $menu,
            'breakfasts'   => $breakfasts,
            'lunches'      => $lunches,
            'firstDishesSupper'  => $firstDishesSupper,
            'secondDishesSupper' => $secondDishesSupper,
            'firstDishesDinner'  => $firstDishesDinner,
            'secondDishesDinner' => $secondDishesDinner,
        ]);
    }; ?>
</div>