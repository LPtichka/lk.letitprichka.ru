<?php
namespace app\models\Forms;

use app\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var \app\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Ссылка на восстановление пароля не верная или не действительная. Запросите восстановление пароля снова');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Ссылка для восстановления пароля больше не действительна. Запросите восстановление пароля снова');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     * @throws \yii\base\Exception
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}