<?php

namespace app\models\Repository;

use app\components\Dadata;
use app\models\Builder\Suggestions;
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
 * @property string $phone
 * @property string $default_address_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Address[] $addresses
 *
 * @property Exception $exception
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer}}';
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
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'                 => \Yii::t('customer', 'ID'),
            'fio'                => \Yii::t('customer', 'FIO'),
            'phone'              => \Yii::t('customer', 'Phone'),
            'email'              => \Yii::t('customer', 'Email'),
            'default_address_id' => \Yii::t('customer', 'Default address ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['default_address_id', 'type', 'status'], 'integer'],
            [['fio', 'email', 'phone'], 'string'],
            [['type'], 'default', 'value' => 1],
            [['status'], 'default', 'value' => 10],
            [['fio', 'email', 'phone'], 'required'],
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
     * @param Address[] $addresses
     */
    public function setAddresses(array $addresses): void
    {
        $this->addresses = $addresses;
    }

    /**
     * @param array $data
     * @return Customer
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function build(array $data): Customer
    {
        $customer = new Customer();

        $customer->fio = trim($data['fio']);
        $customer->email = trim($data['email']);
        $customer->phone = trim($data['phone']);

        if (!empty(trim($data['full_address']))) {
            $address = new Address();
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
                $address->city = $data->getData()->cityWithType;
                $address->street = $data->getData()->streetWithType;
                $address->house = $data->getData()->house;
                $address->housing = $data->getData()->block;
                $address->flat = $data->getData()->flat;
                $address->postcode = $data->getData()->postalCode;
            }

            $customer->setAddresses([$address]);
        }

        return $customer;
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
}