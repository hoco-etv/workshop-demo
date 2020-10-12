<?php

namespace app\modules\maillist\models;


class Maillist_member extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
            [['user_id', 'list_id'], 'required'],
            [['user_id', 'list_id'], 'integer', 'min' => 0],
        ];
    }

    public static function tableName()
    {
        /* 
         * id -> id of the maillist
         * list_name -> integer representing name of the maillist, should identify which email is linked
         * user_id -> id of linked user, each linked user gets its own entry
         * added_at -> datetime when user was added to maillist
         */
        return 'maillist_members';
    }
}
