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

class AdminProductCurrenciesController extends ModuleAdminControllerCore
{
    
    public function initContent()
    {
        parent::initContent();
        $this->content.=$this->module->getContent();
        $this->context->smarty->assign(array(
            'content' => $this->content,
            )
        );

    }
}
