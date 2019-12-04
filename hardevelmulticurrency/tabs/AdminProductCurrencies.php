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

include_once( PS_ADMIN_DIR.'/../classes/AdminTab.php');
include_once(_PS_MODULES_DIR_.'/hardevelmulticurrency/hardevelmulticurrency.php');

class AdminProductCurrencies extends AdminTab
{
    public function __construct()
    {
        $this->className = 'AdminProductCurrencies';
        $this->lang = false;
        $this->edit = true;
        $this->delete = true;
        $this->table = 'hardevel_multicurrency';

        parent::__construct();
    }

    public function display()
    {
        $hardevelmulticurrency=new hardevelmulticurrency();
        echo $hardevelmulticurrency->getContent();
    }
}