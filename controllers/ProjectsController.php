<?php

namespace app\controllers;

use app\models\Project;
use app\models\ProjectSearch;
use app\modules\maillist\models\Maillist;
use maerduq\usm\models\Redirect;
use Yii;
use yii\debug\models\search\Mail;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ProjectsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'place' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['upload', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['upload'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    // TODO 
    // Textfile naar projecten exporteren (of pdf en die laten downloaden)

    public function actionView()
    {
        $isAdmin = !Yii::$app->user->isGuest;
        $isAuthor = false;

        if (!isset($_GET['id'])) {                    // id not set, redirect back to projects
            return $this->redirect('/site/projects');
        } elseif ($isAdmin && !isset($_GET['h'])) {     // admin views project, hash not provided
            $model = Project::findOne($_GET['id']);
            if ($model->approved) {
                return $this->render('view', ['model' => $model, 'isAuthor' => false]);
            }
            Yii::$app->session->setFlash('warning', 'Since you are an admin, you have been redirected to an editable link.');
            return $this->redirect(['view', 'id' => $model->id, 'h' => $model->hash]);
        } elseif (isset($_GET['id']) && isset($_GET['h'])) {            // author or admin views project, hash provided
            $isAuthor = true;
            $id = $_GET['id'];
            $hash = $_GET['h'];
            $model = Project::findOne(['id' => $id, 'hash' => $hash]);
        } else {
            $model = Project::findOne($_GET['id']);
        }


        if (Yii::$app->request->isPost) {
            return $this->actionEdit();
        }

        if ($model == null) {                                   // ID does not match a project
            throw new HttpException(404, 'File not found');
        } elseif (!$model->approved) {                          // Model is not yet approved
            if ($isAuthor) {
                if ($isAdmin) {                                     // User is logged in (admin)
                    $adminView['table'] = "<br><br><br>
                        <div style='background-color:#ffffff;'>
                        <table class='table table-striped table-bordered table-hover'>
                            <thead>
                                <tr>
                                    <th colspan='2'>Project Details</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>Project ID</td>
                                <td>$model->id</td>
                            </tr>
                            <tr>
                                <td>Title</td>
                                <td>$model->title</td>
                            </tr>
                            <tr>
                                <td>Subtitle</td>
                                <td>$model->subtitle</td>
                            </tr>
                            <tr>
                                <td>Author</td>
                                <td>$model->author</td>
                            </tr>
                            <tr>
                                <td>Author email</td>
                                <td>$model->email</td>
                            </tr>
                            <tr>
                                <td>Created at</td>
                                <td>$model->created_at</td>
                            </tr>
                            <tr>
                                <td>Reviewer</td>
                                <td>$model->reviewer</td>
                            </tr>
                            <tr>
                                <td>Confirmed</td>
                                <td>" . ($model->confirmed ? "yes" : "no") . "</td>
                            </tr>" . ($model->approved
                        ?
                        "<tr>
                                <td>Approved at</td>
                                <td>$model->approved_at</td>
                            </tr>"
                        : "<tr>
                                <td>Approved</td>
                                <td>no</td>
                            </tr>") .
                        "<tr>
                                <td>Hash</td>
                                <td>$model->hash</td>
                            </tr>
                        </table>
                        </div>
                        <form id='project-info' method='post'>
                        <input type='hidden' name='_csrf' value=" . Yii::$app->request->getCsrfToken() . "}' />
                        <input type='hidden' name=id value=$model->id>" .
                        "<input type='hidden' name='hash' value=$model->hash>" .
                        "<input type='submit' name='action' value='Edit' class='btn btn-primary' style='width:90px;margin-right:20px' data-toggle='tooltip' title='Edit this project'>
                        <button  type='button' onclick=\"location.href='mailto:$model->email'\" class='btn btn-primary' data-toggle='tooltip' data-original-title='Email the author of this project to provide info about required changes before the project will be approved' style='margin-right:20px'>Email author</button>
                        <input type='submit' name='action' value='Approve' label='Approve' class='btn btn-success' style='width:90px;margin-right:20px' data-toggle='tooltip' title='Approve this project'>
                        <button type='button' class='btn btn-link' onclick=\"location.href='/admin/projects'\" data-toggle='tooltip' title='navigate back to the admin project plugin' >Back</button>
                        </form>";
                    $adminView['footer'] = "<span style='font-size:9pt'>This window is visible to admins until the project has been approved.</span>";
                } else {
                    $adminView['table'] = "<br><br>
                    <form id='project-info' method='post'>
                        <input type='hidden' name='_csrf' value=" . Yii::$app->request->getCsrfToken() . "}' />
                        <input type='submit' name='action' value='Edit' class='btn btn-primary' style='width:90px;margin-right:20px'>
                    </form>";
                    $adminView['footer'] = "<span style='font-size:9pt'>This window is visible to the author until the project has been approved.</span>";
                }
                Yii::$app->session->setFlash(
                    'info',
                    "<span style='font-size:60px;float:right' class='glyphicon glyphicon-exclamation-sign'></span>" .
                        "<h4>This post has not yet been approved</h4>Only admins and the author of this project can see it." .
                        $adminView['table'] . "<br>" .
                        $adminView['footer']
                );
                return $this->render('view', ['model' => $model, 'isAuthor' => true]);
            }
            return $this->render('not_approved');
        }

        return $this->render('view', ['model' => $model, 'isAuthor' => false]);
    }

    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            return $this->actionNew();
        }
        return $this->render('upload');
    }

    public function actionNew()
    {
        if (Yii::$app->request->isPost && isset($_POST['Project'])) {
            $model = new Project;
            $model->scenario = 'new';

            $model->load(Yii::$app->request->post());
            $model->cover = UploadedFile::getInstance($model, 'cover');
            $model->file1 = UploadedFile::getInstance($model, 'file1');
            $model->file2 = UploadedFile::getInstance($model, 'file2');
            $model->file3 = UploadedFile::getInstance($model, 'file3');
            $model->file4 = UploadedFile::getInstance($model, 'file4');
            $model->file5 = UploadedFile::getInstance($model, 'file5');

            $transaction = $model->getDb()->beginTransaction();
            $model->save();
            $path = Yii::$app->basePath . "/files/projects/" . $model->id . "/";

            if ($model->validate()) {
                FileHelper::createDirectory($path);
                $this->save_files($model, $path);

                $transaction->commit();
                if ($this->Send_confirm_mail($model)) {
                    Yii::$app->session->setFlash('success', "Your project has been added! \n We've send you a confirmation and edit link via the provided email address.");
                } else {
                    Yii::$app->session->setFlash('warning', "Your project has been added!\nWe were unable to send you an email, please contact <a href='mailto:klushok-etv@tudelft.nl' data-mce-href='mailto:klushok-etv@tudelft.nl'>klushok-etv@tudelft.nl</a> to confirm you project.");
                }
                return $this->redirect('upload');
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('danger', 'Something went with saving the file.');
                return $this->render('upload');
            }
        } else {
            Yii::$app->session->setFlash('danger', 'Something went wrong.');
            return $this->render('upload');
        }
    }

    private function save_files($model, $path)
    {
        // if ($model->cover) {
        //     $model->cover->saveAs($path . 'cover');
        // }
        $filenames = ['cover', 'file1', 'file2', 'file3', 'file4', 'file5'];

        foreach ($filenames as $file) {
            if ($model->$file) { // check if file is empty uploaded
                $model->$file->saveAs($path . $file);
            }
        }
    }

    private function Send_confirm_mail($project)
    {
        return Yii::$app->mailer->compose(
            [
                'html' => 'project_confirm/html',
                'text' => 'project_confirm/text',
            ],
            [
                'project' => $project,
            ]
        )
            ->setFrom('noreply-etv@tudelft.nl')
            ->setTo($project->email)
            ->setSubject('Klushok project confirmation')
            ->send();
    }

    public function actionImage()
    {
        if (Yii::$app->request->isGet) {
            $id = $_GET['id'];
            $doc = $_GET['doc'];
            isset($_GET['h']) ? $hash = $_GET['h'] : $hash = null;
            $doc_type = $doc . '_type';
            $doc_ext = $doc . '_ext';
            $doc_name = $doc . '_name';

            if (isset($_GET['h'])) {
                $project = Project::findOne(['id' => $id, 'hash' => $hash]);
            } else {
                $project = Project::findOne($id);
            }

            if (!$project->approved && Yii::$app->user->isGuest && $hash === null) { // A guest cannot access images of unapproved projects
                throw new HttpException(403, 'Forbidden');
            }
            if (!$project) {
                throw new HttpException(404, 'Not Found');
            }

            if ($doc === 'cover') {
                $doc_name = 'cover';
            } else {
                $doc_name = $project->$doc_name;
            }

            $basePath = Yii::$app->basePath . "/files/projects/" . $id . "/";
            $file = $basePath . $doc;

            if (!file_exists($file)) {
                throw new HttpException(404, 'Not Found');
            }

            $this->setHttpHeaders(
                $project->$doc_ext,
                $doc_name,
                $project->$doc_type
            );

            if (!is_resource(Yii::$app->response->stream = fopen($file, 'r'))) {
                throw new ServerErrorHttpException('file access failed: permission deny');
            }

            Yii::$app->response->send();
        }
    }

    protected function setHttpHeaders($ext, $name, $mime, $encoding = 'binary')
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") == false) {
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        } else {
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: public");
        }
        header("Expires: 0");
        header("Content-Encoding: {$encoding}");
        // header("Content-Type: {$mime}; charset={$encoding}");
        header("Content-Type: {$mime}");
        header("Content-Disposition: attachment; filename={$name}.{$ext}");
        // header("Content-Disposition: attachment; filename={$name}");
        header("Cache-Control: max-age=0");
    }

    public function actionDelete($id = null)
    {
        $model = Project::findOne($id);
        if ($model == null) {
            throw new HttpException(404, 'File not found');
        }

        $transaction = $model->getDb()->beginTransaction();
        $model->delete();

        $basePath = Yii::$app->basePath . "/files/projects/" . $id;
        try {
            FileHelper::removeDirectory($basePath);
        } catch (\yii\helpers\FileHelper\ErrorException $e) {
            Yii::$app->session->setFlash('danger', 'Could not remove project directory.' . $e);
            $transaction->rollBack();
            return;
        }

        $transaction->commit();
        Yii::$app->session->setFlash('success', 'Project succesfully removed!');
        $this->redirect('/site/projects');
    }

    public function actionEdit() //WIP
    {
        // must be called using get request containing both hash and id of project
        if (Yii::$app->request->isPost && isset($_POST['Project'])) {
            $id = $_POST['Project']['id'];
            $hash_post = $_POST['Project']['hash'];

            $project = Project::findOne(['id' => $id, 'hash' => $hash_post]);
            $project->scenario = 'edit';
            $project->load(Yii::$app->request->post());

            // get instances of files and replace currently existing files
            $project->cover = UploadedFile::getInstance($project, 'cover');
            $project->file1 = UploadedFile::getInstance($project, 'file1');
            $project->file2 = UploadedFile::getInstance($project, 'file2');
            $project->file3 = UploadedFile::getInstance($project, 'file3');
            $project->file4 = UploadedFile::getInstance($project, 'file4');
            $project->file5 = UploadedFile::getInstance($project, 'file5');

            $path = Yii::$app->basePath . "/files/projects/" . $project->id . "/";
            $this->save_files($project, $path); // overwrite current files

            if ($project->save()) {  // save changes to db
                Yii::$app->session->setFlash('success', 'Project edited and saved!');
                return $this->redirect(['view', 'id' => $id, 'h' => $hash_post]);
            }
            Yii::$app->session->setFlash('error', 'Could not save edited project.');
        } elseif (Yii::$app->request->isPost && isset($_POST['action']) && $_POST['action'] === 'Approve') {

            $id = $_POST['id'];
            $hash_post = $_POST['hash'];
            $project = Project::findOne(['id' => $id, 'hash' => $hash_post]);

            $project->approved = true;
            if ($project->save()) {
                $project->send_project_approved_mail();
                Yii::$app->session->setFlash('success', 'Project approved!');
                return $this->redirect(['view', 'id' => $id, 'h' => $hash_post]);
            }
        }

        if (!isset($_GET['id']) || !isset($_GET['h'])) { // bad request
            Yii::$app->session->setFlash('error', 'Bad edit request.');
            return $this->redirect('/projects');
        }

        $id = $_GET['id'];
        $hash = $_GET['h'];
        $model = Project::findOne(['id' => $id, 'hash' => $hash]);

        if (!$model) {
            Yii::$app->session->setFlash('error', 'Error editing this project, not found');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->scenario = 'edit';

        if ($model->approved) {    // model is approved, editing not allowed
            Yii::$app->session->setFlash('error', 'You cannot edit approved projects, please contact the klushok committee if you want to make changes');
            return $this->redirect(['view', 'id' => $id]);
            //return $this->refresh();
        }

        return $this->render('edit', ['model' => $model, 'hash' => $hash]);
        // edit your project using the email link
    }

    public function actionConfirm() //WIP
    {
        if (!isset($_GET['h'])) {                   // no hash code provided
            throw new HttpException(404, "Not Found");
        }

        $hash = $_GET['h'];
        $project = Project::findOne(['hash' => $hash]);

        if (!$project) {
            throw new HttpException(404, 'Project not found');
        }

        if ($project->confirmed) {                        // already confirmed -> send to editable view
            return $this->redirect(['view', 'id' => $project->id, 'h' => $project->hash]);
        } elseif (Yii::$app->request->isPost && isset($_POST['Project']['confirmed'])) {    // 'confirm project' submitted
            $project->confirmed = true;
            if ($project->save()) {
                Yii::$app->session->setFlash('success', 'Project confirmed!');

                $mailTo = array_filter(Maillist::getMaillistMembersByPath('project_confirmed_committee'));
                if (!empty($mailTo)) {
                    Yii::$app->mailer->compose(
                        [
                            'html' => 'project_confirmed_committee/html',
                            'text' => 'project_confirmed_committee/text'
                        ],
                        [
                            'model' => $project,
                        ]
                    )
                        ->setFrom('noreply-etv@tudelft.nl')
                        ->setTo($mailTo)
                        ->setSubject('Klushok project confirmed')
                        ->send();
                }
                return $this->redirect(['view', 'id' => $project->id, 'h' => $project->hash]);
            } else {
                Yii::$app->session->setFlash('danger', 'Something went wrong during confirmation, please try reloading this page or contact <a href="mailto:klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a>');
            }
        }

        Yii::$app->session->setFlash('success', $this->renderAjax('_confirmForm', ['model' => $project]));
        return $this->render('view', ['model' => $project, 'isAuthor' => true]);
    }

    public function actionImgdelete()
    {
        if (isset($_GET['h']) && isset($_GET['file'])) {
            $hash = $_GET['h'];
            $filenames = ['file1', 'file2', 'file3', 'file4', 'file5'];
            $file = $filenames[$_GET['file']];

            $project = Project::findOne(['hash' => $hash]);
            if (!$project->approved) {
                $path = Yii::$app->basePath . "/files/projects/" . $project->id . "/";
                if (unlink($path . $file)) {
                    $project->{$file . '_type'} = null;
                    $project->{$file . '_name'} = null;
                    $project->{$file . '_ext'} = null;
                    $project->save();
                    Yii::$app->session->setFlash('success', 'file deleted');
                } else {
                    Yii::$app->session->setFlash('danger', 'Something went wrong');
                }
            }
            return $this->redirect(['projects/edit', 'id' => $project->id, 'h' => $hash]);
        }
    }
}
