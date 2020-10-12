<?php

namespace maerduq\usm\components;

use yii\db\ActiveRecord;
use maerduq\usm\models\Translation;
use Yii;

class TranslatedActiveRecord extends ActiveRecord {

    public function translationSettings() {
        die('NOT SET!');
        return [
            'item_type' => '',
            'keys' => []
        ];
    }

    public function saveTranslations($input) {
        $languages = Yii::$app->getModule('usm')->languages;
        array_shift($languages);

        $settings = $this->translationSettings();
        foreach ($settings['keys'] as $k) {
            foreach ($languages as $l) {
                if (isset($input[$k][$l])) {
                    $attributes = [
                        'item_type' => $settings['item_type'],
                        'item_id' => $this->id,
                        'lang' => $l,
                        'key' => $k
                    ];
                    $translation = Translation::find()->where($attributes)->one();
                    if ($translation === null) {
                        $translation = new Translation();
                        $translation->attributes = $attributes;
                    }
                    $translation->value = $input[$k][$l];
                    $translation->save();
                }
            }
        }
    }

    public function __get($name) {
        try {
            return parent::__get($name);
        } catch (\yii\base\UnknownPropertyException $e) {
            $settings = $this->translationSettings();
            foreach ($settings['keys'] as $k) {
                if ($name == $k . 'Translations') {
                    $value = $this->hasMany(Translation::className(), ['item_id' => 'id'])
                            ->where('item_type = "' . $settings['item_type'] . '"')
                            ->andWhere('`key` = "' . $k . '"')->findFor($name, $this);
                    return $value;
                } elseif ($name == $k . 'TranslationsByKey') {
                    $value = $this->hasMany(Translation::className(), ['item_id' => 'id'])
                            ->where('item_type = "' . $settings['item_type'] . '"')
                            ->andWhere('`key` = "' . $k . '"')->findFor($name, $this);
                    $out = [];
                    foreach ($value as $item) {
                        $out[$item->lang] = $item;
                    }
                    return $out;
                }
            }
            throw new \yii\base\UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

}
