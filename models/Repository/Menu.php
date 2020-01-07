<?php

namespace app\models\Repository;

use app\models\Helper\Weight;
use app\models\Queries\MenuQuery;
use app\models\Queries\OrderQuery;
use app\models\Queries\ProductQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     * @return MenuQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'subscription_id' => \Yii::t('order', 'Subscription ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
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
}