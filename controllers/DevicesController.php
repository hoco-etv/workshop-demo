<?php

namespace app\controllers;

use app\models\Device;
use app\modules\maillist\models\Maillist;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;

class DevicesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays report device page.
     *
     * @return view
     */
    public function actionReport()
    {
        if (!isset($_GET['id'])) {
            Yii::$app->session->setFlash('danger', 'Please select a device to report a problem');
            return $this->redirect('/devices');
        }

        $device = Device::findOne($_GET['id']);
        if(empty($device)){
            throw new HttpException(404,'Device not found');
        }
        return $this->render('report', [
            'device' => $device,
        ]);
    }

    public function actionReport_submitted()
    {

        if (isset($_POST['Device']) && isset($_POST['Device']['id'])) {

            $id = $_POST['Device']['id'];
            $device = Device::findOne($id);

            if (empty($device)) {
                Yii::$app->session->setFlash('danger', 'Device not found in database, please send an email to <a href="mailto:klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a>');
                return $this->redirect('/devices');
            }

            $date = date('Y-m-d H:i:s');
            $user = '';
            $email = '';

            $mdreport = "\n\n##User report" . (empty($user) ? '' : ' by '.$user) .
                "\n **" . $date . "**\n" .
                (empty($email) ? '' : '**Email: ' . $email . '**\n\n') .
                $_POST['Device']['userReport'];

            $device->repair_notes .= $mdreport;
            $device->last_updated_at = $date;
            if ($device->status == 0) {
                $device->status = 1; // change status to 'problem reported'
            }

            if ($device->save() && $this->sendMail($device)) {
                Yii::$app->session->setFlash('success', 'Your issue has been reported. Thank you for letting us know!');
            } else {
                Yii::$app->session->setFlash('danger', 'Could not save report, please try again or send an email to <a href="mailto:klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a>');
            }
        }
        return $this->redirect('/devices');
    }

    private function sendMail($model)
    {
        $mailTo = array_filter(Maillist::getMaillistMembersByPath('devices_committee'));
        if(empty($mailTo)){     // empty recipients, no mail to send
            return true;
        }
        return Yii::$app->mailer->compose(
            [
                'html' => 'devices_committee/html',
                'text' => 'devices_committee/text',
            ],
            [
                'model' => $model,
            ]
        )
            ->setFrom('noreply-etv@tudelft.nl')
            ->setTo($mailTo)
            ->setSubject('Klushok apparaat is kapot')
            ->send();
    }


}
