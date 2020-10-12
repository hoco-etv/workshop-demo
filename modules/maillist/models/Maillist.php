<?php

namespace app\modules\maillist\models;

use app\models\User;

class Maillist extends \yii\base\BaseObject
{
    public static $maillists = [ // TODO : automate the creation of this list based on keywords or the folder containing the email
        [
            'id' => 0,
            'name' => 'Project confirmed committee',
            'info' => 'Email send when a new project has been confirmed',
            'mail_path' => 'project_confirmed_committee',
        ],
        [
            'id' => 1,
            'name' => 'Order placed committee',
            'info' => 'Email send when a new order has been confirmed',
            'mail_path' => 'order_placed_committee',
        ],
        [
            'id' => 2,
            'name' => 'Devices committee',
            'info' => 'Email to notify a report of a broken device has been made',
            'mail_path' => 'devices_committee',
        ],
        [
            'id' => 3,
            'name' => 'Inventory committee',
            'info' => 'Email to notify the runout of components that should be in stock',
            'mail_path' => 'inventory_committee'
        ],

    ];

    public function rules()
    {
        return [];
    }

    /**
     * Find a maillist based on its id
     * 
     * @param int Id of the maillist to search for
     * @return mixed Maillist
     */
    public static function getMaillistById($id)
    {

        foreach (self::$maillists as $list) {
            if ($list['id'] == $id) {
                return $list;
            }
        }

        return false;
    }

    /**
     * Find all members of a maillist
     * 
     * @param string Basepath of the email within the mail folder (e.g. devices_committee)
     * @return mixed Array of 
     */
    public static function getMaillistMembersByPath($path){
        foreach (self::$maillists as $list) {
            if ($list['mail_path'] === $path) {
                $members = Maillist_member::findAll(['list_id'=>$list['id']]);

                $emails = [];
                foreach($members as $member){
                    array_push($emails, User::findIdentity($member->user_id)->email);
                }
                return $emails;
            }
        }

        return null;
    }
}
