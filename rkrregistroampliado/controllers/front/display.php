<?php
/*
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by Rekire.com
*
* @author     Rekire - Tecnología web
* @copyright  2018 Rekire.com All right reserved
* @license    Rekire
* @contact    info@rekire.com
*
*/

class RkrRegistroAmpliadoDisplayModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $_prefixDb;
    public $_prefixOnly;

    public function init()
    {
        parent::init();
        $this->customer = $this->context->customer;
        $this->_prefixOnly = "rkr_";
        $this->_prefixDb = _DB_PREFIX_ . $this->_prefixOnly;
    }

    public function initContent()
    {
        parent::initContent();

        $update = null;
        $cust_id = (int) $this->customer->id;
        if (empty($cust_id)) {
            return;
        }

        if (Tools::isSubmit('submitCustomData') || Tools::isSubmit('submitIdentity')) {
            $update = $this->module->postUpdateResponses($cust_id, false);
        }

        $id_shop = (int) Context::getContext()->shop->id;
        $bufferResponses = Db::getInstance()->executeS('SELECT * FROM ' . $this->_prefixDb . 'reponses WHERE id_customer = ' . (int) $cust_id);
        $responses = array();
        foreach ($bufferResponses as $response) {
            $responses[$response['id_champ']] = $response;
        }

        $this->context->smarty->assign(array(
      "fields" => Db::getInstance()->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop),
      "responses" => $responses,
      "update" => $update,
      "groupesList" => Group::getGroups(Context::getContext()->language->id),
      "filetypeName" => InputForm::getFileTypeName($this->module),
      "this_path" => $this->module->getPathUri(),
      "this_path_ssl" => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
    ));
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->setTemplate('module:rkrregistroampliado/views/templates/front/settings17.tpl');
        } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $this->setTemplate('settings16.tpl');
        } else {
            $this->setTemplate('settings.tpl');
        }
    }

    public function postProcess()
    {
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addCSS(_THEME_CSS_DIR_ . 'identity.css');
        $this->path = __PS_BASE_URI__ . 'modules/rkrregistroampliado/';

        // Injection des CSS
        $this->context->controller->addCSS(($this->path) . 'views/css/jquery.datetimepicker.css');
        $this->context->controller->addCSS(($this->path) . 'views/css/multi-select.css');
        $this->context->controller->addCSS(($this->path) . 'views/css/content.css');
        // Injection des JS
        $this->context->controller->addJS(($this->path) . 'views/js/jquery.datetimepicker.js');
        $this->context->controller->addJS(($this->path) . 'views/js/jquery.multi-select.js');
        $this->context->controller->addJS(($this->path) . 'views/js/rkr_form.js');
    }
}
