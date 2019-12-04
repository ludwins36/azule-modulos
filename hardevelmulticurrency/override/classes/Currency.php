<?php
/**

 * HarDevel LLC.

 *

 * NOTICE OF LICENSE

 *

 * This source file is subject to the EULA

 * that is bundled with this package in the file LICENSE.txt.

 * It is also available through the world-wide-web at this URL:

 * http://hardevel.com/License.txt

 *

 * @category  HarDevel

 * @package   HarDevel_multicurrency

 * @author    HarDevel

 * @copyright Copyright (c) 2012 - 2015 HarDevel LLC. (http://hardevel.com)

 * @license   http://hardevel.com/License.txt

 */

class Currency extends CurrencyCore
{

    public function update($autodate = true, $nullValues = false)
    {
    
        $result = parent::update($autodate,$nullValues);

        if($result){
            
            include_once(_PS_MODULE_DIR_.'hardevelmulticurrency/hardevelmulticurrency.php');
            
            $hardevelmulticurrency = new hardevelmulticurrency();

            $hardevelmulticurrency->updatePrices(array(),$this->id);

        }

        return $result;
    }
}