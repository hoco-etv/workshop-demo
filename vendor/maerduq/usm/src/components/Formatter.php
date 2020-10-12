<?php

namespace maerduq\usm\components;

class Formatter extends \yii\i18n\Formatter {

    public function asTimeAgo($value, $config = null) {
        $sec = time() - strtotime($this->asDatetime($value, 'php:d-m-Y H:i:s'));

        if ($sec < 60) {
            return 'less than a minute ago';
        }
        $min = round($sec / 60);
        if ($min < 60) {
            return $min . ' minutes ago';
        }
        $hour = round($sec / (60 * 60));
        if ($hour < 24) {
            return $hour . ' hours ago';
        }
        $day = round($sec / (60 * 60 * 24));
        if ($day < 14) {
            return $day . ' days ago';
        }
        $week = round($sec / (60 * 60 * 24 * 7));
        return $week . ' weeks ago';
    }

}
