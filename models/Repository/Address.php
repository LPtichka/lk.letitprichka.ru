<?php

namespace app\models\Repository;

use app\components\Dadata;
use app\models\Builder\Suggestions;
use app\models\Queries\AddressQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%address}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $status
 * @property string $city
 * @property string $street
 * @property string $house
 * @property string $housing
 * @property string $building
 * @property string $flat
 * @property string $floor
 * @property string $porch
 * @property int $postcode
 * @property string $description
 * @property string $full_address
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $customer
 */
class Address extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_ACTIVE = 10;

    /** @var bool */
    public $is_default_address = false;

    /** @var bool */
    public $address_detailed = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address}}';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'           => \Yii::t('address', 'ID'),
            'customer_id'  => \Yii::t('address', 'Customer ID'),
            'full_address' => \Yii::t('address', 'Full address'),
            'city'         => \Yii::t('address', 'City'),
            'street'       => \Yii::t('address', 'Street'),
            'house'        => \Yii::t('address', 'House'),
            'housing'      => \Yii::t('address', 'Housing'),
            'building'     => \Yii::t('address', 'Building'),
            'flat'         => \Yii::t('address', 'Flat'),
            'floor'        => \Yii::t('address', 'Floor'),
            'porch'        => \Yii::t('address', 'Porch'),
            'postcode'     => \Yii::t('address', 'Postcode'),
            'description'  => \Yii::t('address', 'Extra info'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 10],
            [['customer_id', 'postcode', 'status'], 'integer'],
            [['city', 'street', 'house', 'housing', 'floor', 'porch', 'building', 'flat', 'full_address', 'description'], 'string'],
            [['city', 'street', 'house', 'customer_id', 'full_address'], 'required'],
            [['customer_id'], 'exist', 'targetClass' => Customer::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующий ID покупателя'],
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
     * @param array $data
     * @return Address
     */
    public function build(array $data): Address
    {
        $address = new Address();

        $address->description = (string) $data['description'];
        $address->customer_id = (int) $data['customer_id'];
        $address->prepareAddress($address, $data['full_address']);

        return $address;
    }

    /**
     * @param Address $address
     * @param string $fullAddress
     * @return Address
     */
    public function prepareAddress(Address $address, string $fullAddress): Address
    {
        try {
            $suggestions = (new Dadata())->getSuggestions('address', [
                'query' => $fullAddress,
                'limit' => 10,
            ]);
        } catch (\Exception $e) {
            \Yii::info($e->getMessage(), 'address-import-exception');
            return $address;
        }

        \Yii::info($suggestions['suggestions'], 'debug-address');

        $suggest = (new Suggestions())->setSuggestions($suggestions['suggestions'] ?? [])->build();
        if (isset($suggest[0])) {
            /** @var Suggestion $data */
            $data = $suggest[0];

            $address->full_address = $data->value;
            $address->city         = $data->getData()->cityWithType;
            $address->street       = $data->getData()->streetWithType ?? '-';
            $address->house        = $data->getData()->house ?? '-';
            $address->housing      = $data->getData()->block;
            //$address->flat         = $data->getData()->flat;
            $address->postcode     = $data->getData()->postalCode;
        }

        return $address;
    }

    /**
     * @param string $fullAddress
     * @param int|null $customerId
     * @return Address
     */
    public function getByFullAddress(string $fullAddress, ?int $customerId): ?Address
    {
        $address = Address::find()->where(['full_address' => $fullAddress, 'customer_id' => $customerId])->one();
        return $address;
    }

    /**
     * @inheritdoc
     * @return AddressQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AddressQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getFullAddress(): string
    {
        $address = '';
        if (!empty($this->city) && $this->city != 'null') {
            $address .= $this->city;
            if (!empty($this->street)) {
                $address .= ', '. $this->street;
            }
        } else {
            $address .= $this->full_address;
        }

        if (!empty($this->house)) {
            $address .= ', д. '. $this->house;
        }
        if (!empty($this->flat)) {
            $address .= ', кв. '. $this->flat;
        }
        if (!empty($this->floor)) {
            $address .= ', этаж '. $this->floor;
        }
        if (!empty($this->porch)) {
            $address .= ', подъезд '. $this->porch;
        }
        if (!empty($this->description)) {
            $address .= ', доп. информация: '. $this->description;
        }

//        if (!empty($this->full_address) && !empty($this->city)) {
//            $address .= ', ('. $this->full_address . ')';
//        }

        return $address;
    }
}