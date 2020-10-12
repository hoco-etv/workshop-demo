<?php

namespace maerduq\usm\models;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "usm_translations".
 *
 * @property integer $id
 * @property string $item_type
 * @property integer $item_id
 * @property string $lang
 * @property string $key
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 */
class Translation extends \yii\db\ActiveRecord {

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'usm_translations';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['item_type', 'lang', 'key'], 'required'],
            [['item_id'], 'integer'],
            [['value'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_type'], 'string', 'max' => 50],
            [['lang'], 'string', 'max' => 10],
            [['key'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'item_type' => 'Item Type',
            'item_id' => 'Item ID',
            'lang' => 'Lang',
            'key' => 'Key',
            'value' => 'Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
