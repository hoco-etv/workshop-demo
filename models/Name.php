<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class Name extends ActiveRecord
{
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['first_name', 'last_name'], 'safe'],
        ];
    }

    public static function tableName() {
        return 'names';
    }
}
