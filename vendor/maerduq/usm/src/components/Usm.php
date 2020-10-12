<?php

namespace maerduq\usm\components;

use Yii;
use yii\helpers\Url;
use maerduq\usm\models\MenuItem;
use maerduq\usm\models\Page;
use maerduq\usm\models\Redirect;

class Usm {

    static $accessOptions = [
        0 => "All visitors",
        1 => "Only logged in users",
        2 => "Only administrators"
    ];

    static function getCurrentPage() {
        if (Yii::$app->getModule('usm')->getPageUrl() != null) {
            return Yii::$app->getModule('usm')->getPageUrl();
        } else {
            return null;
        }
    }

    static $_activeMenuItem = null;

    static function mockCurrentActionForMenu($url) {
        self::$_activeMenuItem = $url;
    }

    static function getCurrentUrl() {
        if (Yii::$app->getModule('usm')->getPageUrl() != null) {
            return Yii::$app->getModule('usm')->getPageUrl()->url;
        } else {
            return null;
        }
    }

    static function getBreadcrumbs($pageUrl = null, $lastWithLink = false) {
        if (Yii::$app->getModule('usm')->isUserAdmin()) {
            $access = 2;
        } elseif (Yii::$app->user->isGuest) {
            $access = 0;
        } else {
            $access = 1;
        }
        $ret = [];

        if ($pageUrl == null) {
            if (isset(Yii::$app->controller->view->params['breadcrumbs'])) {
                return Yii::$app->controller->view->params['breadcrumbs'];
            }
            $page = Yii::$app->getModule('usm')->getPageUrl();
        } else {
            if (substr($pageUrl, 0, 1) == '/') {
                $pageUrl = substr($pageUrl, 1);
            }
            $page = Redirect::findOne(['url' => $pageUrl]);
        }

        if ($page != null) {
            if ($page->menuItem != null) {
                $accessAllowed = ($page->menuItem->page != null) ? $page->menuItem->page->access : $page->menuItem->access;
                if ($page->menuItem->parent_id != null) {
                    if ($page->menuItem->parent->redirect != null) {
                        $ret[] = ['label' => $page->menuItem->parent->title, 'url' => Url::base() . "/" . $page->menuItem->parent->redirect->url];
                    } else {
                        $ret[] = $page->menuItem->parent->title;
                    }
                }
                $ret[] = ($lastWithLink) ? ['label' => $page->menuItem->title, 'url' => Url::base() . '/' . $page->menuItem->redirect->url] : $page->menuItem->title;
            } else {
                switch ($page->type) {
                    case 'cms':
                        $accessAllowed = $page->page->access;
                        $ret[] = $page->page->title;
                        break;
                    default:
                        $accessAllowed = 0;
                        $ret = [];
                }
            }
        } else {
            $accessAllowed = 0;
            $ret = [];
        }

        if ($access < $accessAllowed) {
            return [];
        } else {
            return $ret;
        }
    }

    static function datetime($phpdate = null) {
        if ($phpdate == null) {
            $phpdate = time();
        }
        return date('Y-m-d H:i:s', $phpdate);
    }

    static function returnUrl($hash = null, $default = null) {
        if ($hash == null && $default == null) {
            $url = $_SERVER['REQUEST_URI'];
            $hash = substr(md5($url), 1, 5);
            $_SESSION['ret'][$hash] = $url;

            if (count($_SESSION['ret']) > 100) {
                array_pop($_SESSION['ret']);
            }
            return $hash;
        } elseif ($hash == null && $default != null) {
            return $default;
        } elseif (isset($_SESSION['ret'][$hash])) {
            return $_SESSION['ret'][$hash];
        } else {
            return $default;
        }
    }

    static private $_menu = null;

