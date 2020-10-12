<?php

namespace maerduq\usm\models;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use maerduq\usm\components\Usm;
use maerduq\usm\components\TranslatedActiveRecord;
use Yii;

/**
 * This is the model class for table "menuitems".
 *
 * The followings are the available columns in table 'menuitems':
 * @property integer $id
 * @property integer $parent_id
 * @property integer $position
 * @property string $title
 * @property string $alias
 * @property string $type
 * @property integer $page_id
 * @property string $url
 * @property boolean $visible
 * @property integer $access
 * @property string $created_at
 * @property string $updated_at
 * @property MenuItem $parent
 * @property Page $page
 * @property Redirect $redirect
 * @property MenuItem[] $children
 */
class MenuItem extends TranslatedActiveRecord {

    public function translationSettings() {
        return [
            'item_type' => 'menu_item',
            'keys' => ['title']
        ];
    }

    static $typeOptions = [
        "empty" => "Empty menu item",
        "cms" => 'Page',
        "php" => "Controller",
        "link" => "Link"
    ];

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    public function init() {
        $this->type = "empty";
        $this->visible = 0;
        $this->access = 2;
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'usm_menu_items';
    }

    public function rules() {
        return [
            [['title', 'type', 'access', 'url'], 'required'],
            [['alias'], 'unique'],
            [['parent_id', 'position', 'access'], 'integer'],
            [['visible'], 'boolean'],
            [['title', 'alias'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 200],
            [['page_id'], 'safe']
        ];
    }

    public function getParent() {
        return $this->hasOne(MenuItem::className(), ['id' => 'parent_id']);
    }

    public function getPage() {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }

    public function getRedirect() {
        return $this->hasOne(Redirect::className(), ['menu_item_id' => 'id'])
                ->where(['generated' => 1]);
    }

    public function getChildren() {
        return $this->hasMany(MenuItem::className(), ['parent_id' => 'id']);
    }

    public function afterFind() {
        if (in_array($this->type, ['cms', 'empty'])) {
            $this->url = '';
        }
    }

    public function beforeValidate() {
        if ($this->alias == null) {
            $this->alias = $this->title;
        }

        $this->alias = Usm::makeAlias($this->alias);

        if ($this->type == 'cms') {
            $this->url = '-';
            $this->access = 2;

            if ($this->page_id == null) {
                $new_page = new Page();
                $new_page->title = $this->title;
                $new_page->access = 2;
                $new_page->save();
                $this->page_id = $new_page->id;
            }
        } elseif ($this->type == 'empty') {
            $this->url = '-';
            $this->page_id = null;
        } else {
            $this->page_id = null;
        }

        return true;
    }

    public function afterValidate() {
        if ($this->isNewRecord) {
            $highestPosition = MenuItem::find()
                ->select('position')
                ->where('parent_id IS NULL')
                ->orderBy('position DESC')
                ->one();

            $this->position = ($highestPosition == null) ? 1 : $highestPosition->position + 1;
        }
    }

    public function afterSave($insert, $changedAttributes) {
        $this->updateRedirect($this);
    }

    private function updateRedirect(&$item) {
        $r = $item->redirect;
        if ($item->type == 'empty') {
            if ($r != null) {
                $r->delete();
            }
        } else {
            if ($r == null) {
                $r = new Redirect();
                $r->type = 'menu_item';
                $r->menu_item_id = $item->id;
            }
            $r->active = 1;
            $r->forward = 0;
            $r->generated = 1;
            $r->url = ($item->parent_id != null) ? $item->parent->alias . "/" . $item->alias : $item->alias;
            $postfix = 1;
            while (!$r->save() && count($r->getErrors('url')) > 0) {
                $r->url = (($postfix == 1) ? $r->url : substr($r->url, 0, -1 * (strlen($postfix) + 1))) . '-' . ($postfix++);
            }
        }

        if (count($item->children) > 0) {
            foreach ($item->children as $c) {
                $this->updateRedirect($c);
            }
        }
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'visible' => 'Visible',
            'position' => 'Position',
            'alias' => 'Url alias',
            'title' => 'Menu item title',
            'type' => 'Item type',
            'page_id' => 'Page',
            'url' => 'Url',
            'parent_id' => 'Parent',
        ];
    }

}
