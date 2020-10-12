<?php

namespace maerduq\usm\components;

use Yii;
use yii\web\UrlRuleInterface;
use yii\base\BaseObject;
use maerduq\usm\models\Redirect;

class RedirectRule extends BaseObject implements UrlRuleInterface {

    private $_redirects = [];

    public function parseRequest($manager, $request) {
        $pathInfo = $request->pathInfo;

        // detect language
        $parts = explode('/', $pathInfo);
        $languages = Yii::$app->getModule('usm')->languages;
        $baseLanguage = array_shift($languages);
        if (in_array($parts[0], $languages)) {
            Yii::$app->language = $parts[0];
            array_shift($parts);
            $pathInfo = implode('/', $parts);
        }

        if ($pathInfo == 'usm/sitemap') {
            return ['usm/global/sitemap', []];
        } elseif (substr($pathInfo, 0, 9) == 'usm/page/') {
            return ['usm/interpret/page', ['id' => (int) substr($pathInfo, 9)]];
        } elseif (substr($pathInfo, 0, 5) == 'file/') {
            if (stripos($pathInfo, '?view') !== false) {
                return ['usm/files/download', ['id' => substr($pathInfo, 5, -5), 'view' => true]];
            }
            return ['usm/files/download', ['id' => substr($pathInfo, 5)]];
        }

        try {
            $match = Redirect::find()->with('menuItem')->where(['url' => $pathInfo, 'active' => 1])->one();
        } catch (\yii\db\Exception $e) {

            die('<p>' . $e->getMessage() . '</p>'
                . '<p>USM table migrations should probably be ran!</p>'
                . '<code>yii migrate --migrationPath=@vendor/maerduq/usm/migrations --interactive=0</code>');
        }

        if ($match == null) {
            return [$pathInfo, []];
        }

        if ($match->menu_item_id != null && $match->menuItem != null) {
            if ($match->forward) {
                $menuItemUrl = Redirect::find()->with('menuItem')->where(['type' => 'menu_item', 'menu_item_id' => $match->menu_item_id, 'active' => 1, 'forward' => 0])->one();
                if ($menuItemUrl != null) {
                    Yii::$app->response->redirect(['/' . $menuItemUrl->url]);
                    Yii::$app->end();
                }
            }
            $type = $match->menuItem->type;
            $destination = ($match->menuItem->type == 'cms') ? $match->menuItem->page_id : $match->menuItem->url;
        } else {
            $type = $match->type;
            $destination = $match->destination;
        }

        switch ($type) {
            case 'cms':
                $continue = ['/usm/interpret/page', ['id' => $destination]];
                $forward = ['/usm/interpret/page', 'id' => $destination];
                break;
            case 'link':
                $continue = ['/usm/interpret/link', ['href' => $destination]];
                $forward = ['/usm/interpret/link', 'href' => $destination];
                break;
            default:
                $str = $destination;
                $str = explode('?', $str);
                $ret = [];
                if (count($str) > 1) {
                    $get = explode('&', $str[1]);
                    foreach ($get as $item) {
                        $item = explode('=', $item);
                        $ret[$item[0]] = (isset($item[1])) ? $item[1] : true;
                    }
                }
                $continue = [$str[0], $ret];
                $forward = [$str[0]] + $ret;
        }

        if ($match->forward) {
            Yii::$app->response->redirect($forward + ['lang' => Yii::$app->language]);
            Yii::$app->end();
        } else {
            Yii::$app->getModule('usm')->setPageUrl($match);
            return $continue;
        }
    }

    public function createUrl($manager, $route, $params) {

        if ($this->_redirects == []) {
            $temp = Redirect::find()->with('menuItem')->where(['active' => 1, 'forward' => 0])->all();
            foreach ($temp as $item) {
                if ($item->menuItem != null) {
                    $type = $item->menuItem->type;
                    $destination = ($item->menuItem->type == 'cms') ? $item->menuItem->page_id : $item->menuItem->url;
                } else {
                    $type = $item->type;
                    $destination = $item->destination;
                }
                switch ($type) {
                    case 'cms':
                        $destination = 'usm/interpret/page?id=' . $destination;
                        break;
                    case 'link':
                        $destination = 'usm/interpret/link?href=' . $destination;
                        break;
                    default:
                        $destination = (substr($destination, 0, 1) == '/') ? substr($destination, 1) : $destination;
                }
                $this->_redirects[$destination] = $item->url;
            }
        }
        $str = $route;

        $lang = Yii::$app->language;
        $get = [];
        foreach ($params as $key => $item) {
            if (!is_array($item) && $key === 'lang') {
                $lang = $item;
            } else {
                $get[$key] = $item;
            }
        }

        if ($get != []) {
            $str .= "?" . http_build_query($get);
        }

        if (isset($this->_redirects[$str])) {
            $goTo = $this->_redirects[$str];
        } elseif ($route == 'usm/interpret/page' && isset($params['id'])) {
            $goTo = 'usm/page/' . $params['id'];
        } elseif ($route == 'usm/files/download' && isset($params['category']) && isset($params['name'])) {
            $goTo = 'file/' . (($params['category'] === '') ? $params['name'] : ($params['category'] . '/' . $params['name']));
        } elseif ($route == 'usm/files/download' && isset($params['id'])) {
            $goTo = 'file/' . $params['id'];
            if (isset($params['view'])) {
                $goTo .= '?view';
            }
        } else {
            $goTo = $str;
        }

        $languages = Yii::$app->getModule('usm')->languages;
        $baseLanguage = array_shift($languages);

        if (in_array($lang, $languages)) {
            return $lang . '/' . $goTo;
        } else {
            return $goTo;
        }

        return false;  // this rule does not apply
    }

}