    static function getMenu($force_access = null, $flat = false) {
        $usm = Yii::$app->getModule('usm');
        if ($force_access == null && $flat == false && self::$_menu != null) {
            return self::$_menu;
        }

        if ($force_access !== null) {
            $access = $force_access;
        } elseif ($usm->isUserAdmin()) {
            $access = 2;
        } elseif (Yii::$app->user->isGuest) {
            $access = 0;
        } else {
            $access = 1;
        }

        $lang = Yii::$app->language;
        if (!in_array($lang, $usm->languages)) {
            $lang = $usm->languages[0];
        }

        $items_raw = MenuItem::find()
            ->select(['t.*', 'IFNULL(in.value, t.title) AS title'])
            ->from(MenuItem::tableName() . ' AS t')
            ->leftJoin('usm_translations AS in', 'in.item_type = "menu_item" AND in.item_id = t.id AND in.key = "title" AND in.lang = :lang', ['lang' => $lang])
            ->joinWith(['parent' => function ($query) {
                    $query->from(MenuItem::tableName() . ' AS parent');
                },
                'page' => function ($query) {
                    $query->from(Page::tableName() . ' AS page');
                }])
            ->where('t.visible = 1')
            ->andWhere('IF(parent.id IS NOT NULL, parent.visible=1, 1)')
            ->andWhere('IF(page.id IS NOT NULL, 
					page.access <= :access, 
					t.access <= :access)', ['access' => $access])
            ->andWhere('IF(parent.id IS NOT NULL, parent.access <= :access, 1)', ['access' => $access])
            ->with('page')
            ->orderBy('IF(parent.position is null, `t`.`position` * 100, (parent.position * 100)+t.position) ASC')
            ->all();

        $items = [];
        $last_parent = 1;

        $curItem = self::getCurrentPage();
        $activeMenuItem = self::$_activeMenuItem;

        foreach ($items_raw as $item) {
            $active = false;

            switch ($item->type) {
                case 'php':
                    $parts = explode("?", $item->url);
                    if (count($parts) > 1) {
                        $getdata = explode('&', $parts[1]);
                        $urlpart2 = [];
                        foreach ($getdata as $gd) {
                            $gd = explode('=', $gd);
                            $urlpart2[$gd[0]] = $gd[1];
                        }
                        $url = [$parts[0]] + $urlpart2;
                    } else {
                        $url = [$parts[0]];
                    }

                    if ($activeMenuItem != null) {
                        $active = ($activeMenuItem == $item->url);
                    } elseif ($curItem != null) {
                        if ($curItem->menuItem != null) {
                            $active = ($curItem->menuItem->type == 'php' && $item->url == $curItem->menuItem->url);
                        } else {
                            $active = ($curItem->type == 'php' && $item->url == $curItem->destination);
                        }
                    }

                    break;
                case 'cms':
                    if ($item->page == null) {
                        $url = '#';
                    } else {
                        $url = ['/usm/interpret/page', 'id' => $item->page->id];
                        if ($curItem != null) {
                            if ($curItem->menuItem != null) {
                                $active = ($curItem->menuItem->type == 'cms' && $item->page->id == $curItem->menuItem->page_id);
                            } else {
                                $active = ($curItem->type == 'cms' && $item->page->id == $curItem->destination);
                            }
                        }
                    }
                    break;
                case 'link':
                    $url = ['/usm/interpret/link', 'href' => $item->url];
                    break;
                case 'empty':
                    $url = '#';
                    break;
                default:
                    $url = '#';
            }

            $new_item = [
                'label' => $item->title,
                'url' => $url,
                'active' => $active
            ];

            if ($item->parent == null || $flat) {
                $items[] = $new_item;
                $last_parent = count($items) - 1;
            } else {
                $items[$last_parent]['items'][] = $new_item;
                if ($new_item['active']) {
                    $items[$last_parent]['active'] = true;
                }
            }
        }

        if ($force_access == null && $flat == false) {
            self::$_menu = $items;
        }
        return $items;
    }

    static function getSubmenu() {
        if (self::$_menu == null) {
            self::getMenu();
        }
        foreach (self::$_menu as $m) {
            if ($m['active'] && isset($m['items'])) {
                return $m['items'];
            }
        }
        return [];
    }

    static function evalContent($string) {
        $replaces = [
            '{{baseUrl}}' => Url::base()
        ];
        return str_replace(array_keys($replaces), array_values($replaces), $string);
    }

    static function isUserAdmin() {
        return Yii::$app->getModule('usm')->isUserAdmin();
    }

    static function makeAlias($name) {
        $name = strtolower($name);
        $name = self::transliterateString($name);
        $name = str_replace(" ", "-", $name);
        $name = preg_replace("/[^a-z0-9\-]/", "", $name);
        return $name;
    }

    public static function transliterateString($txt) {
        $transliterationTable = ['á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'e', 'ё' => 'e', 'Ё' => 'e', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja'];
        $txt = str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
        return $txt;
    }

}
