<?php

namespace app\models;

use app\validators\Tumail;
use yii\db\ActiveRecord;
use Yii;

class Project extends ActiveRecord
{
    public $cover, $cover_name;
    public $file1, $file2, $file3, $file4, $file5;

    public function rules()
    {
        return [
            [['title', 'author', 'content', 'email'], 'required'],
            [['cover'], 'required', 'on' => 'new'],
            [['content'], 'string', 'max' => 65000],
            // [['title'], 'unique'],
            [['title', 'author'], 'string', 'max' => 40],
            [['subtitle'], 'string', 'max' => 140],
            [['email'], 'email'],
            [['email'], Tumail::className()],
            [['cover'], 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 10 * 1024 * 1024, 'on' => 'new'],
            [['file1', 'file2', 'file3', 'file4', 'file5'], 'file', 'maxSize' => 10 * 1024 * 1024],
            [['cover_type', 'file1_type', 'file2_type', 'file3_type', 'file4_type', 'file5_type'], 'string', 'max' => 40, 'skipOnEmpty' => true],
            [['file1_name', 'file2_name', 'file3_name', 'file4_name', 'file5_name'], 'string', 'max' => 40, 'on' => 'edit']
        ];
    }

    public static function tableName()
    {
        /*
        [id]            [int]               []          id
        [title]         [varchar 40]        []          project title
        [subtitle]      [varchar 140]       [null]      140 character summary
        [author]        [varchar 40]        []
        [email]         [text]              []          email of the author
        [created]       [datetime]          []          date of creation
        [confirmed]     [tinyint 1]         [false]     bool email confirmation
        [approved]      [tinyint 1]         [false]     bool approved by committee
        [hash]          [text]              []          hash to confirm and edit project
        [content]       [text]              []          content of the table
         */
        return 'projects';
    }


    public function beforeSave($insert)
    {

        // If a db error rises indicating the file type is too long, increase the allocated space in the db to accomodate this.
        // the max mime size is 255
        // for this application the likeliness of filetypes beyond this length is small so the length has set at a lower value

        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->scenario == 'edit' || $this->scenario == 'new') {

            $filenames = ['cover', 'file1', 'file2', 'file3', 'file4', 'file5'];

            foreach ($filenames as $file) {
                if ($this->$file) { // check if file is uploaded
                    $this->{$file . '_type'} = $this->$file->type;
                    $this->{$file . '_ext'} = $this->$file->extension;
                    $this->{$file . '_name'} = $this->$file->basename;
                }
                if(strlen($this->{$file . '_name'})>40){
                    $this->{$file . '_name'} = substr($this->{$file . '_name'},0,39);
                }
            }
        }
        return true;
    }

    public function __construct()
    {
        $this->createUniqueHash();
    }

    public function createUniqueHash()
    {
        // create new hash until it is a unique one
        do {
            $this->hash = md5(uniqid(rand(), true));
        } while (
            Project::findAll(['hash' => $this->hash])
        );
    }

    public function overviewHTML()
    {
        return
            "<div class='project'>" .
            "<a class='image' href='/projects/view?id=" . $this->id . "'>" .
            "<img src='/projects/image?id=" . $this->id . "&doc=cover' class='image'>" .
            "<div class='info'>" .
            "<h4>" . $this->title . "</h4>" .
            "<p>" . $this->subtitle . "</p>" .
            "</div>" .
            "<div class='details'>" .
            "<p>Created by " . $this->author . "</p>" .
            "</div>" .
            "</a>" .
            "</div>";
    }

    public function show_content($isAuthor)
    {
        $pattern = array("/(?i){{cover}}/", "/(?i){{file1}}/", '/(?i){{file2}}/', '/(?i){{file3}}/', '/(?i){{file4}}/', '/(?i){{file5}}/');
        $isAuthor ? $allow_img = '&h=' . $this->hash : $allow_img = '';
        $replacement = array(
            "![](/projects/image?id=" . $this->id . "&doc=cover" . $allow_img . "){.image}",
            "![](/projects/image?id=" . $this->id . "&doc=file1" . $allow_img . "){.image}",
            "![](/projects/image?id=" . $this->id . "&doc=file2" . $allow_img . "){.image}",
            "![](/projects/image?id=" . $this->id . "&doc=file3" . $allow_img . "){.image}",
            "![](/projects/image?id=" . $this->id . "&doc=file4" . $allow_img . "){.image}",
            "![](/projects/image?id=" . $this->id . "&doc=file5" . $allow_img . "){.image}",
        );
        $new_cont = preg_replace($pattern, $replacement, $this->content);

        $fileHTML = "<br><br><ul class='downloads tr'>";
        for ($i = 1; $i <= 5; $i++) {
            if ($this->{'file' . $i . '_type'}) {
                $fileHTML .= "<li class='file'>" .
                    "<div class='filename'><span>" . $this->{"file" . $i . '_name'} . "." . $this->{'file' . $i . '_ext'} . "</span></div>" .
                    "<div class='d-btn'><a href = '/projects/image?id=" . $this->id . "&doc=file" . $i . "'>Download</a></div>" .
                    "</li>";
            }
        }
        $fileHTML .= "</ul>";

        $new_cont .= $fileHTML;
        return $new_cont;
    }

    public function show_cover($isAuthor)
    {
        $isAuthor ? $allow_img = '&h=' . $this->hash : $allow_img = '';
        return "<img src='/projects/image?id=" . $this->id . "&doc=cover" . $allow_img . "' class='cover'><br>";
    }

    public function send_project_approved_mail()
    {
        Yii::$app->mailer->compose(
            [
                'html' => 'project_approved/html',
                'text' => 'project_approved/text',
            ],
            [
                'project' => $this,
            ]
        )
            ->setFrom('noreply-etv@tudelft.nl')
            ->setTo($this->email)
            ->setSubject('Klushok project approved')
            ->send();
    }
}
