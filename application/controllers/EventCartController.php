<?php

class EventCartController extends FatController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }


    public function add()
    {

       
        
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
         $grpclsId = FatApp::getPostedData('grpclsId', FatUtility::VAR_INT, 0);
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        $oneOnOne=FatApp::getPostedData('oneOnOne', FatUtility::VAR_INT, 0);
        $newlessonQty=FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 1);
        /* ] */
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        /* add to cart[ */
             $this->SYSTEM_ARR['cart'] = [];
       
        // $db = FatApp::getDb();
        // /* validate teacher[ */

        $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId());
      
        $planData=new SearchBase('tbl_three_reasons');
        $planData->addCondition('registration_plan_title','=',$userRow['user_sponsorship_plan']);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $grpclsId=$planResult['three_reasons_id'];  
        // echo "<pre>";
        // print_r($grpclsId);
                    $this->SYSTEM_ARR['cart'] = [];
          if (!$userRow) {
            $this->error = Label::getLabel('LBL_Teacher_not_found');
            return false;
        }
        $userId=EventUserAuthentication::getLoggedUserId();    
        $key = $userId . '_' . $grpclsId;
        $key = base64_encode(serialize($key));
        $cartTotal =300;
          $this->SYSTEM_ARR['cart'][$key] = [
            'teacherId' => $loggedUserId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 0,
             'cartTotal' => $cartTotal,
                'cartTaxTotal' => 0,
                'cartDiscounts' => 0,
                'cartWalletSelected' => 0,
                'siteCommission' => 0,
                'orderNetAmount' => $cartTotal,
                'walletAmountCharge' => 0,
                'orderPaymentGatewayCharges' => $cartTotal
        ];
        $cart_arr = $this->SYSTEM_ARR['cart'];
            // if (isset($this->SYSTEM_ARR['shopping_cart']) && is_array($this->SYSTEM_ARR['shopping_cart']) && (!empty($this->SYSTEM_ARR['shopping_cart']))) {
            //     $cart_arr["shopping_cart"] = $this->SYSTEM_ARR['shopping_cart'];
            // }
            $cart_arr = serialize($cart_arr);
            $_SESSION['cart']=$this->SYSTEM_ARR['cart'];

        $this->updateEventUserCart();
        // $cart = new EventCart();
       
        //     if (!$cart->add($loggedUserId,$this->siteLangId,$grpclsId)) {
        //     FatUtility::dieJsonError($cart->getError());
        //     }
      
    
        // $cartData = $cart->getCart($this->siteLangId,$fromKids);
        // if (empty($cartData)) {
        //     FatUtility::dieJsonError($cart->getError());
        // }
        $msg = '';
        if (isset($post['checkoutPage'])) {
            $msg = Label::getLabel('LBL_ITEM_ADD_TO_CART.');
        }
        // 'redirectUrl' => CommonHelper::generateUrl('Checkout')
        FatUtility::dieJsonSuccess(['isFreeLesson' => applicationConstants::NO, 'msg' => $msg]);
    }

 public function updateEventUserCart()
    {
        
            $record = new TableRecord('tbl_user_cart');
            $cart_arr = $this->SYSTEM_ARR['cart'];
            // if (isset($this->SYSTEM_ARR['shopping_cart']) && is_array($this->SYSTEM_ARR['shopping_cart']) && (!empty($this->SYSTEM_ARR['shopping_cart']))) {
            //     $cart_arr["shopping_cart"] = $this->SYSTEM_ARR['shopping_cart'];
            // }
            $cart_arr = serialize($cart_arr);
            $record->assignValues([
                "usercart_user_id" => EventUserAuthentication::getLoggedUserId(),
                "usercart_type" =>4,
                "usercart_details" => $cart_arr,
                "usercart_added_date" => date('Y-m-d H:i:s')
            ]);
            // $_SESSION['cart']=$cart_arr;
            if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            
                Message::addErrorMessage($record->getError());
                throw new Exception('');
            }
       
    }
}
