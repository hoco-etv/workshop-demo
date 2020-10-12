<?php

namespace maerduq\usm\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "usm_files".
 *
 * The followings are the available columns in table 'usm_files':
 * @property integer $id
 * @property string $category
 * @property string $name
 * @property int $access
 * @property string $file_name
 * @property string $file_type
 * @property integer $file_size
 * @property string $file_ext
 * @property string $last_accessed_at
 * @property string $created_at
 * @property string $updated_at
 */
class File extends ActiveRecord {

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    public $file, $prefix = 'usm/';

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'usm_files';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['file_name', 'file_type', 'file_size', 'file_ext', 'access'], 'required'],
            [['file_size', 'access'], 'integer'],
            [['name', 'category', 'file_type'], 'string', 'max' => 60],
            [['file_name'], 'string', 'max' => 120],
            [['file_ext'], 'string', 'max' => 10],
            [['file'], 'file'],
            [['file_name'], 'unique'],
            ['name', 'unique', 'targetAttribute' => ['name', 'category', 'file_ext']],
            [['file'], 'required', 'on' => 'new'],
            [['name'], 'required', 'except' => 'new'],
            // The following rule is used by search().
            [['id', 'name', 'category', 'file_name', 'file_type', 'file_size', 'file_ext'], 'safe', 'on' => 'search']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'category' => 'Category',
            'file_name' => 'File Name',
            'file_type' => 'File Type',
            'file_size' => 'File Size',
            'file_size_readable' => 'File Size',
            'file_ext' => 'File Extension',
            'last_accessed_at' => 'Last Accessed',
            'updated_at' => 'Updated'
        ];
    }

    public function beforeValidate() {
        $continue = false;
        if (is_object($this->file) && get_class($this->file) == 'yii\web\UploadedFile') {
            $continue = true;
        } else if ((UploadedFile::getInstance($this, 'file') != null)) { //als er een nieuw bestand is
            $this->file = UploadedFile::getInstance($this, 'file');
            $continue = true;
        }

        if (!$continue) {
            return true;
        }

        if ($this->name == '') {
            $str = explode(".", $this->file->name);
            array_pop($str);
            $this->name = implode(".", $str);
        }

        if ($this->file_name !== null) {
            @unlink(self::fileDir() . $this->file_name);
        }
        do {
            $characters = "0123456789abcdefghijklmnopqrstuvwxyzQWERTYUIOPLKJHGFDSAMNBVCXZ";
            $string = "";
            for ($p = 0; $p < 20; $p++) {
                $string .= $characters[mt_rand(0, strlen($characters) - 1)];
            }
            $newFileName = $this->prefix . $string;
        } while (file_exists(self::fileDir() . $newFileName));
        $this->file_name = $newFileName;

        $this->file_type = $this->file->type;
        $this->file_size = $this->file->size;
        $this->file_ext = $this->file->extension;

        return true;
    }

    public function afterSave($insert, $changedAttributes) {
        if (is_object($this->file) && get_class($this->file) == "yii\web\UploadedFile") {
            $img = self::fileDir() . $this->file_name;
            $this->file->saveAs($img);
        }
    }

    public function afterDelete() {
        @unlink(self::fileDir() . $this->file_name);
        return true;
    }

    public static function fileDir() {
        switch ($_SERVER['HTTP_HOST']) {
            default:
                $img = Yii::$app->basePath . "/files/";
        }

        return $img;
    }

    public function getFile_size_readable() {
        if ($this->file_size < 1000) {
            return $this->file_size . " byte";
        } elseif (($temp = $this->file_size / 1000) < 1000) {
            return round($temp, 2) . " kB";
        } elseif (($temp = $temp / 1000) < 1000) {
            return round($temp, 2) . " MB";
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

        return $dataProvider;
    }

    public static function make($name, $category, $postFile, $prefix = '', $access = 0) {
        $file = self::findOne(['name' => $name, 'category' => $category]);
        if ($file === null) {
            $file = new self();
            $file->name = $name;
            $file->category = $category;
            $file->prefix = $prefix;
            $file->access = $access;
        }
        $file->file = $postFile;
        if (!$file->save()) {
            throw new \yii\web\HttpException(404, \yii\helpers\Html::errorSummary($file));
        }
        return $file;
    }

}
