<?php

namespace app\models;

use app\validators\Tumail;
use Yii;

class Customer extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['email'], 'email'],
            [['email'], Tumail::className()],
            ['name', 'string', 'max' => 90],
            ['student_no', 'integer', 'min' => 0],
        ];
    }

    public static function tableName()
    {
        /*
        [id]        [int 11]    user who ordered the components
        [email]     [text]      text field containing the email address of the customer
        [name][text]      text field containing the name of the customer
        [student_no][int 11]    contains the student number of the customer
         */
        return 'customers';
    }

    public function getOrder()
    {
        return $this->hasMany(Order::className(), ['user_id' => 'id']);
    }

    public function addCustomer($email, $name, $student_no)
    {
        $user = new Customer();
        $user->email = $email;
        $user->name = $name;
        $user->student_no = $student_no;
        $user->save(); //new row added to table
        return $user->id;
    }

    public function getCustomerById($user_id)
    {
        $details = Customer::find()->where(['id' => $user_id])->one(); //is a unique number so should return only 1
        return $details;
    }

    public function getCustomerByName($name)
    {
        $details = Customer::find()->where(['fist_name' => $name])->all();
        return [$details, count($details)];
    }

    public function getCustomerByEmail($email)
    {
        $details = Customer::find()->where(['email' => $email])->all();
        return [$details, count($details)];
    }

    public function getCustomerByStudent_no($student_no)
    {
        $details = Customer::find()->where(['student_no' => $student_no])->one(); //is a unique number so should return only 1
        return $details;
    }
}
