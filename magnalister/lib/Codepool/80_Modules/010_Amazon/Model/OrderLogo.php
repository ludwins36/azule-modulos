<?php

class ML_Amazon_Model_OrderLogo
{
    public function getLogo(ML_Shop_Model_Order_Abstract $oModel)
    {
        $aData = $oModel->get('data');
        $sCancelledStatus = MLDatabase::factory('config')->set('mpid', $oModel->get('mpid'))
            ->set('mkey', 'orderstatus.cancelled')->get('value');
        $sShippedStatus = MLDatabase::factory('config')->set('mpid', $oModel->get('mpid'))
            ->set('mkey', 'orderstatus.shipped')->get('value');

        $fulfillment = $aData['FulfillmentChannel'];
        if ($fulfillment !== 'MFN-Prime' && $fulfillment !== 'MFN' && $fulfillment !== 'Business') {
            $sLogo = 'amazon_fba_orderview';
        } else {
            // business, prime and regular orders could also be cancelled or shipped
            $suffix = '';
            if ($fulfillment === 'MFN-Prime') {
                $suffix = '_prime';
            } elseif ($fulfillment === 'Business') {
                $suffix = '_business';
            }

            $sStatus = $oModel->get('status');
            if (false) {//todo
                $sLogo = 'amazon_orderview_error';
            } elseif ($sCancelledStatus == $sStatus) {
                $sLogo = 'amazon_orderview_cancelled'.$suffix;
            } elseif ($sShippedStatus == $sStatus) {
                $sLogo = 'amazon_orderview_shipped'.$suffix;
            } else {
                $sLogo = 'amazon_orderview'.$suffix;
            }
        }

        return $sLogo . '.png';
    }
}
