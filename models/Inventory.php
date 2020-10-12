<?php

namespace app\models;

use Yii;
use yii\base\Component;
use yii\base\Model;

class Inventory extends \yii\db\ActiveRecord
{
    public $additionalNotes;

    public function rules()
    {
        return [
            [['category', 'name', 'price'], 'required', 'on' => 'new'],
            ['price', 'double', 'min' => 0],
        ];
    }

    public static function tableName()
    {
        return 'inventory';
    }

    public static function getCategories() //returns a list of unique categories from the db
    {
        $categories = array();
        $categories_request = Inventory::find()->select(['category'])->distinct()->all();
        foreach ($categories_request as $category) {
            array_push($categories, $category['category']);
        }
        return $categories;
    }

    /**
     * Finds distict names
     *
     * @param string $category
     * category in which the names should fall
     *  
     * @return array
     * returns array of distinct names, empty if none are found
     */
    public static function getNames($category = null)
    {
        $names = array();
        if ($category) {
            $names_request = Inventory::find()->select(['name'])->where(['category' => $category])->distinct()->all();
        } else {
            $names_request = Inventory::find()->select(['name'])->distinct()->all();
        }
        foreach ($names_request as $name) {
            array_push($names, $name['name']);
        }
        return $names;
    }

    /**
     * Finds distict info
     *
     * @param string $category
     * category in which the info should fall
     * 
     * @param string $name
     * Name of the item you want the distict info from
     * 
     * @return array
     * returns array of distinct names, empty if none are found
     */
    public static function getInfo($category, $name)
    {
        $infos = array();

        $infos_request = Inventory::find()
            ->select(['info'])
            ->where(['category' => $category, 'name' => $name])
            ->distinct()
            ->all();

        foreach ($infos_request as $info) {
            if (!empty($info['info'])) {
                array_push($infos, $info['info']);
            }
        }
        return $infos;
    }


    public function addComponent($category, $name, $info, $price, $stock = 0)
    {
        // $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        // $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        // $info = filter_input(INPUT_POST, 'info', FILTER_SANITIZE_SPECIAL_CHARS);
        // $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_SPECIAL_CHARS);
        // afvangen in rules
        if (empty($category)) {
            echo "Please select a category";
            die();
        }
        if (empty($name)) {
            echo "Please enter a name";
            die();
        }
        if (empty($price)) {
            echo "Please enter a price";
            die();
        }

        $component = new Inventory();
        $component->category =  $category;
        $component->name =      $name;
        $component->info =      $info;
        $component->price =     $price;
        $component->stock =     $stock;
        $component->save();
    }

    public function getItemsByCategory($category = '*')
    {
        $items = Inventory::find()
            ->where(['category' => $category])
            ->all();
        return [$items, count($items)];
    }

    public function getItemsByName($name)
    {
        $items = Inventory::find()
            ->where(['name' => $name])
            ->all();
        return [$items, count($items)];
    }

    public function getStockStatus($stock) //return stock status and message
    {
        //$stock contains the amount of not-available notifications
        $message = "Stock unknown";
        $status = "unknown";

        //if the stock is null or negative, it will be unknown
        if ($stock == 0) {
            $message = "Available";
            $status = "available";
        } else if ($stock > 0 && $stock < 3) {
            $message = "Limited";
            $status = "limited";
        } else if ($stock >= 3) {
            $message = "Sold out";
            $status = "sold-out";
        }

        return ['status' => $status, 'message' => $message];
    }

    public function setStock($id, $value)
    {
        $item = Inventory::find()
            ->where(['id' => $id])
            ->one();
        $item->stock = $value;
        $item->save();
    }

    public function setPrice($id, $value)
    {
        $item = Inventory::find()
            ->where(['id' => $id])
            ->one();
        $item->price = $value;
        $item->save();
    }

    public function isInDb($category, $name, $info)
    {
        $component = Inventory::find()
            ->where([
                'category' => $category,
                'name' => $name,
                'info' => $info
            ])
            ->one();
        //return $component;
        if ($component != null) {
            return true;
        } else return false;
    }

    public function deleteComponentById($id)
    {
        $component =  Inventory::find()->where(['id' => $id])->one();
        $component->delete();
        return $component;
    }

    public function getCategoryOptions() //translate unique categories to <option> HTML
    {
        $options = "";
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $options .= "<option value =" . $category . ">" . $category . "</option>";
        }
        return $options;
    }

    public function getPricelist($showStatus)
    {
        $number_of_components = count(Inventory::find()->all());
        $component_index = 0;

        $isRightColumn = false;
        $pricelist = "<div class='column left'>";
        $categories = $this->getCategories(); //returns an array of unique categories

        foreach ($categories as $category) {
            $itemHTML = $this->getItemHTML($category, $showStatus);
            $component_index += $itemHTML[1];
            $pricelist .=  "<div class='category'>" .
                "<span class='category header'>${category}</span>" .
                $itemHTML[0] .
                "</div>";
            if ((2 * $component_index >= $number_of_components) && !$isRightColumn) {
                $isRightColumn = true;
                $pricelist .= "</div><div class='column right'>";
            }
        }
        $pricelist .= "</div>";
        return $pricelist;
    }


    private function getItemHTML($category, $showStatus)
    {
        $items = $this->getItemsByCategory($category)[0];

        $itemHTML = "";                               //initialize string object
        foreach ($items as $item) {
            $stock = $this->getStockStatus($item['stock']);

            $itemHTML .=
                "<div class='item-container' id='" . $item['id'] . "'>" .
                "<span class='item name'>" . $item['name'] . "</span>" .
                "<span class='item code'>" . $item['info'] . "</span>" .
                "<span class='item price'>&euro; " . money_format('%.2n', $item['price']) . "</span>";

            if ($showStatus) {
                $itemHTML .= "<div class='item dot " . $stock['status'] . "'>" .
                    "<span class='tooltiptext'>" . $stock['message'] . "</span>" .
                    "</div>";
            }
            $itemHTML .=    "</div>";
        }
        return [$itemHTML, count($items)];
    }
}
