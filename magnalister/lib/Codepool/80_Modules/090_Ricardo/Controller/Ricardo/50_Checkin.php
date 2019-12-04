<?php

MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_UploadAbstract');

class ML_Ricardo_Controller_Ricardo_Checkin extends ML_Productlist_Controller_Widget_ProductList_UploadAbstract {
 
    protected function callAjaxGetItemsFee() {
        $oList = $this->getProductList();
        $oService = MLService::getAddItemsInstance();

        try {
            $itemsFeeResponse = $oService->setProductList($oList)->getItemsFee();

            if (isset($itemsFeeResponse['DATA']['TotalFee']) === false) {
                throw new MagnaException($itemsFeeResponse['ERRORS'][0]['ERRORMESSAGE']);
            }

            MLSetting::gi()->add(
                'aAjax', array(
                    'Status' => 'OK',
                    'ItemsFee' => $itemsFeeResponse['DATA']['TotalFee']
                )
            );
        } catch (Exception $oEx) {
            MLSetting::gi()->add(
                'aAjax', array(
                    'Status' => 'ERROR',
                    'Error' => $oEx->getMessage()
                )
            );
        }
    }

}
