<?php

namespace app\controllers;

use app\models\Customer;
use app\models\Order_details;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Order;
use yii\helpers\Html;
use app\models\ConfirmForm;
use app\modules\maillist\models\Maillist;

class OrderController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'place' => ['post'],
                ],
            ],
        ];
    }

    // public function actionShoppingcart()
    // {
    //     return $this->render('shoppingcart');
    // }

    public function actionConfirm()
    {
        $hash = Yii::$app->request->get('h');
        if (!empty($hash)) {                    // not empty get request for key h
            $orders = Order::find()            // find orders where 
                ->where(['hash' => $hash])
                ->all();
            if (!empty($orders)) {
                $isConfirmed = true;
                foreach ($orders as $order) {         // check if orders are confirmed
                    $isConfirmed = $isConfirmed && $order->confirmed;
                }

                if (!$isConfirmed) {      //at least one of the orders has not been confirmed
                    $model = new ConfirmForm;
                    if ($model->load(Yii::$app->request->post()) && $model->validate()) { // check if confirmation form has been submitted
                        foreach ($orders as $order) {
                            $order->confirmed = true;
                            $order->save();
                        }
                        $mailTo = array_filter(Maillist::getMaillistMembersByPath('order_placed_committee'));
                        if (!empty($mailTo)) {
                            Yii::$app->mailer->compose(
                                [
                                    'html' => 'order_placed_committee/html',
                                    'text' => 'order_placed_committee/text'
                                ],
                                [
                                    'models' => $orders,
                                ]
                            )
                                ->setFrom('noreply-etv@tudelft.nl')
                                ->setTo($mailTo)
                                ->setSubject('Klushok order confirmed')
                                ->send();
                        }
                        return $this->refresh();
                    }
                    return $this->render('confirm', [  // Ask user to confirm order
                        'model' => $model,
                        'hash' => $hash,
                        'orders' => $orders,
                    ]);
                } elseif ($isConfirmed) {   //all orders are confirmed
                    return $this->confirmed_success($orders);
                }
            }
        }
        return $this->render('confirmed_error');
    }


    public function confirmed_success($orders)
    {

        $isConfirmed = true;
        $isPaid = true;
        $isOrdered = true;
        $isRetrieved = true;
        $isArrived = true;

        foreach ($orders as $order) {
            $isConfirmed = $isConfirmed && $order->confirmed;
            $isPaid = $isPaid && $order->paid;
            $isOrdered = $isOrdered && $order->ordered;
            $isArrived = $isArrived && $order->arrived;
            $isRetrieved = $isRetrieved && $order->retrieved;
        }

        return $this->render('confirmed_success', [
            'isConfirmed' => $isConfirmed,
            'isPaid' => $isPaid,
            'isOrdered' => $isOrdered,
            'isArrived' => $isArrived,
            'isRetrieved' => $isRetrieved
        ]);
    }

    public function actionSubmit()
    {
        /*  
            De grote Ik-Wil-Bestellen-Flowchart
            1. Kijk of $user al bestaat in customers database
                - bestaat $user niet, maak deze aan en vraag id op
                - bestaat user wel, vraag user_id op
            2. maak Order aan in db
            3. splits order op per winkel (nodig voor de booleans)
            4. vraag order_id op per winkel
            5. schrijf de order_details naar de db per winkel
        
        */


        $post               = Yii::$app->request->post();
        $cart_details       = json_decode($post['cart'], true);
        $customer_details   = json_decode($post['customer'], true);
        // TODO check validation from order details

        $customer = Customer::find()
            ->where(['email' => $customer_details['email']])
            ->one();                                                //find customer by email

        if ($customer === null) {       //create customer is it does not exist
            $customer = new Customer;
            $customer->name = $customer_details['name'];
            $customer->email = $customer_details['email'];
            $customer->student_no = $customer_details['student_no'];
            if (!$customer->save()) {
                return json_encode([
                    'status' => false,
                    'message' => "Something seems to be wrong with your personal information, please verify and click 'order' again. 
                    <br> If the issue persists, please send an email explaining the issue to <a href='mailto:klushok-etv@tudelft.nl'>klushok-etv@tudelft.nl</a>"
                ]);
            }
        }


        // $customer_id = $this->customerExists($customer_details); //get customer_id or create new customer
        // $customer = (new Customer)->getCustomerById($customer_id);

        $split_order = $this->splitStores($cart_details);
        $hash = md5(uniqid(rand(), true));

        foreach ($split_order as $key => $order) {
            $order_id[$key] = $this->addOrder($customer->id, $hash);
            if ($order_id[$key] != null) { // check if order was created
                foreach ($order as $detail) {

                    $order_details = new Order_details;

                    $order_details->order_id = $order_id[$key];
                    $order_details->store = $detail['store'] + 1; // Offset of 1 due to mysql index starting at 1
                    $order_details->part_no = $detail['order_no'];
                    $order_details->description = $detail['description'];
                    $order_details->quantity = $detail['quantity'];
                    if (!$order_details->save()) {
                        $this->detailNotSaved($order_id);
                        return json_encode([
                            'status' => false,
                            'message' => "Your order contained an error and could therefore not be saved.\n" .
                                "Entry containing the error: \n" .
                                "Store: " . Order_details::$stores[$detail['store']] . "\n" .
                                'Part number: ' . $detail['order_no'] . "\n"
                        ]);
                    }
                }
            }
        }

        $this->sendMail($hash, $customer);

        return json_encode([
            'status' => true,
            'message' => "Awesome! you've confirmed your order! \nPlease check your email for a confirmation message"
        ]);
    }

    /**
     * Deletes the order and its order details containing an error
     * 
     * @param array Array containing the id's of the orders that have been placed by the user 
     * @return bool true if all orders and corresponding details could be deleted, else false
     */
    private function detailNotSaved($order_id_array)
    {
        $success = true;
        foreach ($order_id_array as $id) {
            $success = $success && Order_details::deleteAll('order_id = ' . $id); // delete all order detials for this order
            $success = $success && Order::deleteAll('id = ' . $id); // only delete order if all order details were deleted
        }
        return $success;
    }

    public function addOrder($user_id, $hash)
    {
        $order = new Order();
        $order->hash = $hash;
        $order->user_id = $user_id;
        $order->confirmed = false;
        $order->ordered = false;
        $order->paid = false;
        $order->retrieved = false;
        $order->save(); //new row added to table
        return $order->getPrimaryKey(); //see https://forum.yiiframework.com/t/how-to-get-an-autoincremented-id-from-a-new-model-save/27647/3
    }

    private function sendMail($hash, Customer $customer)  //temporary static function
    {
        Yii::$app->mailer->compose(
            [
                'html' => 'order_confirm/html',
                'text' => 'order_confirm/text'
            ],
            [
                'customer' => $customer,
                'hash' => $hash
            ]
        )
            ->setFrom('noreply-etv@tudelft.nl')
            ->setTo($customer->email)
            ->setSubject('Klushok order confirmation')
            ->send();
    }

    // private function customerExists($customer_details)
    // {
    //     $customer = (new Customer)->getCustomerByEmail($customer_details['email']);
    //     if ($customer[1] === 0) {       //create customer if he/she does not exist
    //         $user_id = (new Customer)->addCustomer(
    //             $customer_details['email'],
    //             $customer_details['name'],
    //             $customer_details['student_no']
    //         );
    //         return $user_id;
    //     }
    //     return $customer[0][0]['id'];   //array from getcustomer method, returning first object
    // }
    // //TODO: handling van meerdere customers met hetzelfde email adress

    public function splitStores($cart_details)
    {
        $split_order = array();
        foreach ($cart_details as $detail) {
            $store = $detail['store'];                      //find the store from the details
            if (!array_key_exists($store, $split_order)) {  //check if the store has an array yet
                $split_order[$store][0] = $detail;          //If the store does not yet have an array, create one
            } else {
                array_push($split_order[$store], $detail); //write the details to the corresponding store in the array
            }
        }
        return $split_order;
    }



    // public function getRSImage($order_no)
    // {
    //     //$order_no = '289-9997';                                         //rs stock number
    //     $order_no = preg_replace('/\D/', '', $order_no);                //any non-numerical entries removed

    //     $url = 'https://nl.rs-online.com/web/c/?sra=oss&r=t&searchTerm=' . $order_no; //url for rs search query

    //     $arrContextOptions = array(
    //         "ssl" => array(
    //             "verify_peer" => false,
    //             "verify_peer_name" => false,
    //         ),
    //     );


    //     $html = htmlspecialchars(file_get_contents($url, false, stream_context_create($arrContextOptions)));                  //sanitized html body of result 
    //     $RS_media_url = '//media.rs-online.com/t_thumb100/';                     //first part of the url to the image
    //     $media_pos = strpos($html, $RS_media_url);                           //location of media url

    //     $mainImage = 0;                                                     //mainimage in case of undefined
    //     if ($media_pos == false) {
    //         echo 'invalid RS order number!' . "\r\n";                       //no media string found in html body
    //     } else {                                       //determine the url to the image
    //         $mainImage = substr($html, $media_pos, (strpos($html, htmlspecialchars('"'), $media_pos) - $media_pos));
    //     }
    //     //echo "\r\n" . "<img src =  $mainImage>" . "\r\n";           //show the image    
    //     return $mainImage;
    // }

    // public function getFarnellImage($order_no)
    // {
    //     $order_no = preg_replace('/\D/', '', $order_no);                //any non-numerical entries removed
    //     $key = '23tmszq3mwx2xf7yg57nm29n';                              //element14 api key

    //     $url = 'https://api.element14.com/catalog/products?term=id%3A' . $order_no . '&storeInfo.id=nl.farnell.com&resultsSettings.offset=0&resultsSettings.numberOfResults=1&resultsSettings.responseGroup=large&callInfo.omitXmlSchema=false&callInfo.responseDataFormat=json&callinfo.apiKey=' . $key;           //url for farnell search query

    //     $arrContextOptions = array(
    //         "ssl" => array(
    //             "verify_peer" => false,
    //             "verify_peer_name" => false,
    //         ),
    //     );

    //     $html = file_get_contents($url, false, stream_context_create($arrContextOptions));             //sanitized html body of result
    //     $farnell_media_url = 'https://nl.farnell.com/productimages/standard/en_GB';  //first part of the url to the image
    //     $farnell_media_key = '.jpg';
    //     $media_pos = strpos($html, $farnell_media_key) + strlen($farnell_media_key);                  //location of media url

    //     $mainImage = 0;                                                 //mainimage in case of undefined
    //     if ($media_pos == false) {
    //         echo 'invalid RS order number!' . "\r\n";                       //no media string found in html body
    //     } else {                                       //determine the url to the image
    //         $mainImage = substr($html, 0, $media_pos);                      //string up and including the .jpg extension
    //         $mainImage = substr($mainImage, strrpos($mainImage, '/'));     //substring containing the image name and extension
    //         $mainImage = $farnell_media_url . $mainImage;                   //full path to image
    //     }
    //     return $mainImage;
    // }
}
