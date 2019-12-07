<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Search\Exception;
use yii\db\IntegrityException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ExceptionController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Exception();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/exception/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'title'        => \Yii::t('exception', 'Exceptions'),
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $exception = new \app\models\Repository\Exception();

        if (\Yii::$app->request->post()) {
            $this->log('exception-create', []);
            $exception->load(\Yii::$app->request->post());
            $isValidate = $exception->validate();
            if ($isValidate && $exception->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('exception', 'Exception was saved successfully'));
                $this->log('exception-create-success', ['name' => $exception->name]);
                return $this->redirect(['exception/index']);
            } else {
                $this->log('exception-create-fail', ['name' => $exception->name, 'errors' => json_encode($exception->getFirstErrors())]);
            }
        }
        return $this->render('/exception/create', [
            'model' => $exception,
            'title' => \Yii::t('exception', 'Exception create'),
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $exception = \app\models\Repository\Exception::findOne($id);
        if (!$exception) {
            throw new NotFoundHttpException('Исключение не найдено');
        }

        if (\Yii::$app->request->post()) {
            $this->log('exception-edit', ['name' => $exception->name]);
            $exception->load(\Yii::$app->request->post());
            $isValidate = $exception->validate();
            if ($isValidate && $exception->save()) {
                $this->log('exception-edit-success', ['name' => $exception->name]);
                \Yii::$app->session->addFlash('success', \Yii::t('exception', 'Exception was saved successfully'));
                return $this->redirect(['exception/index']);
            } else {
                $this->log('exception-edit-fail', ['name' => $exception->name]);
            }
        }
        return $this->render('/exception/create', [
            'model' => $exception,
            'title' => \Yii::t('exception', 'Exception update'),
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $exceptionsIDs = \Yii::$app->request->post('selection');

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('exception-delete', $exceptionsIDs);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($exceptionsIDs as $id) {
            try {
                $isDelete = \app\models\Repository\Exception::deleteAll(['id' => $id]);
            } catch (IntegrityException $e) {
                $transaction->rollBack();
                $this->log('exception-delete-fail', [$e->getMessage()]);
                return [
                    'status' => false,
                    'title'  => \Yii::t('order', 'Error'),
                    'description'  => \Yii::t('order', 'Exception was not deleted because has links with products'),
                ];
            }

            if (!$isDelete) {
                $transaction->rollBack();
                $this->log('exception-delete-fail', $exceptionsIDs);
                return [
                    'status' => false,
                    'title'  => \Yii::t('order', 'Exception was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('exception-delete-success', $exceptionsIDs);
        return [
            'status' => true,
            'title'  => \Yii::t('exception', 'Exception was successful deleted')
        ];
    }

    /**
     * Испорт исключений из Excel
     *
     * @throws \PHPExcel_Exception
     * @throws \Exception
     */
    public function actionImport()
    {
        $inputFile = $_FILES['xml'];
        $excel     = new Excel();
        $excel->load($inputFile);
        if (!$excel->validate()) {
            throw new \Exception(\Yii::t('file', 'File is not suitable'));
        }

        $parserData = $excel->parse();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction                 = \Yii::$app->db->beginTransaction();
        foreach ($parserData as $payment) {
            $exception       = new \app\models\Repository\Exception();
            $exception->name = $payment[0] ?? null;

            if (!($exception->validate() && $exception->save())) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('danger', \Yii::t('exception', 'Exception import was failed'));
                return [
                    'success' => false,
                ];
            }
        }

        $transaction->commit();
        \Yii::$app->session->addFlash('success', \Yii::t('exception', 'Exception was imported successfully'));

        return [
            'success' => true,
        ];
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionExport()
    {
        $exceptions = (new Exception())->export(\Yii::$app->request->post());

        $excel = new Excel();
        $excel->loadFromTemplate('files/templates/base.xlsx');
        $excel->prepare($exceptions, Excel::MODEL_EXCEPTION);
        $excel->save('exception.xlsx', 'temp');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'url' => $excel->getUrl(),
        ];
    }
}
