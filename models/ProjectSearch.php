<?php

namespace app\models;

use Yii;
use yii\base\Model;

// use app\models\Project;
use yii\data\ActiveDataProvider;

class ProjectSearch extends Project
{
    //https://www.yiiframework.com/wiki/653/displaying-sorting-and-filtering-model-relations-on-a-gridview
    public $id, $title, $author, $email, $created_at;


    public function rules()
    {
        return [
            [['id', 'title', 'author', 'email', 'created_at'], 'safe'],
        ];
    }

    public function search($params, $query = null)
    {
        if (!$query) {
            $query = Project::find();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'created_at', $this->created_at]);


        return $dataProvider;
    }
}
