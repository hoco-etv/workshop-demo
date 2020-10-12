<?php
use maerduq\usm\models\Textblock;

/* @var $this yii\web\View */

$this->title = 'Klushok';
$device->scenario = 'report';


?>
<div class="devices-report">
  <h2>Report device: <?=$device->brand . " " . $device->name?></h2>

<?=Textblock::read('Device_report_top')?>

<div class="container" style='margin-bottom:20px'>
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <img src='<?=$device->image?>' style='max-width:100%'>
        </div>
        <div class="col"></div>
   </div>
</div>

<?php
if($device->status == 99){
    echo "<div class='alert alert-danger' style='text-align:center'><span style='font-size:14pt'>This device has been decommissioned, <br>you can no longer report issues</span></div>";
} else{
    echo $this->render('_reportForm', ['device' => $device]);
}
?>
</div>
