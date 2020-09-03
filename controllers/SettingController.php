<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Repository\Settings;
use app\models\Search\PaymentType;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SettingController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        if ($post = \Yii::$app->request->post()) {

            foreach ($post['Settings'] as $name => $setting) {
                $settingRepository = Settings::find()->where(['name' => $name])->one();
                if (!$settingRepository) {
                    $settingRepository = new Settings();
                }

                $settingRepository->name = $name;
                $settingRepository->value = $setting['value'];

                $settingRepository->validate()
                && $settingRepository->save();
            }
            \Yii::$app->session->addFlash('success', \Yii::t('settings', 'Settings was saved successfully'));
        }
        return $this->render('/setting/index', [
            'settings' => Settings::find()->orderBy(['id' => SORT_ASC])->all()
        ]);
    }
}
