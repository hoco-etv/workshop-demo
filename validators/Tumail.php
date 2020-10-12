<?php

namespace app\validators;

use Yii;
use yii\validators\Validator;
use yii\validators\ValidationAsset;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\JsExpression;


class Tumail extends Validator
{
    protected function validateValue($email)
    {
        $validDomains = array('tudelft.nl', 'student.tudelft.nl');
        list($txt, $domain) = explode("@", $email);

        if (!in_array($domain, $validDomains)) { // if no valid domain is found, send error
            return 'Please use a @tudelft.nl or @student.tudelft.nl email address.';
        }
        return null;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $email = $model->$attribute;
        ValidationAsset::register($view);

        return "
        var validDomains = ['tudelft.nl', 'student.tudelft.nl'];
        var [txt, domain] = value.split('@');

        if(!validDomains.includes(domain)){  
            messages.push(" . JSON::encode('Please use a @tudelft.nl or @student.tudelft.nl email address.') . ");  
        }    
        ";
    }
}
