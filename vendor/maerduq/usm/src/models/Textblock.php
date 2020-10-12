<?php

namespace maerduq\usm\models;

use maerduq\usm\components\TranslatedActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use maerduq\usm\components\Usm;
use Yii;

/**
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class Textblock extends TranslatedActiveRecord {

    public function translationSettings() {
        return [
            'item_type' => 'textblock',
            'keys' => ['text']
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

    public static function tableName() {
        return 'usm_textblocks';
    }

    private static $_adminMode = false;

    public static function setAdminMode($mode = null) {
        if ($mode == null) {
            self::$_adminMode = !self::$_adminMode;
        } else {
            self::$_adminMode = ($mode == true);
        }
        return;
    }

    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['text', 'description'], 'safe']
        ];
    }

    public static function read($name = null, $replaces = []) {
        if ($name == null) {
            return false;
        }

        $lang = Yii::$app->language;
        if (!in_array($lang, Yii::$app->getModule('usm')->languages)) {
            $lang = $usm->languages[0];
        }

        if (!is_array($name)) {
            $result = self::find()
                    ->select(['t.*', 'IFNULL(in.value, t.text) AS text'])
                    ->from(self::tableName() . ' AS t')
                    ->leftJoin('usm_translations AS in', 'in.item_type = "textblock" AND in.item_id = t.id AND in.key = "text" AND in.lang = :lang', ['lang' => $lang])
                    ->where('t.name = :name', ['name' => $name])
                    ->one();

            if ($result == null) {
                $x = new self();
                $x->name = $name;
                $x->save();
                return '';
            } elseif (!self::$_adminMode) {
                return self::evalIt($result->text, $replaces);
            } else {
                return "<div class='tiny-editable' data-name='{$name}' data-checksum='" . md5($result->text) . "'>{$result->text}</div>";
            }
        } else {
            $results = self::find()
                    ->select(['t.*', 'IFNULL(in.value, t.text) AS text'])
                    ->from(self::tableName() . ' AS t')
                    ->leftJoin('usm_translations AS in', 'in.item_type = "textblock" AND in.item_id = t.id AND in.key = "text" AND in.lang = :lang', ['lang' => $lang])
                    ->where('name IN ("' . implode('", "', $name) . '")')
                    ->all();
            
            $return = [];
            foreach ($results as $result) {
                $return[$result->name] = self::evalIt($result->text, $replaces);
            }
            return $return;
        }
    }

    private static function evalIt($text, $replaces) {
        $text = Usm::evalContent($text);
        
        foreach ($replaces as $find => $replace) {
            $text = str_replace('{{' . $find . '}}', $replace, $text);
        }

        return $text;
    }

    public static function write($in = [], $value = null) {
        if ($value != null && !is_array($in)) {
            $in = [$in => $value];
        }
        foreach ($in as $name => $value) {
            $setting = self::findOne(['name' => $name]);
            if ($setting == null) {
                $setting = new self();
                $setting->name = $name;
            }
            $setting->text = $value;
            $setting->update_time = Usm::datetime();
            $setting->save();
        }
    }

    public function search($params = null) {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $this->andFilterWhere('id', $this->id);
        $this->andFilterWhere('name', $this->name, true);
        $this->andFilterWhere('text', $this->text, true);

        return $dataProvider;
    }

}
