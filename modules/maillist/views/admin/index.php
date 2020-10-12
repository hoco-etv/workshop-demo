<?php

/* @var $this yii\web\View */

use app\models\User;
use app\modules\maillist\assets\MaillistAsset;
use app\modules\maillist\models\Maillist_member;
use yii\widgets\Pjax;

$this->title = 'Klushok';
$this->params['documentation'] = $this->render('_docs');

MaillistAsset::register($this);

?>



<div class="maillist-index">

    <div id="user-display-container" class="collapse">
        <h1>Users</h1>
        <h4>Click to add or remove users from maillists: <span id="maillist-name"></span></h4>
        <?php foreach ($users as $user) : ?>
            <div id=<?= $user['id'] ?> class="panel panel-default user-display-panel" onclick="userClicked(this)">
                <div class="panel-body">
                    <div class="panel-picture">
                        <p class="glyphicon glyphicon-user"></p>
                    </div>
                    <div class="panel-description">
                        <table>
                            <tr>
                                <th style="padding-right: 3px;">Name: </th>
                                <td class="user-name"><?= $user['first_name'] . ' ' . $user['last_name'] ?></td>
                            </tr>
                            <tr>
                                <th style="padding-right: 3px;">Email: </th>
                                <td><?= $user['email'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div style="margin-top: 2em">
            <a id="user-save-changes-btn" class="btn btn-success" style="display: none" onclick="saveBtnClicked()">Save changes</a>
            <a id="user-cancel-changes-btn" class="btn btn-link" style="display: none" data-toggle="collapse" data-target="#user-display-container">Cancel</a>
        </div>
    </div>


    <?php Pjax::begin(['id' => 'maillist-pjax']) ?>
    <div class="maillist-container">
        <h1>Mail lists</h1>
        <?php foreach ($maillists as $maillist) : ?>

            <div id=<?= $maillist['id'] ?> class="panel panel-default maillist-display-panel">
                <div class="panel-heading">
                    <div class="maillist-panel-heading">
                        <p class="maillist-name"><?= $maillist['name'] ?></p>
                        <span class="glyphicon glyphicon-pencil maillist-panel-edit-button" onclick="editBtnClicked(this)" data-toggle="tooltip" title="Edit"></span>
                    </div>
                    <div class="maillist-info-field">
                        <?= $maillist['info'] ?>
                    </div>
                </div>
                <div class="panel-body">
                    <?php
                    $list_members = Maillist_member::findAll(['list_id' => $maillist['id']]);
                    // $list_members = Maillist_member::find()->where(['list_id' => $maillist['id']])->all();

                    if (empty($list_members)) : ?>
                        <span>There are no users subscribed to this list</span>
                    <?php
                    endif;
                    foreach ($list_members as $member) :
                        $user = User::findIdentity($member['user_id']);
                    ?>
                        <div id=<?= $user->id ?> class="maillist-member" style="margin-bottom: 1.5em">
                            <?= $user->first_name . ' ' . $user->last_name . "<br>" ?>
                            <?= '(' . ($user->email ? $user->email : 'email not set') . ')' ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php Pjax::end() ?>
</div>


<script>
    var selected_list_id;

    function editBtnClicked(e) {
        var maillistName = e.parentElement.parentElement.firstElementChild.innerHTML;
        selected_list_id = e.parentElement.parentElement.parentElement.id;
        var maillistPanel = e.parentElement.parentElement.parentElement;
        var maillistMembers = maillistPanel.getElementsByClassName('maillist-member');
        document.getElementById('maillist-name').innerHTML = maillistName;


        highlightReset(); // reset style attributes
        highlightMembers(maillistMembers);


        $('.collapse').collapse("show")

        // show cancel button
        document.getElementById('user-cancel-changes-btn').style.display = 'unset';
    }

    function highlightMembers(members) {
        Array.prototype.forEach.call(members, function(member) {
            document.getElementById(member.id).style.boxShadow = "inset 0px 0px 0px 2px green";
        });
    }

    function highlightReset(member = null) {

        if (member != null) {
            var users = member
        } else {
            var users = document.getElementsByClassName('user-display-panel');
        }
        Array.prototype.forEach.call(users, function(user) {
            user.removeAttribute("style");
        });
    }

    function userClicked(e) {
        // console.log(e);
        var id = e.id;
        if (e.getAttribute('style') != null) {
            highlightReset([e]);
        } else {
            highlightMembers([e]);
        }

        // show save and cancel button
        document.getElementById('user-save-changes-btn').style.display = 'unset';
        document.getElementById('user-cancel-changes-btn').style.display = 'unset';
    }



    function saveBtnClicked() {

        var elements = document.getElementsByClassName('user-display-panel');
        var selected = [];
        Array.prototype.forEach.call(elements, function(element) {
            if (element.style.boxShadow) {
                selected.push(element.id);
            }
        });

        // var jsonString = JSON.stringify(selected);
        // console.log(jsonString)

        $.ajax({
            url: 'update_members',
            type: 'post',
            data: {
                'list_id': selected_list_id,
                'members': selected,
            },
            success: $.pjax.reload({
                url: 'index',
                container: '#maillist-pjax'
            })
        });
    }
</script>