<?php
namespace app\events;

use app\models\Repository\Menu;
use yii\base\Event;

class LinkOrderDishes extends Event
{
    const EVENT_MENU_CREATED = 'menu_created';

    /** @var int */
    private $menuID;

    /**
     * @param int $menuID
     */
    public function setMenuID(int $menuID): void
    {
        $this->menuID = $menuID;
    }

    /**
     * @return bool
     */
    public function linkOrderDishes()
    {
        // TODO доделать привязку блюд заказами
        $menu = Menu::findOne($this->menuID);

        return true;
    }

    /**
     * @return $this
     */
    public function prepareEvent()
    {
        \Yii::$app->on(LinkOrderDishes::EVENT_MENU_CREATED, function (LinkOrderDishes $event) {
            $event->linkOrderDishes();
        });

        return $this;
    }
}
