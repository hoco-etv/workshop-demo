<?php

namespace maerduq\usm;

use Yii;

class UsmModule extends \yii\base\Module {

    public $controllerNamespace = 'maerduq\usm\controllers';
    public $languages = null;
    public $access_type = 'usm'; //['usm', 'yii']
    public $access_password = 'usmadmin';
    public $access_admin_check = null;
    public $layout_container = '@usm/views/layouts/default';
    public $layout_plain = '@usm/views/layouts/default';
    public $layout_empty = '@usm/views/layouts/empty';
    public $sitemaps = [];
    public $plugins = [];
    private $_menu = [
        [
            'label' => '<span class="glyphicon glyphicon-tasks"></span> Menu',
            'url' => ['/usm/menu/admin']
        ],
        [
            'label' => '<span class="glyphicon glyphicon-edit"></span> Pages',
            'url' => ['/usm/pages/admin']
        ],
        [
            'label' => '<span class="glyphicon glyphicon-text-width"></span> Textblocks',
            'url' => ['/usm/textblocks/admin']
        ],
        [
            'label' => '<span class="glyphicon glyphicon-transfer"></span> URLs',
            'url' => ['/usm/redirects/index']
        ],
        [
            'label' => '<span class="glyphicon glyphicon-file"></span> Files',
            'url' => ['/usm/files/index']
        ],
    ];
    private $_plugins = [];

    public function init() {
        parent::init();
        
        Yii::setAlias('@usm', __DIR__);

        // language things
        if (!is_array($this->languages)) {
            $this->languages = [Yii::$app->language];
        } elseif ($this->languages[0] != Yii::$app->language) {
            $this->languages = array_merge([Yii::$app->language], $this->languages);
        }

        //submodule things
        $dir = dirname(__FILE__) . "/modules";
        if (file_exists($dir)) {
            $dirs = scandir($dir);
            unset($dirs[0]);
            unset($dirs[1]);
            foreach ($dirs as $item) {
                if (is_dir($dir . "/" . $item) && file_exists($dir . "/" . $item . "/" . ucfirst($item) . "Module.php")) {
                    $this->_plugins[] = $item;
                }
            }
        }

        if ($this->_plugins != []) {
            $this->setModules($this->_plugins);
            foreach ($this->_plugins as $module) {
                $menuitems = $this->getModule($module)->menuItems();
                foreach ($menuitems as $menuitem) {
                    $this->_menu[] = $menuitem;
                }
            }
        }
        foreach ($this->_plugins as $module) {
            $this->getModule($module)->menu = $this->_menu;
        }

        //new plugin things
        if ($this->plugins != []) {
            $pluginMenu = [];
            foreach ($this->plugins as $pluginController) {
                $controller = Yii::$app->createController($pluginController);
                if ($controller == null) {
                    continue;
                }

                $menuitems = $controller[0]->menuItems();
                foreach ($menuitems as $menuitem) {
                    $pluginMenu[] = $menuitem;
                }
            }
            $this->_menu[] = ['label' => 'Plugins', 'items' => $pluginMenu];
        }
    }

    public function getMenu() {
        return $this->_menu;
    }

    private $_pageUrl = null;

    public function getPageUrl() {
        return $this->_pageUrl;
    }

    public function setPageUrl($val) {
        $this->_pageUrl = $val;
    }

    public function isUserAdmin() {
        switch ($this->access_type) {
            case 'yii':
                if (is_callable($this->access_admin_check)) {
                    return call_user_func($this->access_admin_check);
                } else {
                    return !Yii::$app->user->isGuest;
                }
            case 'usm':
                return (Yii::$app->session->get('usm_loggedin') == true);
            default:
                throw new \yii\web\HttpException(500, 'USM Access rules have not been set properly');
        }
    }

    public function getAccessRules() {
        switch ($this->access_type) {
            case 'yii':
                if (is_callable($this->access_admin_check)) {
                    return [
                        [
                            'allow' => call_user_func($this->access_admin_check)
                        ]
                    ];
                } else {
                    return [
                        [
                            'allow' => true,
                            'roles' => ['@']
                        ],
                        [
                            'allow' => false
                        ]
                    ];
                }
            case 'usm':
                return [
                    [
                        'allow' => Yii::$app->session->get('usm_loggedin')
                    ]
                ];
            default:
                throw new \yii\web\HttpException(500, 'USM Access rules have not been set properly');
        }
    }

}
