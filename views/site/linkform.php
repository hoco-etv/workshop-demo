<?php
use yii\helpers\Url;
?>
<h1>Hoi</h1>

<form method="post" action="<?= Url::to(['/site/link']) ?>" >
    <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>"
           value="<?=Yii::$app->request->csrfToken?>"/>
    <div class="input-group">
        <input name="link" type="text" class="form-control" placeholder="link" aria-describedby="basic-addon1">
    </div>
</form>