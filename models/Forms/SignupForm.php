<?php
namespace app\models\Forms;

use app\models\User;
use yii\base\Exception;
use yii\base\Model;
use yii\rbac\DbManager;
use yii\rbac\Role;

class SignupForm extends Model
{
    public $email;
    public $fio;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'email', 'password'], 'required'],
            ['fio', 'string', 'max' => 512],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => \Yii::t('app', 'This email address has already been taken')],
            ['password', 'string', 'min' => 8],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email'    => \Yii::t('app', 'email'),
            'password' => \Yii::t('app', 'password'),
            'fio'      => \Yii::t('app', 'fio'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     * @throws \yii\base\Exception
     */
    public function signUp()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();

        $user->scenario = User::SCENARIO_CREATE;
        $user->email    = $this->email;
        $user->fio      = $this->fio;

        $user->setPassword($this->password);
        if ($user->validate()) {
            $user->save();
        } else {
            throw new Exception(500, 'Пользователь не может быть сохранен');
        }

        /** @var DbManager $authManager */
        $authManager = \Yii::$app->authManager;

        /** @var Role $role */
        $role = $authManager->getRole($user->getRole());

        $authManager->assign($role, $user->id);
        $authManager->invalidateCache();

        return !$user->hasErrors() ? $user : null;
    }
}