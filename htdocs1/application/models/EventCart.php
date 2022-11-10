<?php

class EventCart extends FatModel
{

    private $cartData = [];
    private $cart_user_id;

    const DB_TBL = 'tbl_user_cart';
    const TYPE_EVENT_SPONSER_BOOK = 4;
    const TYPE_GIFTCARD = 2;
    const TYPE_KIDS_BOOK = 3;

    public function __construct($user_id = 0)
    {
        parent::__construct();
        $user_id = FatUtility::int($user_id);
        if ($user_id < 1) {
            $user_id = EventUserAuthentication::getLoggedUserId();
        }

        $srch = new SearchBase('tbl_user_cart');
        $srch->addCondition('usercart_user_id', '=', EventUserAuthentication::getLoggedUserId());
        // $srch->addCondition('usercart_type', '=', EventCart::TYPE_EVENT_SPONSER_BOOK);
        
        $rs = $srch->getResultSet();
        if ($row = FatApp::getDb()->fetch($rs)) {
            $this->SYSTEM_ARR['cart'] = unserialize($row["usercart_details"]);
            if (isset($this->SYSTEM_ARR['cart']['shopping_cart'])) {
                $this->SYSTEM_ARR['shopping_cart'] = $this->SYSTEM_ARR['cart']['shopping_cart'];
                unset($this->SYSTEM_ARR['cart']['shopping_cart']);
            }
        }
        if (!isset($this->SYSTEM_ARR['cart']) || !is_array($this->SYSTEM_ARR['cart'])) {
            $this->SYSTEM_ARR['cart'] = [];
        }
        if (!isset($this->SYSTEM_ARR['shopping_cart']) || !is_array($this->SYSTEM_ARR['shopping_cart'])) {
            $this->SYSTEM_ARR['shopping_cart'] = [];
        }
    }

    public function add(int $teacherId,int $languageId=0, int $grpclsId = 0,  $startDateTime = '', $endDateTime = '')
    {
        $this->SYSTEM_ARR['cart'] = [];
       
        $db = FatApp::getDb();
        /* validate teacher[ */

         $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId());
      
