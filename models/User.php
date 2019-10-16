<?php
namespace app\models;

use Yii;
use yii\base\Security;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\rbac\DbManager;
use yii\rbac\Role;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $access_token
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $fio
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_SELF_UPDATE = 'selfUpdate';
    const SCENARIO_CREATE = 'create';

    const STATUS_INACTIVE = 0;
    const STATUS_SYSTEM = 1;
    const STATUS_ACTIVE = 10;

    const ROLE_WATCHER = 'manager';
    const ROLE_ROOT = 'root';

    private $_password = '';
    private $role;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire    = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return $statuses = [
            self::STATUS_ACTIVE  => Yii::t('app', 'active'),
            self::STATUS_DELETED => Yii::t('app', 'deleted'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array(
            TimestampBehavior::class,
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['password', 'default', 'value' => (new Security())->generateRandomString(8), 'on' => self::SCENARIO_CREATE],
            ['access_token', 'default', 'value' => $this->generateAccessToken()],
            ['password', 'string', 'min' => 8],
            ['role', 'default', 'value' => 'manager', 'on' => self::SCENARIO_CREATE],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['auth_key', 'default', 'value' => $this->generateAuthKey()],
            [['fio', 'email', 'role'], 'required'],
            [['password'], 'required', 'on' => self::SCENARIO_CREATE],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE], 'on' => self::SCENARIO_CREATE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_SYSTEM, self::STATUS_INACTIVE], 'on' => self::SCENARIO_DEFAULT],
            [['status'], 'integer'],
            ['fio', 'string', 'max' => 256],
            ['access_token', 'string', 'length' => 32],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => Yii::t('app', 'This email address has already been taken')],
            [['email', 'fio'], 'trim']
        ];

        /** @var User $user */
        if ($user = \Yii::$app->user->identity) {
            $roles   = $user->getAllowedRoleIds();
            $rules[] = ['role', 'in', 'range' => $roles];
        }

        return $rules;
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateAccessToken(): string
    {
        return (new Security())->generateRandomString(32);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        return \Yii::$app->security->generateRandomString();
    }

    /**
     * @return Role[]
     */
    public function getAllowedRoleIds()
    {
        return array_keys($this->getAllowedRoles());
    }

    /**
     * @return Role[]
     */
    public function getAllowedRoles()
    {
        /** @var DbManager $authManager */
        $authManager  = \Yii::$app->authManager;
        $currentRoles = $authManager->getRolesByUser(\Yii::$app->user->id);

        $roles = [];
        $this->collectChildRoles($currentRoles, $roles);

        return $roles;
    }

    /**
     * @param Role[] $currentRoles
     * @param $roles array
     */
    private function collectChildRoles($currentRoles, &$roles)
    {
        /** @var DbManager $authManager */
        $authManager = Yii::$app->authManager;
        /** @var Role $currentRole */
        foreach ($currentRoles as $currentRole) {
            if (!isset($roles[$currentRole->name])) {
                $roles[$currentRole->name] = ($currentRole->description) ? $currentRole->description : $currentRole->name;

                if ($childRoles = $authManager->getChildRoles($currentRole->name)) {
                    $this->collectChildRoles($childRoles, $roles);
                }
            }
        }
    }

    /**
     * @throws \yii\base\Exception
     */
    public function resetAccessToken(): void
    {
        $this->access_token = $this->generateAccessToken();
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @param int $status
     * @return string
     */
    public function getStatusByKey(int $status): string
    {
        return $this->getStatuses()[$status];
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return $statuses = [
            self::STATUS_ACTIVE   => \Yii::t('app', 'active'),
            self::STATUS_SYSTEM   => \Yii::t('app', 'system'),
            self::STATUS_INACTIVE => \Yii::t('app', 'deleted'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios                             = parent::scenarios();
        $scenarios[self::SCENARIO_SELF_UPDATE] = ['fio', 'email'];
        $scenarios[self::SCENARIO_CREATE]      = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @return bool
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->_password     = $password;
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
        return true;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = \Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param null $statusCode
     * @return string
     */
    public function getStatusName($statusCode = null)
    {
        return ArrayHelper::getValue($this->statuses, $statusCode ? $statusCode : $this->status);
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        if (!$this->role && ($role = Yii::$app->authManager->getRolesByUser($this->id))) {
            $this->role = array_keys($role)[0];
        }

        return $this->role;
    }

    /**
     * @param $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Отправка письма пользователю
     *
     * @return bool
     */
    public function sendNewUserEmail(): bool
    {
        return \Yii::$app
            ->mailer
            ->compose(
                ['html' => 'newUser-html', 'text' => 'newUser-text'],
                ['user' => $this]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Your new access in ' . \Yii::$app->name)
            ->send();
    }

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return isset($this->getStatuses()[$this->status]) ? $this->getStatuses()[$this->status] : null;
    }

    /**
     * @return string
     */
    public function getFio(): string
    {
        return $this->fio;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
