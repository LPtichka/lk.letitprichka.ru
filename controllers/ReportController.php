<?php
namespace app\controllers;

class ReportController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('/report/index', []);
    }
}
