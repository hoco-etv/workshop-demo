<?php

namespace maerduq\usm\models;

use maerduq\usm\components\TranslatedActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

/**
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $access
 * @property boolean $wysiwyg
 * @property string $style
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property MenuItem[] $menuItems
 */
class Page extends TranslatedActiveRecord {

    public function translationSettings() {
        return [
            'item_type' => 'page',
            'keys' => ['title', 'content']
        ];
    }
    
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    private $_oldAccess;
    public static $styleOptions = [
        "container" => "In view container",
        "plain" => "Only in layout",
        "empty" => "No layout"
    ];

    public function init() {
        $this->wysiwyg = 1;
        $this->access = 2;
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'usm_pages';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['title', 'access'], 'required'],
            [['wysiwyg', 'access'], 'integer'],
            [['title', 'style'], 'string', 'max' => 100],
            [['content'], 'safe']
        ];
    }

    public function getMenuItems() {
        return $this->hasMany(MenuItem::className(), ['page_id' => 'id'])
                ->where(MenuItem::tableName() . '.type="cms"');
    }

    public function getRedirects() {
        return $this->hasMany(Redirect::className(), ['destination' => 'id'])
                ->where('type = "cms" AND forward = 0');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Page Title',
            'content' => 'Page Content',
            'wysiwyg' => 'WYSIWYG editing',
            'style' => 'Presenting style',
            'access' => 'Who may see this page?',
        ];
    }

    public function afterFind() {
        $this->_oldAccess = $this->access;
    }

    public function afterSave($insert, $changedAttributes) {
        if ($this->_oldAccess != $this->access) {
            foreach ($this->menuItems as $m) {
                $m->access = $this->access;
                $m->save();
            }
        }
    }

    public function search($params = null) {

        $query = self::find()->with(['menuItems', 'redirects']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $this->andFilterWhere('id', $this->id);
        $this->andFilterWhere('title', $this->title, true);
        $this->andFilterWhere('content', $this->content, true);

        return $dataProvider;
    }

}