        $planData=new SearchBase('tbl_three_reasons');
        $planData->addCondition('registration_plan_title','=',$userRow['user_sponsorship_plan']);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $grpclsId=$planResult['three_reasons_id'];  
        // echo "<pre>";
        // print_r($grpclsId);
          if (!$userRow) {
            $this->error = Label::getLabel('LBL_Teacher_not_found');
            return false;
        }
        $userId=EventUserAuthentication::getLoggedUserId();    
        $key = $userId . '_' . $grpclsId;
        $key = base64_encode(serialize($key));
        $this->SYSTEM_ARR['eve_cart'][$key] = [
            'teacherId' => $loggedUserId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 0,
        ];
        // $this->updateEventUserCart();
        return true;
    }


    public function cartData($langId)
    {
        $key = key($this->SYSTEM_ARR['cart']);
        if (empty($key)) {
            $this->error = Label::getLabel('LBL_SOMETHING_WENT_WORNG');
            return false;
        }
        $cartData = $this->SYSTEM_ARR['cart'][$key];
        $languageId = $cartData['languageId'];
        $lessonDuration = $cartData['lessonDuration'];
        $lessonQty = $cartData['lessonQty'];
        $grpclsId = $cartData['grpclsId'];
        $isFreeTrial = $cartData['isFreeTrial'];
        $keyDecoded = unserialize(base64_decode($key));
        list($teacherId, $grpclsId) = explode('_', $keyDecoded);
         $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId());
        $planData=new SearchBase('tbl_three_reasons');
        $planData->addCondition('registration_plan_title','=',$userRow['user_sponsorship_plan']);
        $planResult = $db->fetch($planData->getResultSet());
        $grpclsId=$planData['three_reasons_id'];  
        $itemName = '';
        if (empty($userRow)) {
           
            $this->removeCartKey($key);

            $this->error = Label::getLabel('LBL_Invalid_Request');
            return false;
        }
        $totalPrice = $planResult['registration_plan_price'];
        $itemPrice = $totalPrice;
        $itemName=$planResult['registration_plan_title']
        
        $this->cartData = $userRow;
        $this->cartData['key'] = $key;
        $this->cartData['grpclsId'] = $grpclsId;
        $this->cartData['teacherId'] = $teacherId;
        $this->cartData['isFreeTrial'] = $isFreeTrial;
        $this->cartData['lessonQty'] = $lessonQty;
        $this->cartData['languageId'] = $languageId;
        $this->cartData['lessonDuration'] = $lessonDuration;
        $this->cartData['lpackage_is_free_trial'] = $isFreeTrial;
        $this->cartData['lpackage_lessons'] = $lessonQty;
        $this->cartData['startDateTime'] = $cartData['startDateTime'];
        $this->cartData['endDateTime'] = $cartData['endDateTime'];
        $this->cartData['startDateTime'] = $cartData['startDateTime'];
        $this->cartData['endDateTime'] = $cartData['endDateTime'];
        $this->cartData['itemName'] = $itemName;
        $this->cartData['itemPrice'] = $itemPrice;
        $this->cartData['total'] = $totalPrice;
        return $this->cartData;
    }

    public function getCart($langId = 0)
    {
    
       
        $langId = FatUtility::int($langId);
        if (!$this->cartData) {
        
            /* cart Summary[ */
            $this->cartData = $this->cartData($langId);
            if (empty($this->cartData)) {
    
                return [];
            }
            $userWalletBalance = EventUser::getUserBalance($this->cart_user_id);
            $cartTotal = $this->cartData['total'];
            $cartTaxTotal = 0;
            $cartDiscounts = $this->getCouponDiscounts($langId);
            $totalSiteCommission = 0;
            $totalDiscountAmount = $cartDiscounts['coupon_discount_total'] ?? 0;
            $orderNetAmount = ($cartTotal + $cartTaxTotal) - $totalDiscountAmount;
            $walletAmountCharge = ($this->isCartUserWalletSelected()) ? min($orderNetAmount, $userWalletBalance) : 0;
            $orderPaymentGatewayCharges = $orderNetAmount - $walletAmountCharge;
            $summaryArr = [
                'cartTotal' => $cartTotal,
                'cartTaxTotal' => $cartTaxTotal,
                'cartDiscounts' => $cartDiscounts,
                'cartWalletSelected' => $this->isCartUserWalletSelected(),
                'siteCommission' => $totalSiteCommission,
                'orderNetAmount' => $orderNetAmount,
                'walletAmountCharge' => $walletAmountCharge,
                'orderPaymentGatewayCharges' => $orderPaymentGatewayCharges,
            ];
            $this->cartData = $this->cartData + $summaryArr;
            /* ] */
        }
        return $this->cartData;
    }

    public function updateCartWalletOption($val,$fromKids=0)
    {
        $this->SYSTEM_ARR['shopping_cart']['Pay_from_wallet'] = $val;
        
            $this->updateEventUserCart();
        
        return true;
    }

    public function isCartUserWalletSelected()
    {
        return (isset($this->SYSTEM_ARR['shopping_cart']['Pay_from_wallet']) && intval($this->SYSTEM_ARR['shopping_cart']['Pay_from_wallet']) == 1) ? 1 : 0;
    }

    public function removeCartKey($key)
    {
        unset($this->cartData[$key]);
        unset($this->SYSTEM_ARR['cart'][$key]);
        $this->updateEventUserCart();
        return true;
    }
    public function removeCartKidsKey($key)
    {
        unset($this->cartData[$key]);
        unset($this->SYSTEM_ARR['cart'][$key]);
        $this->updateUserKidsCart();
        return true;
    }

    public function updateEventUserCart()
    {
        if (isset($this->cart_user_id)) {
            $record = new TableRecord('tbl_user_cart');
            $cart_arr = $this->SYSTEM_ARR['cart'];
            if (isset($this->SYSTEM_ARR['shopping_cart']) && is_array($this->SYSTEM_ARR['shopping_cart']) && (!empty($this->SYSTEM_ARR['shopping_cart']))) {
                $cart_arr["shopping_cart"] = $this->SYSTEM_ARR['shopping_cart'];
            }
            $cart_arr = serialize($cart_arr);
            $record->assignValues([
                "usercart_user_id" => $this->cart_user_id,
                "usercart_type" => EVENTCART::TYPE_EVENT_SPONSER_BOOK,
                "usercart_details" => $cart_arr,
                "usercart_added_date" => date('Y-m-d H:i:s')
            ]);
            if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            
                Message::addErrorMessage($record->getError());
                throw new Exception('');
            }
        }
    }
    public function updateUserKidsCart()
    {
        if (isset($this->cart_user_id)) {
            $record = new TableRecord('tbl_user_cart');
            $cart_arr = $this->SYSTEM_ARR['cart'];
            if (isset($this->SYSTEM_ARR['shopping_cart']) && is_array($this->SYSTEM_ARR['shopping_cart']) && (!empty($this->SYSTEM_ARR['shopping_cart']))) {
                $cart_arr["shopping_cart"] = $this->SYSTEM_ARR['shopping_cart'];
            }
            $cart_arr = serialize($cart_arr);
            $record->assignValues([
                "usercart_user_id" => $this->cart_user_id,
                "usercart_type" => EVENTCART::TYPE_KIDS_BOOK,
                "usercart_details" => $cart_arr,
                "usercart_added_date" => date('Y-m-d H:i:s')
            ]);
            if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            
                Message::addErrorMessage($record->getError());
                throw new Exception('');
            }
        }
    }

    public function getCartUserId()
    {
        return $this->cart_user_id;
    }

    public function hasItems()
    {
        return count($this->SYSTEM_ARR['cart']);
    }

    public function clear()
    {
        $this->cartData = [];
        $this->SYSTEM_ARR['cart'] = [];
        $this->SYSTEM_ARR['shopping_cart'] = [];
        unset($_SESSION['shopping_cart']["order_id"]);
        unset($_SESSION['oneOneOne']);
    }

    public function updateCartDiscountCoupon($val)
    {
        $this->SYSTEM_ARR['shopping_cart']['discount_coupon'] = $val;
        $this->updateEventUserCart();
        return true;
    }

    public function removeUsedRewardPoints()
    {
        if (isset($this->SYSTEM_ARR['shopping_cart']) && array_key_exists('reward_points', $this->SYSTEM_ARR['shopping_cart'])) {
            unset($this->SYSTEM_ARR['shopping_cart']['reward_points']);
            $this->updateEventUserCart();
        }
        return true;
    }

    public function getCouponDiscounts($langId = 0)
    {
        $couponObj = new DiscountCoupons();
        if (!$this->getCartDiscountCoupon()) {
            return false;
        }
        $couponInfo = $couponObj->getValidCoupons($this->cart_user_id, $langId, $this->getCartDiscountCoupon());
        $cartSubTotal = $this->getSubTotal($langId);
        $couponData = [];
        if ($couponInfo) {
            $labelArr = [
                'coupon_label' => $couponInfo["coupon_title"],
                'coupon_id' => $couponInfo["coupon_id"],
                'coupon_discount_in_percent' => $couponInfo["coupon_discount_in_percent"],
                'max_discount_value' => $couponInfo["coupon_max_discount_value"]
            ];
            if ($couponInfo['coupon_discount_in_percent'] == applicationConstants::PERCENTAGE) {
                $couponDiscountValue = $cartSubTotal * $couponInfo['coupon_discount_value'] / 100;
            } elseif ($couponInfo['coupon_discount_in_percent'] == applicationConstants::FLAT) {
                $couponDiscountValue = $couponInfo["coupon_discount_value"];
            }
            if ($cartSubTotal < $couponDiscountValue) {
                $couponDiscountValue = $cartSubTotal;
            }
            if ($couponInfo['coupon_discount_in_percent'] == applicationConstants::PERCENTAGE && $couponDiscountValue > $couponInfo["coupon_max_discount_value"] ){
                $couponDiscountValue = $couponInfo["coupon_max_discount_value"];
            }
            $couponData = [
                'coupon_discount_type' => $couponInfo["coupon_type"],
                'coupon_code' => $couponInfo["coupon_code"],
                'coupon_discount_value' => $couponInfo["coupon_discount_value"],
                'coupon_discount_total' => $couponDiscountValue,
                'coupon_info' => json_encode($labelArr),
            ];
        }
        if (empty($couponData)) {
            return false;
        }
        return $couponData;
    }

    public function getSubTotal($langId)
    {
        if (!$this->cartData) {
            return 0;
        }
        $cartTotal = $this->cartData($langId);
        return $cartTotal['total'];
    }

    public function getCartDiscountCoupon()
    {
        return isset($this->SYSTEM_ARR['shopping_cart']['discount_coupon']) ? $this->SYSTEM_ARR['shopping_cart']['discount_coupon'] : '';
    }

    public function removeCartDiscountCoupon()
    {
        $couponCode = array_key_exists('discount_coupon', $this->SYSTEM_ARR['shopping_cart']) ? $this->SYSTEM_ARR['shopping_cart']['discount_coupon'] : '';
        unset($this->SYSTEM_ARR['shopping_cart']['discount_coupon']);
        /* Removing from temp hold[ */
        if ($couponCode != '') {
            $loggedUserId = $this->cart_user_id;
            $srch = DiscountCoupons::getSearchObject(0, false, false);
            $srch->addCondition('coupon_code', '=', $couponCode);
            $srch->setPageSize(1);
            $srch->addMultipleFields(['coupon_id']);
            $rs = $srch->getResultSet();
            $couponRow = FatApp::getDb()->fetch($rs);
            if ($couponRow && $loggedUserId) {
                FatApp::getDb()->deleteRecords(DiscountCoupons::DB_TBL_COUPON_HOLD, ['smt' => 'couponhold_coupon_id = ? AND couponhold_user_id = ?', 'vals' => [$couponRow['coupon_id'], $loggedUserId]]);
            }
        }
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        if ($orderId != '') {
            FatApp::getDb()->deleteRecords(DiscountCoupons::DB_TBL_COUPON_HOLD_PENDING_ORDER, ['smt' => 'ochold_order_id = ?', 'vals' => [$orderId]]);
        }
        /* ] */
        $this->updateEventUserCart();
        return true;
    }

}
