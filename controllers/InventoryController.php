<?php

namespace app\controllers;

use app\models\Component;
use app\models\Inventory;
use app\modules\maillist\models\Maillist;
use Yii;
use yii\bootstrap\Alert;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;


class InventoryController extends Controller
{

    // public function actionPricelist_pdf() // generate pricelist as pdf
    // {
    //     $pricelist = $this->renderPartial('/inventory/pricelist');
    //     $pdf = new \Mpdf\Mpdf();
    //     // $pdf->writeHTML(
    //     //     "<section id='pricelist-container'>".
    //     //         "<h1><span>pricelist</span></h1>".
    //     //         InventoryController::getPricelist().
    //     //     "<div style='clear:both'></div>".
    //     //     "</section>)");
    //     $pdf->SetHTMLHeader('');
    //     $pdf->AddFontDirectory('@web/fonts');
    //     $pdf->writeHTML($pricelist);
    //     return $pdf->Output();
    // }

    // public function actionPricelist()        // generate partial render of pricelist
    // {
    //     return $this->renderPartial('pricelist');
    // }

    public function actionStock()
    {
        $model = new Inventory();
        $model->scenario = 'stock';

        if (isset($_GET['category'])) {
            $model->category = $_GET['category'];
            if (isset($_GET['name'])) {
                $model->category = $_GET['name'];
                if (isset($_GET['info'])) {
                    $model->category = $_GET['info'];
                }
            }
        }

        if (isset($_POST['Inventory']['category'])) {
            $category = $_POST['Inventory']['category'];
            $model->category = $category;

            if (isset($_POST['Inventory']['name'])) {
                $name = $_POST['Inventory']['name'];

                // check if category + name combination exists, clear name field if not
                if (empty(Inventory::findOne(['category' => $category, 'name' => $name]))) {
                    $name = null;
                }
                $model->name = $name;

                if (isset($_POST['Inventory']['info'])) { // User submitted the form! 
                    $info = $_POST['Inventory']['info'];
                    $model->info = $info;
                    $model->additionalNotes = $_POST['Inventory']['additionalNotes'];

                    $model2 = Inventory::findOne([
                        'category' => $category,
                        'name' => $name,
                        'info' => $info
                    ]);
                }
            }
        }

        return $this->render('stock', [
            'model' => $model
        ]);
    }

    public function actionStock_user_submit()
    {
        if (
            !isset($_POST['Inventory']['category']) ||
            !isset($_POST['Inventory']['name']) ||
            !isset($_POST['Inventory']['info'])
        ) {
            Yii::$app->session->setFlash('danger', 'Please fill in a category, name and info. If no info is specified on the pricelist, choose \'Select info\'.');
            return $this->redirect('stock');
        }
        $category = $_POST['Inventory']['category'];
        $name = $_POST['Inventory']['name'];
        $info = $_POST['Inventory']['info'];
        $additionalNotes = $_POST['Inventory']['additionalNotes'];


        $model = Inventory::findOne([
            'category' => $category,
            'name' => $name,
            'info' => $info
        ]);

        if (empty($model)) {
            Yii::$app->session->setFlash('danger', 'Combination of selected attributes not found, please try again or contact <a href = "mailto: klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a>');
            return $this->redirect('stock');
        }

        $model->additionalNotes = $additionalNotes;
        if ($model->stock >= 0) {
            $model->stock++;
        }
        if ($model->save() && $this->sendMail($model)) {
            Yii::$app->session->setFlash('success', 'The committee has been notified! Thank you');
        } else {
            Yii::$app->session->setFlash('danger', 'Something went wrong when letting the committee know, please contact <a href = "mailto: klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a>');
        }
        return $this->redirect('stock');
    }


    private function sendMail($model)
    {   
        $mailTo = array_filter(Maillist::getMaillistMembersByPath('inventory_committee'));
        if(empty($mailTo)){     // no recipients -> no mail to send
            return true;
        }
        return Yii::$app->mailer->compose(
            [
                'html' => 'inventory_committee/html',
                'text' => 'inventory_committee/text',
            ],
            [
                'model' => $model,
            ]
        )
            ->setFrom('noreply-etv@tudelft.nl')
            ->setTo($mailTo)
            ->setSubject('Klushok component is op')
            ->send();
    }
}
