<?php
namespace app\models;
use Yii;
use yii\base\Model;
/**
 * ConfirmForm is the model behind the confirmation of orders.
 *
 */
class ConfirmForm extends Model
{
    public $consent;

    public function rules()
    {
        return [
            array('consent', 'required', 'requiredValue' => 1, 'message' => 'Please accept these terms.'),
        ];
    }
}