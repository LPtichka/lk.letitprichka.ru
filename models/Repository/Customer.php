<?php

namespace app\models\Repository;

use app\components\Dadata;
use app\models\Builder\Suggestions;
use app\models\Helper\Phone;
use app\models\Product;
use app\models\Queries\CustomerQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%customer}}".
 *
 * @property int $id
 * @property int $status
 * @property int $type
 * @property string $fio
 * @property string $email
 * @property string $comment
 * @property string $phone
 * @property string $default_address_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Address[] $addresses
 *
 * @property Exception[] $exceptions
 */
class Customer extends \yii\db\ActiveRecord
{
    const SCENARIO_NEW_CUSTOMER = 'new_customer';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer}}';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'                 => \Yii::t('customer', 'ID'),
            'fio'                => \Yii::t('customer', 'FIO'),
            'phone'              => \Yii::t('customer', 'Phone'),
            'email'              => \Yii::t('customer', 'Email'),
            'comment'            => \Yii::t('customer', 'Comment'),
            'default_address_id' => \Yii::t('customer', 'Default address ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'filter', 'filter' => function (){
                return '+7' . (new Phone((string) $this->phone))->getClearPhone();
            }],
            [['default_address_id', 'type', 'status'], 'integer'],
            [['fio', 'email', 'phone', 'comment'], 'string'],
            [['type'], 'default', 'value' => 1],
            [['status'], 'default', 'value' => 10],
            [['fio', 'phone'], 'required', 'on' => self::SCENARIO_NEW_CUSTOMER],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Address::class, ['customer_id' => 'id'])->andWhere(['address.status' => 10]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExceptions()
    {
        return $this->hasMany(Exception::class, ['id' => 'exception_id'])
            ->viaTable('{{%customer_exception}}', ['customer_id' => 'id']);
    }

    /**
     * @param array $data
     * @return Customer
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function build(array $data): Customer
    {
        $customer           = new Customer();
        $customer->scenario = self::SCENARIO_NEW_CUSTOMER;

        $customer->fio   = trim($data['fio']);
        $customer->email = trim($data['email']);
        $customer->phone = trim($data['phone']);

        if (!empty(trim($data['full_address']))) {
            $address              = new Address();
            $address->description = (string) $data['description'];

            $suggestions = (new Dadata())->getSuggestions('address', [
                'query' => $data['full_address'],
                'limit' => 10,
            ]);

            $suggest = (new Suggestions())->setSuggestions($suggestions['suggestions'] ?? [])->build();
            if (isset($suggest[0])) {
                /** @var Suggestion $data */
                $data = $suggest[0];

                $address->full_address = $data->value;
                $address->city         = $data->getData()->cityWithType;
                $address->street       = $data->getData()->streetWithType;
                $address->house        = $data->getData()->house;
                $address->housing      = $data->getData()->block;
                $address->flat         = $data->getData()->flat;
                $address->postcode     = $data->getData()->postalCode;
            }

            $customer->setAddresses([$address]);
        }

        return $customer;
    }

    /**
     * @param Address[] $addresses
     */
    public function setAddresses(array $addresses): void
    {
        $this->addresses = $addresses;
    }

    /**
     * @return Address|null
     */
    public function getDefaultAddress(): ?Address
    {
        foreach ($this->addresses as $address) {
            if ($address->id === $this->default_address_id) {
                return $address;
            }
        }

        return null;
    }

    /**
     * @param array $params
     * @return Customer
     */
    public function getByParams(array $params)
    {
        $query = Customer::find();

        !empty($params['fio']) && $query->andWhere(['fio' => $params['fio']]);
        !empty($params['email']) && $query->andWhere(['email' => $params['email']]);
        !empty($params['phone']) && $query->andWhere(['phone' => '+7' . (new Phone($params['phone']))->getClearPhone()]);

        $customer = $query->one();

        if (!$customer) {
            $customer = new Customer();
        }
        return $customer;
    }

    /**
     * @inheritdoc
     * @return CustomerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomerQuery(get_called_class());
    }

    /**
     * @param array $exceptions
     */
    public function setExceptions(array $exceptions)
    {
        $this->exceptions = $exceptions;
    }
}