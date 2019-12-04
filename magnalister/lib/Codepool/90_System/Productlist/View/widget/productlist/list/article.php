<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
    if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {
        if ($this->renderVariantsError() || $this->renderVariants()) { 
            $aVariants=$oList->getVariants($oProduct);
        }
        if($this->renderVariantsError() && $this instanceof ML_Productlist_Controller_Widget_ProductList_Selection){
            $iCountVariants=0;
            $aMessages=array();
            foreach($aVariants as $oVariant){
                $iCountVariants+=(int)$this->productSelectable($oVariant, false);
                foreach(MLMessage::gi()->getObjectMessages($oVariant) as $sMessage){
                    $aMessages[$sMessage]=isset($aMessages[$sMessage])?$aMessages[$sMessage]+1:1;
                }
            }
            if(!empty($aMessages)){
                $sAddMessage='<ul>';
                foreach($aMessages as $sMessage=>$iCount){
                    $sAddMessage.='<li>'.$iCount.'&nbsp;*&nbsp;'.$sMessage.'</li>';
                }
                $sAddMessage.='</ul>';
                MLMessage::gi()->addObjectMessage($oProduct, MLI18n::gi()->get('Productlist_ProductMessage_sVariantsHaveError').$sAddMessage);
            }
        }
        $this->includeView('widget_productlist_list_mainarticle',array('oList'=>$oList,'oProduct'=>$oProduct));
        if($this->renderVariants()){
            //in safari(10.0.2) there is problem to show dotted style for several td, here we tried to solve it to show dotted only for one td and solved the problem
                
            if ($oProduct->exists() && count($aVariants) > 0 && count(MLMessage::gi()->getObjectMessages($oProduct))==0) { ?>
                <tr class="ml-h-seperator">
                            <td></td>
                            <td class="ml-hl-dotted" colspan="<?php echo count($oList->getHead())+($this instanceof ML_Productlist_Controller_Widget_ProductList_Selection?1:0); ?>"></td>
                </tr>
                <tr class="child">
                            <td></td>
                            <td class="next-child" colspan="<?php echo count($oList->getHead())+($this instanceof ML_Productlist_Controller_Widget_ProductList_Selection?1:0); ?>"><?php echo $this->__("Productlist_Variation_Label") ?></td>
                </tr>
                <?php
                $oCurrentProduct = $oProduct;
                //in safari(10.0.2) there is problem to show dotted style for several td, here we tried to solve it to show dotted only for one td and solved the problem
                foreach ($aVariants as $oProduct) {
                    ?>
                        <tr class="ml-h-seperator">
                                    <td></td>
                                    <td class="ml-hl-dotted" colspan="<?php echo count($oList->getHead())+($this instanceof ML_Productlist_Controller_Widget_ProductList_Selection?1:0); ?>"></td>
                        </tr>
                        <tr class="child">
                            <td></td>
                            <td class="next-child" colspan="<?php echo count($oList->getHead())+($this instanceof ML_Productlist_Controller_Widget_ProductList_Selection?1:0); ?>"></td>
                        </tr>
                    <?php
                    $this->includeView('widget_productlist_list_variantarticle',array('oList'=>$oList,'oProduct'=>$oProduct)); 
                    foreach ($oList->additionalRows($oProduct) as $sAddRow) {
                        $this->includeView('widget_productlist_list_variantarticleadditional_'.$sAddRow, array('oProduct'=>$oProduct,'aAdditional'=>isset($aAdditional)?$aAdditional:array()));
                    }
                }
            }
        }
    }
