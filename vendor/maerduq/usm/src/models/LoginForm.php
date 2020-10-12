<?php

namespace maerduq\usm\models;

class LoginForm extends \yii\base\Model {

    public $password;

    public function rules() {
        return [
            [['password'], 'required'],
        ];
    }

}
