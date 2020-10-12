<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <style type="text/css">
        .footer {
            display: block;
            font-style: italic;
        }
    </style>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <div class="footer">
        <p>
            This email has been generated automatically, you are unable to respond to this email address.<br>
            If you have any questions, please contact <a href = "mailto: klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a>
        </p>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>