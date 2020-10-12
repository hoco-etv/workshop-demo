<?php

namespace maerduq\usm\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

/**
 * @property integer $id
 * @property boolean $active
 * @property string $url
 * @property string $type
 * @property integer $menu_item_id
 * @property string $destination
 * @property boolean $forward
 * @property boolean $generated
 * @property string $created_at
 * @property string $updated_at 
 * @property MenuItem $menuItem
 * @property Page $page
 */
class Redirect extends ActiveRecord {

    public static $typeOptions = [
        'menu_item' => 'Menu item',
        'cms' => 'Page',
        'php' => 'Controller',
        'link' => 'Link'
    ];
    public static $forwardOptions = ["Endpoint", "Forward"];

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    public function init() {
        $this->type = 'cms';
        $this->forward = 0;
        $this->generated = 0;
    }

    public static function tableName() {
        return 'usm_redirects';
    }

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [['type'], 'required'],
            [['menu_item_id'], 'integer'],
            [['active', 'forward'], 'boolean'],
            [['url'], 'unique'],
            [['url', 'destination'], 'string', 'max' => 200],
            [['destination'], 'safe'],
        ];
    }

    public function getMenuItem() {
        return $this->hasOne(MenuItem::className(), ['id' => 'menu_item_id']);
    }

    public function getPage() {
        return $this->hasOne(Page::className(), ['id' => 'destination']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'url' => 'URL',
            'destination' => 'Destination',
            'menu_item_id' => 'Menu Item'
        ];
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->type != 'menu_item') {
            $this->menu_item_id = null;
        }

        if ($this->type == 'link') {
            $this->forward = 1;
        }

        return true;
    }

    public function search($params = null) {

        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['url' => SORT_ASC],
                'attributes' => ['url', 'destination', 'active', 'redirect']
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->andFilterWhere('id', $this->id);
        $this->andFilterWhere('active', $this->active);
        $this->andFilterWhere('url', $this->url, true);
        $this->andFilterWhere('destination', $this->destination, true);

        return $dataProvider;
    }

}
