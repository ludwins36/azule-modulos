<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_PrepareAbstract');
class ML_Idealo_Controller_Idealo_Prepare_Form extends ML_Form_Controller_Widget_Form_PrepareAbstract {
    
    protected $aParameters = array('controller');
    
    public function __construct() {
        MLSetting::gi()->add('aJs', 'idealo.form.js');
        parent::__construct();
        MLSetting::gi()->set('idealo.activatedirectbuyconfigurl', $this->getUrl(array(
            'controller' => substr($this->getRequest('controller'), 0, strpos($this->getRequest('controller'), '_')).'_config_account')
        ));
    }
    protected function getSelectionNameValue() {
        return 'match';
    }
    
    /**
     * @todo verify addItems
     * @return boolean
     */
    protected function triggerBeforeFinalizePrepareAction() {
        $this->oPrepareList->set('verified', 'OK');
        return true;
    }
    
    public function render() {
        $this->getFormWidget();
        return $this;
    }
   
    public function checkoutField(&$aField) {
        $aField['autooptional'] = false; // from old tablestructure
    }
    
    protected function shippingTimeField (&$aField) {
        // set key to value.title
        foreach ($aField['subfields']['select']['i18n']['values'] as $sKey => $sValue) {
            if ($sKey === '__ml_lump') {
                $aField['subfields']['select']['values'][$sKey] = array(
                    'textoption' => true,
                    'title' => $sValue['title'],
                );
            } else {
                $aField['subfields']['select']['values'][$sValue['title']] = $sValue['title'];
            }
        }
    }

    protected function shippingCountryField (&$aField) {
        $aField['values'] = array();
        try{
            $aData = MagnaConnector::gi()->submitRequestCached(array(
               'ACTION' => 'GetCountries', 
               'SUBSYSTEM' => 'Core', 
               'DATA' => array(
                   'Language' => MLModul::gi()->getConfig('marketplace.lang')
               )
            ), 60 * 60 * 24 * 30);
            if($aData['STATUS'] == 'SUCCESS' && isset($aData['DATA'])){
                $aField['values'] = $aData['DATA'];
            }
        }  catch (Exception $oEx){}
         
    }
    
}