<?php

use maerduq\usm\assets\TinymceAsset;
use maerduq\usm\components\Usm;
use yii\helpers\Html;

TinymceAsset::register($this);

$this->params['pageHeader'] = "<h1>" . $page->title . "</h1>";
?>
<div class='alert'>
    <p>You are now in editing mode of this normal page. After you edited the page, you can click save.</p>

    <p><?= Html::button('Save', ['id' => 'save-button']); ?>&nbsp;&nbsp;&nbsp;
        <?= Html::a('Edit page properties', ['pages/update', 'id' => $page->id, 'return' => $return]); ?>&nbsp;&nbsp;&nbsp;
        <?= Html::a('Go back', Usm::returnUrl($return, ['pages/update', 'id' => $page->id])); ?></p>
</div>
<?php if (count($languages) > 1): ?>
    <div>
        <ul class='nav nav-pills'>
            <?php foreach ($languages as $l): ?>
                <li class='<?php if ($l == $edit_lang): ?>active<?php endif; ?>'><?= Html::a($l, ['editpage', 'id' => $page->id, 'edit_lang' => $l]); ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
<div class="tiny-editable" id="thecontent">
    <?= $page->content ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        tinymce.init({
            selector: ".tiny-editable",
            inline: true,
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            toolbar: "insertfile undo redo | fontsizeselect | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
            convert_urls: false,
            visual_table_class: 'table',
            style_formats: [
                {title: "Headers", items: [
                        {title: "Header 1", format: "h1"},
                        {title: "Header 2", format: "h2"},
                        {title: "Header 3", format: "h3"},
                        {title: "Header 4", format: "h4"},
                        {title: "Header 5", format: "h5"},
                        {title: "Header 6", format: "h6"}
                    ]},
                {title: "Inline", items: [
                        {title: "Bold", icon: "bold", format: "bold"},
                        {title: "Italic", icon: "italic", format: "italic"},
                        {title: "Underline", icon: "underline", format: "underline"},
                        {title: "Strikethrough", icon: "strikethrough", format: "strikethrough"},
                        {title: "Superscript", icon: "superscript", format: "superscript"},
                        {title: "Subscript", icon: "subscript", format: "subscript"},
                        {title: "Code", icon: "code", format: "code"}
                    ]},
                {title: "Blocks", items: [
                        {title: "Paragraph", format: "p"},
                        {title: "Lead Paragraph", block: "p", classes: "lead"},
                        {title: "Blockquote", format: "blockquote"},
                        {title: "Div", format: "div"},
                        {title: "Pre", format: "pre"}
                    ]},
                {title: "Alignment", items: [
                        {title: "Left", icon: "alignleft", format: "alignleft"},
                        {title: "Center", icon: "aligncenter", format: "aligncenter"},
                        {title: "Right", icon: "alignright", format: "alignright"},
                        {title: "Justify", icon: "alignjustify", format: "alignjustify"}
                    ]},
                {title: "Table", items: [
                        {title: "Striped", classes: "table-striped", selector: "table"}
                    ]}
            ]

        });

        $("a").click(function () {
            e.preventDefault();
        });

        $("#save-button").click(function () {
            form = {
                thecontent: $("#thecontent").html(),
                '_csrf': '<?= Yii::$app->request->csrfToken ?>'
            };
            fakeform(form);
        });
    });


    function fakeform(data) {
        var form = $(document.createElement("form"));
        form.attr("method", 'post');

        for (i in data) {
            form.append($(document.createElement("input")).attr('type', 'hidden').attr('name', i).attr('value', data[i]));
        }
        $('body').append(form);
        form.submit();
    }
</script>
<style>
    .tiny-editable {
        border: 1px dashed black;
        padding:3px;
        padding-bottom:30px;
        border-radius:2px;
        margin-top:70px;
    }
    .alert {
        color: #31708f;
        background-color: #d9edf7;
        border-color: #bce8f1;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
</style>