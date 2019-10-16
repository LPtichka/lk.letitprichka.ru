<?php
namespace app\controllers;

use app\models\Forms\ForgotPassword;
use app\models\Forms\LoginForm;
use app\models\Forms\ResetPasswordForm;
use app\models\Forms\SignupForm;
use InvalidArgumentException;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class MainController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class'        => AccessControl::class,
                'only'         => ['logout', 'login', 'signup', 'forgot-password', 'reset-password'],
                'denyCallback' => function () {
                    \Yii::$app->getResponse()->redirect(\Yii::$app->getHomeUrl())->send();
                },
                'rules'        => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                    [
                        'actions' => ['login', 'signup', 'forgot-password', 'reset-password'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
//        if (\Yii::$app->user->isGuest) {
//            $this->layout = 'auth';
//        }
        return [];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Регистрация пользователя
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())
            && ($user = $model->signUp())
            && Yii::$app->getUser()->login($user)
        ) {
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionForgotPassword()
    {
        $model = new ForgotPassword();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }
        return $this->render('forgotPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
