<?php
namespace app\controllers;

use app\models\Repository\Dish;
use app\models\search\Menu;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class MenuController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Menu();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/menu/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $menu         = new \app\models\Repository\Menu();
        $disabledDays = $menu->getDisabledDays();

        if (\Yii::$app->request->post()) {
            $this->log('menu-create', []);
            $menu = $menu->build(\Yii::$app->request->post());

            if ($menu->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('menu', 'Menu was saved successfully'));
                $this->log('menu-create-success', [
                    'start' => $menu->menu_start_date,
                    'end'   => $menu->menu_end_date,
                    'id'    => $menu->id,
                ]);
                return $this->redirect(['menu/index']);
            } else {
                $this->log('menu-create-fail', [
                    'start'  => $menu->menu_start_date,
                    'end'    => $menu->menu_end_date,
                    'errors' => json_encode($menu->getFirstErrors()),
                ]);
            }
        }
        return $this->render('/menu/create', [
            'model'        => $menu,
            'disabledDays' => $disabledDays,
            'title'        => \Yii::t('product', 'Menu create'),
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $menu         = \app\models\Repository\Menu::findOne($id);
        $disabledDays = $menu->getDisabledDays();

        if (!$menu) {
            throw new NotFoundHttpException('Меню не найден');
        }

        if (\Yii::$app->request->post()) {
            $this->log('menu-create', []);
            $menu->build(\Yii::$app->request->post());

            if ($menu->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('menu', 'Menu was saved successfully'));
                $this->log('menu-create-success', [
                    'start' => $menu->menu_start_date,
                    'end'   => $menu->menu_end_date,
                    'id'    => $menu->id,
                ]);
                return $this->redirect(['menu/index']);
            } else {
                $this->log('menu-create-fail', [
                    'start'  => $menu->menu_start_date,
                    'end'    => $menu->menu_end_date,
                    'errors' => json_encode($menu->getFirstErrors()),
                ]);
            }
        }

        return $this->render('/menu/create', [
            'model'        => $menu,
            'disabledDays' => $disabledDays,
            'title'        => \Yii::t('menu', 'Menu update'),
        ]);
    }

    /**
     * @return string
     */
    public function actionGetDayBlocks()
    {
        $startDay = \Yii::$app->request->post('menuStartDate');
        $endDay   = \Yii::$app->request->post('menuEndDate');
        $menuID   = \Yii::$app->request->post('menuID');

        $dates          = [];
        $startTimestamp = strtotime($startDay);
        $i              = 0;
        do {
            $date = date('Y-m-d', $startTimestamp + $i * 86400);;
            $dates[] = $date;
            $i++;
        } while ($date < $endDay);

        if ($menuID) {
            $menu = \app\models\Repository\Menu::findOne($menuID);
        } else {
            $menu = new \app\models\Repository\Menu();
        }

        return $this->renderAjax('/menu/_day_menu', [
            'dates'              => $dates,
            'menu'               => $menu,
            'breakfasts'         => ArrayHelper::map(Dish::find()->where(['is_breakfast' => true])->asArray()->all(), 'id', 'name'),
            'lunches'            => ArrayHelper::map(Dish::find()->where(['is_lunch' => true])->asArray()->all(), 'id', 'name'),
            'suppers'            => ArrayHelper::map(Dish::find()->where(['is_supper' => true])->asArray()->all(), 'id', 'name'),
            'firstDishesDinner'  => ArrayHelper::map(Dish::find()->where(['type' => Dish::TYPE_FIRST, 'is_dinner' => true])->asArray()->all(), 'id', 'name'),
            'secondDishesDinner' => ArrayHelper::map(Dish::find()->where(['type' => Dish::TYPE_SECOND, 'is_dinner' => true])->asArray()->all(), 'id', 'name'),
        ]);
    }
}
