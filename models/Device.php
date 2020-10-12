<?php

namespace app\models;

use Exception;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use app\validators\Tumail;


class Device extends \yii\db\ActiveRecord
{

    public $userReport, $user, $userEmail;
    public static $statusArray =
    [
        0 =>
        [
            'message'   =>  'Device OK',
            'icon'      =>  "<span class='glyphicon glyphicon-ok-sign' style='color:#115225;'></span>"
        ],
        1 =>
        [
            'message'   =>  "Prolem reported",
            'icon'      =>  "<span class='glyphicon glyphicon glyphicon-exclamation-sign' style='color:#da961b;'></span>"
        ],
        // TODO this problem can be noted to the 'repair notes' field or to a seperate db where each unique issue gets its own ID and device id
        // this would allow for a maintenance overview of the device
        2 =>
        [
            'message'   =>  'Repair in process',
            'icon'      =>   "<span class='glyphicon glyphicon-info-sign' style='color:#da961b;'></span>"
        ],
        3 =>
        [
            'message'   =>  'Decommissioned',
            'icon'      =>  "<span class='glyphicon glyphicon-remove-sign' style='color:#ff2234' ></span>"
        ]
    ];

    public function rules()
    {
        return [
            [['description'], 'string', 'max' => 500],
            [['repair_notes'], 'string', 'max'=>65000],
            [['brand', 'name', 'type'], 'safe', 'on' => 'search'],
            [['brand', 'name'], 'required', 'on' => 'new'],
            [['brand', 'name', 'type'], 'string', 'max' => 100],
            [['userReport'], 'required', 'on' => 'report'],
            [['image','manual'], 'string', 'max' => 350],
            [['status'], 'integer'],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->last_updated_at = date("Y-m-d H:i:s");
        return true;
    }


    public static function tableName()
    {
        return 'devices';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',

        ];
    }

    /**
     * returns array of unique attributes in database
     *
     * @param string $attribute attribute of which a list of distict values is requested
     * @return array array of distinct values for the provided attribute
     */
    public static function getDistict($attribute) //returns a list of unique categories from the db
    {
        $distinct = array();
        try {
            $models = Device::find()->select([$attribute])->distinct()->all();
        } catch (Exception $e) {
            return [];
        }

        foreach ($models as $model) {
            array_push($distinct, $model->$attribute);
        }
        return $distinct;
    }

    public function search($params)
    {
        $query = Device::find()->orderBy('status');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'brand', $this->brand])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }

    public function getStatus()
    {
        return self::$statusArray[$this->status];
    }

    public function getHTML()
    {
        $html =
            "<div class='panel panel-default'>" .
            "<div class='panel-body device'>" .
            "<div class='device-img'>" .
            "<img src='$this->image'>" .
            "</div>" .
            "<div class='device-info'>" .
            "<p class='device-name'>" .
            $this->brand . " " . $this->name .
            "<br><a class='device-report'
                            href='/devices/report?id=$this->id'>" .
            " Report issue " .
            "</a>" . (Yii::$app->user->isGuest
                ? ''
                : "<a class='device-report' style='margin-left:20px' href='/admin/repair_device?id=$this->id'>Edit/repair device</a>") .
            "</p>" .
            "<p class='device-description'>" .
            $this->description .
            "</p>" .
            "<p class='device-last-updated'>" .
            "Last updated: " . $this->last_updated_at .
            "</p>" .
            "</div>" .
            "<div class='device-icons'>" .
            "<div class='device-status device-glyph' 
                        data-toggle='tooltip' 
                        title='" . $this->getStatus()['message'] . "'>" .
            $this->getStatus()['icon'] .
            "</div>" . ($this->manual ?
                "<div class='device-manual device-glyph'>" .
                "<a 
                            href='" . $this->manual . "'
                            target='_blank'
                            class='glyphicon glyphicon-book' style='text-decoration:none'
                            data-toggle='tooltip'
                            title='View manual'
                            ></a>" .
                "</div>"
                : "") .

            "</div>" .
            "<p         class='device-last-updated-2'>" .
            "Last updated: " . $this->last_updated_at .
            "</p>" .
            "</div>" .
            "</div>";
        return $html;
    }

    // TODO QR code generator
    // https://qrcode-library.readthedocs.io/en/latest/
}
