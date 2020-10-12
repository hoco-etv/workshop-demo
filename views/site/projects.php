<?php

/* @var $this yii\web\View */


use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use maerduq\usm\models\Textblock;


$this->title = 'Klushok';
$this->registerCssFile('css/project.css');
?>
<div class="site-projects">
  <?php
  foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    if ($key == 'error') {
      $key = 'danger';
    }
    echo '<div class="alert alert-' . $key . '">' . $message . "</div>\n";
  }
  ?>

  <h1>Projects</h1>
  <div id='projects-info' style=<?= isset(Yii::$app->request->queryParams['ProjectSearch']) ? "display:none" : '' ?>>
    <?= Textblock::read('projects_intro') ?>
  </div>


  <div class="project-btn-container">

    <a class="project-btn" onclick="$('#projects-info').slideToggle()">
      <span class="project-btn-icon project-btn-text" style="line-height: unset">?</span>
      <br>
      <span class="project-btn-name">Help</span>
    </a>

    <a class="project-btn" href="/projects/upload">
      <span class="glyphicon glyphicon-pencil project-btn-icon" style="line-height: unset;"></span>
      <br>
      <span class="project-btn-name">Create project</span>
    </a>

    <a id='search-btn' class="project-btn" onclick="$('#project-search-panel').slideToggle()">
      <span class="glyphicon glyphicon-search project-btn-icon" style="line-height: unset;"></span>
      <br>
      <span class="project-btn-name">Search project</span>
    </a>

  </div>


  <div class='project-search-container'>
    <div class="panel panel-default" id="project-search-panel" style="display:none">
      <span class="glyphicon glyphicon-search project-search"></span>
      <h3 style="text-align:center">Search projects</h3>
      <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
          'layout' => 'horizontal',
          'id' => 'project-search-form',
          'method' => 'get',
          'action' => ['/projects']
        ]);
        ?>

        <?= $form->field($searchModel, 'title')->label('Project title')->input('text', ['placeholder' => 'Search...']); ?>
        <?= $form->field($searchModel, 'author')->label('Author')->input('text', ['placeholder' => 'Search...']); ?>

        <div style="text-align:center">
          <input type="submit" class="btn btn-success" value="Search">
          <a class="btn btn-link" onclick="$('#project-search-panel').slideUp()">Back</span></a>
        </div>

        <?php
        ActiveForm::end();
        ?>
      </div>
    </div>

    <?php

    if (isset(Yii::$app->request->queryParams['ProjectSearch'])) {
      $params = Yii::$app->request->queryParams['ProjectSearch'];

      $filterTagsHtml = '';
      foreach ($params as $key => $value) {

        if ($value) {
          $paramLink = $params;
          $paramLink[$key] = '';
          $filterTagsHtml .=
            "<div class='label label-info' style='font-size:small; margin-right:10px'>" .
            "<span>" . ucfirst($key) . ": $value</span>" .
            "<a href='projects?" .
            http_build_query(['ProjectSearch' => $paramLink])
            . "' style='color:#fff; margin-left:15px; text-decoration:none'>x</a></div>";
        }
      }
      if (!empty($filterTagsHtml)) {
        echo '<h4>Filters:</h4>';
      }
      echo $filterTagsHtml;
    }
    ?>


  </div>

  <div class='project_container'>
    <?php

    if (!$projects) {
      echo Html::img('/img/project/no_projects_found.jpg', ['style' => ['width' => '90%', 'margin-top' => '40px', 'border-radius' => '10px']]);
    } else {
      foreach ($projects as $project) {
        echo $project->overviewHTML();
      }
    }
    ?>

  </div>

  <div style="text-align:center">
    <?php
    echo LinkPager::widget([
      'pagination' => $pages,
    ]);
    ?>
  </div>

</div>