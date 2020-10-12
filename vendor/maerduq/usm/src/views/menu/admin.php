<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->params['pageHeader'] = 'Menu';
$this->params['breadcrumbs'] = array(
    ['label' => 'Admin Panel', 'url' => ['/usm']],
    'Menu'
);

$this->params['documentation'] = $this->render('_docs');
?>


<div class="btn-toolbar">
    <?= Html::a('New menu item', ['create'], ['class' => 'btn btn-primary']); ?>
    <button type="button" id="sortbutton" class="btn btn-default">Sort mode is off. Click to turn on</button>
</div>
<hr />

<?php if (count($items) == 0): ?>
    <p><i>There are no menu items.</i></p>
<?php endif; ?>

<ul class="menuitemlist first">
    <?php
    $inSubitems = false;
    $first = true;
    foreach ($items as $data) {
        if ($first) {
            $first = false;
            echo "<li data-id='{$data->id}'>";
        } elseif ($data->parent_id != null && !$inSubitems) { //als submenuitem en nog geen submenuitem gehad
            $inSubitems = true;
            echo "<ul class='menuitemlist'><li data-id='{$data->id}'>";
        } elseif ($data->parent_id == null && $inSubitems) { //als geen submenuitem maar wel submenuitem gehad
            $inSubitems = false;
            echo "</ul></li><li data-id='{$data->id}'>";
        } elseif ($data->parent_id == null && !$inSubitems) { //als geen submenuitem en geen submenuitem gehad
            echo "<ul class='menuitemlist'></ul></li><li data-id='{$data->id}'>";
        } else {  //als submenuitem en submenuitem gehad
            echo "<ul class='menuitemlist'></ul></li><li data-id='{$data->id}'>";
        }

        echo $this->render('_list_item', ['data' => $data, 'inSubitems' => &$inSubitems, 'sudo' => $sudo]);
    }

    if (!$inSubitems) {
        echo "<ul class='menuitemlist'></ul>";
    }
    echo "</li>";
    ?>
</ul>

<script type="text/javascript">
    $("#sortbutton").click(sortit);
    draggingitem = null;

    sortingit = false;
    function sortit() {
        if (sortingit) {
            $("#sortbutton").text("Sort mode is off. Click to turn on").removeClass('btn-success').addClass('btn-default');
            $(".menuitemlist a").removeClass('disabled');
            $(".menuitemlist.first").removeClass('sortactive');

            $(".menuitemlist").sortable("destroy");
        } else {
            $("#sortbutton").text("Sort mode is on! Click to turn off again").removeClass('btn-default').addClass('btn-success');
            $(".menuitemlist a").addClass('disabled');
            $(".menuitemlist.first").addClass('sortactive');

            $(".menuitemlist").sortable({
                placeholder: "media media-highlight",
                distance: 15,
                connectWith: ".menuitemlist",
                scroll: false,
                start: function (event, ui) {
                    nu = ui.item;
                    nu.attr('data-old-parent', parseInt(nu.parent().parent().attr('data-id')));
                    nu.attr('data-old-order', nu.index() + 1);
                },
                stop: function (event, ui) {
                    nu = ui.item;
                    console.log(nu);

                    newspot = {
                        id: parseInt(nu.attr('data-id')),
                        parent: parseInt(nu.parent().parent().attr('data-id')),
                        oldParent: nu.attr('data-old-parent'),
                        order: nu.index() + 1,
                        oldOrder: nu.attr('data-old-order')
                    };

                    if (nu.find('ul').children().length > 0 && newspot.parent > 0) {
                        $(".menuitemlist").sortable('cancel');
                    } else {
                        newspot = {
                            id: parseInt(nu.attr('data-id')),
                            parent: parseInt(nu.parent().parent().attr('data-id')),
                            oldParent: nu.attr('data-old-parent'),
                            order: nu.index() + 1,
                            oldOrder: nu.attr('data-old-order')
                        };

                        nu.removeAttr('data-old-parent');
                        nu.removeAttr('data-old-order');

                        if (newspot.oldParent !== newspot.parent || newspot.oldOrder !== newspot.order) {
                            $.ajax({
                                type: 'POST',
                                url: '<?= Url::to('ajaxposition'); ?>',
                                data: {newspot: newspot},
                                success: function (data) {
                                    console.log(data);
                                }
                            });
                        }
                    }
                }
            }).disableSelection();
        }
        sortingit = !sortingit;
    }


    $(".lbl-visible").click(function () {
        dit = $(this);
        if (dit.hasClass('label-success')) { //als visible
            dit.removeClass('label-success').addClass('label-default').text('Hidden');
            state = 0;
        } else {
            dit.removeClass('label-default').addClass('label-success').text('Visible');
            state = 1;
        }

        $.ajax({
            type: "POST",
            dataType: "html",
            url: '<?= Url::to('ajaxvisibility'); ?>',
            data: {
                item: dit.closest('li').attr('data-id'),
                state: state
            },
            success: function (data) {
                if (data == '1') {
                    dit.notify('New state saved', 'success');
                } else {
                    dit.notify('Something went wrong', 'error');
                }
            }
        });
    });

    $(".lbl-access").click(function () {
        dit = $(this);
        if (dit.hasClass('label-success')) { //als visible
            dit.removeClass('label-success').addClass('label-danger').text('Administrators');
            state = 2;
        } else if (dit.hasClass('label-danger')) {
            dit.removeClass('label-danger').addClass('label-warning').text('Logged in users');
            state = 1;
        } else if (dit.hasClass('label-warning')) {
            dit.removeClass('label-warning').addClass('label-success').text('All visitors');
            state = 0;
        }

        $.ajax({
            type: "POST",
            dataType: "html",
            url: '<?= Url::to('ajaxaccess'); ?>',
            data: {
                item: dit.closest('li').attr('data-id'),
                state: state
            },
            success: function (data) {
                if (data == '1') {
                    dit.notify('New access rights saved', 'success');
                } else {
                    dit.notify('Something went wrong', 'error');
                }
            }
        });
    });
</script>

<style type="text/css">
    .lbl-visible, .lbl-access {
        cursor: pointer;
    }

    .sortactive .media:hover {
        background:#eee;
        cursor: pointer;
    }
    .sortactive.menuitemlist, .sortactive.menuitemlist.first > li > .menuitemlist {
        padding-bottom: 15px;
    }
    .sortactive.menuitemlist.first > li > .menuitemlist {
        min-height: 40px;
    }

    .menuitemlist {
        list-style: none;
    }

    .menuitemlist.first {
        margin-right:20px;
    }

    .menuitemlist li {
        margin-top:8px;
    }
    .menuitemlist li li {
        margin-right:-20px;
    }

    .menuitemlist li li .media {
        background-color:#f5f5f5;
    }

    .media {
        /*margin: 5px 0px;line-height:40px;padding:0px 10px; */
        border:1px solid #ccc;
        border-radius: 0px;
        padding: 2px 10px;
        line-height:40px;
        background:#f0f0f0;
    }

    .media-highlight {
        background: #eee;
        height:30px;
    }

    .media a h4{
        color:#333;
    }


    .media .label {
        display:inline-block;
        width:90px;
        margin-bottom:5px;
        margin-right:5px;
    }

    .media h4 {
        margin-top:0px;
        display:inline;
    }
</style>