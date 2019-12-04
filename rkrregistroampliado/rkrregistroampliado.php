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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classes/InputForm.php');
require_once(dirname(__FILE__) . '/classes/License.php');
require_once("lib/upload.php");

class rkrregistroampliado extends Module
{
    protected $db;
    public $_prefixDb;
    public $_prefixOnly;
    protected $_successMsg;
    protected $_errorMsg;


    public function __construct()
    {
        $this->name = 'rkrregistroampliado';
        $this->version = '1.4';
        $this->tab = 'front_office_features';
        $this->author = 'Rekire - Tecnología web';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7.9');
        $this->module_key = "";
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->controllers = array('display');
        $this->display = 'view';
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        // Others parameters of interest.
        // $this->limited_countries = array('es', 'pt');
        // $this->active = 1;

        $this->carpeta = _PS_ROOT_DIR_."/override/classes/form";
        if (!file_exists($this->carpeta)) {
          mkdir($this->carpeta, 0775, true);
        }

        parent::__construct();

        $this->displayName = $this->l('RKR - Registro ampliado.');
        $this->description = $this->l('Añade campos de dirección en el formulario de registro.');
        $this->db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $this->_prefixOnly = "rkr_";
        $this->_prefixDb = _DB_PREFIX_ . $this->_prefixOnly;
    }


    public function install()
    {
        if (file_exists(_PS_ROOT_DIR_."/app/cache/dev/class_index.php")){
            unlink(_PS_ROOT_DIR_."/app/cache/dev/class_index.php");
        }
        if (file_exists(_PS_ROOT_DIR_."/app/cache/prod/class_index.php")){
            unlink(_PS_ROOT_DIR_."/app/cache/prod/class_index.php");
        }

        if (parent::install() == false) {
            return false;
        }

        $installed = true;
        $installed &= $this->createTables();

        $this->installFixes();


        $installed &= $this->registerHook('actionCustomerAccountAdd');
        $installed &= $this->registerHook('displayAdminCustomers');

        $installed &= $this->registerHook('displayCustomerAccount');

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            // Version 1.7 et supérieur
            $installed &= $this->registerHook('displayCustomerAccountFormTop');
            $installed &= $this->registerHook('actionSubmitAccountBefore');
            // Bug prestashop 1.7.0.1,

            if (version_compare(_PS_VERSION_, '1.7.1', '<')) {
                $installed &= $this->registerHook('actionCustomerAccountUpdate');
            } else {
                $installed &= $this->registerHook('actionCustomerAccountAdd');
            }
        } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
            // Version 1.6*
            $installed &= $this->registerHook('createAccountForm');
            $installed &= $this->registerHook('actionBeforeSubmitAccount');
        } else {
            // Version 1.5
            $installed &= $this->registerHook('createAccountForm');
            $installed &= $this->registerHook('actionBeforeSubmitAccount');
        }

        Module::disable();

        // Aplicamos la conversión de variables.
        $var_list['{url}'] = $_SERVER['SERVER_NAME'];
        // Notificación
        $all_contacts = array('guardias@rekire.com','granilusion.com@gmail.com');

        $this->checkRKR = Mail::Send(
            $this->context->language->id,
            'notifications',
            'Nueva instalación del módulo RKR Registro ampliado',
            $var_list,
            $all_contacts,
            'RKR Módulos Prestashop',
            null,
            null,
            '',
            null,
            dirname(__FILE__).'/mails/'
          );

        return true;
    }

    public function uninstall()
    {
        // Eliminamos el override si existe.
        if (file_exists(_PS_ROOT_DIR_."/override/classes/form/CustomerAddressForm.php")){
            unlink(_PS_ROOT_DIR_."/override/classes/form/CustomerAddressForm.php");
        }

        if (!parent::uninstall() || !Configuration::deleteByName('rkrregistroampliado')){
          return false;
        }

        // return (bool) $this->db->execute('
    	// 		DROP TABLE IF EXISTS `' . $this->_prefixDb . 'licenserkr`;
    	// 	');

      return true;
    }


    public function hookDisplayCustomerAccountFormTop($params)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $fields = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop . ' ORDER BY if (`order` = 0, 99999, `order`)');

        $this->smarty->assign(array(
          "fields" => $fields,
          "groupesList" => Group::getGroups(Context::getContext()->language->id),
          "filetypeName" => InputForm::getFileTypeName($this),
      ));

        $this->context->controller->addCSS(($this->_path) . 'views/css/jquery.datetimepicker.css');
        $this->context->controller->addJS(($this->_path) . 'views/js/jquery.datetimepicker.js');
        $this->context->controller->addCSS(($this->_path) . 'views/css/multi-select.css');
        $this->context->controller->addJS(($this->_path) . 'views/js/jquery.multi-select.js');
        $this->context->controller->addJS(($this->_path) . 'views/js/rkr_form.js');

        $licensechecked = $this->checkLicenseKey();
        if($licensechecked["respuesta"] == 'be97eb2f6e1bc174f93c77a39aa57b1d' OR $licensechecked["respuesta"] == 'b8a8cccc5724c81d3d4340f622c975ef'){
          return $this->display(__FILE__, '/views/templates/hook/createAccountForm17.tpl');
        }
    }


    public function hookCreateAccountForm($params)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $fields = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop . ' ORDER BY if (`order` = 0, 99999, `order`)');

        $this->smarty->assign(array(
            "fields" => $fields,
            "groupesList" => Group::getGroups(Context::getContext()->language->id),
            "filetypeName" => InputForm::getFileTypeName($this),
        ));

        $this->context->controller->addCSS(($this->_path) . 'views/css/jquery.datetimepicker.css');
        $this->context->controller->addJS(($this->_path) . 'views/js/jquery.datetimepicker.js');
        $this->context->controller->addCSS(($this->_path) . 'views/css/multi-select.css');
        $this->context->controller->addJS(($this->_path) . 'views/js/jquery.multi-select.js');
        $this->context->controller->addJS(($this->_path) . 'views/js/rkr_form.js');

        if (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<')) {
            $licensechecked = $this->checkLicenseKey();
            if($licensechecked["respuesta"] == 'be97eb2f6e1bc174f93c77a39aa57b1d' OR $licensechecked["respuesta"] == 'b8a8cccc5724c81d3d4340f622c975ef'){
            return $this->display(__FILE__, '/views/templates/hook/createAccountForm15.tpl');
          }
        }

        $licensechecked = $this->checkLicenseKey();
        if($licensechecked["respuesta"] == 'be97eb2f6e1bc174f93c77a39aa57b1d' OR $licensechecked["respuesta"] == 'b8a8cccc5724c81d3d4340f622c975ef'){
          return $this->display(__FILE__, '/views/templates/hook/createAccountForm16.tpl');
        }
    }


    public function hookActionSubmitAccountBefore($params)
    {
        return $this->hookActionBeforeSubmitAccount($params);
    }


    public function hookActionBeforeSubmitAccount($params)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $fields = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop);
        $success = true;

        $licensechecked = $this->checkLicenseKey();
        if($licensechecked["respuesta"] == 'be97eb2f6e1bc174f93c77a39aa57b1d' OR $licensechecked["respuesta"] == 'b8a8cccc5724c81d3d4340f622c975ef'){

        foreach ($fields as $field) {
            if (!$field['required']) {
                continue;
            }


            if ($field['type'] === "upload" && !empty($_FILES['rkr_' . (int) $field['id']])) {
                continue;
            }

            elseif ($field['type'] === "group") {
                $groupes_allowed = unserialize($field["fieldsSelector"]);
                $completed = false;
                foreach ($groupes_allowed as $group_id) {
                    if (Tools::getValue('rkr_'. (int)$field['id'] .'_'. $group_id)) {
                        $completed = true;
                    }
                }

                if ($completed === true) {
                    continue;
                }
            }

            elseif (Tools::getValue('rkr_' . (int) $field['id'])) {
                continue;
            }

            // $this->context->controller->errors[] = $field['name'] . " " . $this->l('is required');
            // Cargamos la traducción desde el fichero de idiomas.
            $this->context->controller->errors[] = $this->l($field['name']) . " " . $this->l('is required');

            $success = false;
        }
      }

      return $success;
    }


    public function hookActionCustomerAccountUpdate($params)
    {
        if (!empty($params['customer'])) {
            $params['newCustomer'] = $params['customer'];
        }
        return $this->hookActionCustomerAccountAdd($params);
    }


    public function hookActionCustomerAccountAdd($params)
    {
        $cust = $params['newCustomer'];
        if (empty($cust)) {
            return;
        }
        $id_shop = (int) Context::getContext()->shop->id;

        $fields = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop);
        $insertData = array();

        foreach ($fields as $field) {
            // ra_campos upload
            if ($field['type'] === "upload" && !empty($_FILES['rkr_' . (int) $field['id']])) {
                // Upload
                $result_upload = $this->upload_file($_FILES['rkr_' . (int) $field['id']], $field);
                // Upload reussi
                if (empty($result_upload['errors'])) {
                    $response = $result_upload['filename'];
                    $insertData[] = array(
                    'id_customer' => (int) $cust->id,
                    'id_champ' => (int) $field['id'],
                    'reponse' => pSQL($response),
                );
                }
            }
            /* Cas d'un ra_campos groupe */
            elseif ($field['type'] === 'group') {
                $groupes_allowed = unserialize($field["fieldsSelector"]);
                $groupes = array();
                foreach ($groupes_allowed as $group_id) {
                    if (Tools::getValue('rkr_'. (int)$field['id'] .'_'. $group_id)) {
                        $groupes[] = $group_id;
                    }
                }

                $response = serialize($groupes);
                $insertData[] = array(
                  'id_customer' => (int) $cust->id,
                  'id_champ' => (int) $field['id'],
                  'reponse' => pSQL($response),
              );
                // Ajoute les groupes à l'utilisateur
                $cust->updateGroup($groupes);
            } elseif (Tools::getValue('rkr_' . (int) $field['id'])) {
                $response = null;
                if ($field['type'] === "dropdown_multi") {
                    $response = serialize(Tools::getValue('rkr_' . (int) $field['id']));
                } elseif ($field['type'] === 'group') {
                    $groupes = array();
                    for ($i=0; $i<= 99; $i++) {
                        if (Tools::getValue('groupSelector_'. $i)) {
                            $groupes[] = $i;
                        }
                    }
                    $response = serialize($groupes);
                } else {
                    $response = Tools::getValue('rkr_' . (int) $field['id']);
                }

                $insertData[] = array(
                    'id_customer' => (int) $cust->id,
                    'id_champ' => (int) $field['id'],
                    'reponse' => pSQL($response),
                );
            }
        }

        return Db::getInstance()->insert($this->_prefixOnly . 'reponses', $insertData);
    }


    public function hookDisplayAdminCustomers($params)
    {
        $update = null;
        if (Tools::isSubmit('submitUpdateResponses')) {
            $update = $this->postUpdateResponses($params['id_customer']);
        }

        $id_shop = (int) Context::getContext()->shop->id;
        $bufferResponses = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'reponses WHERE id_customer = ' . (int) $params['id_customer']);
        $responses = array();
        foreach ($bufferResponses as $response) {
            $responses[$response['id_champ']] = $response;
        }

        $this->context->controller->addCSS(($this->_path) . 'views/css/jquery.datetimepicker.css');
        $this->context->controller->addJS(($this->_path) . 'views/js/jquery.datetimepicker.js');
        $this->smarty->assign(array(
            "bootstrap" => (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<') || $this->bootstrap === false) ? false : true,
            "fields" => $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop),
            "groupesList" => Group::getGroups(Context::getContext()->language->id),
            "responses" => $responses,
            "filetypeName" => InputForm::getFileTypeName($this),
            "rkr_uploads_dir" => _MODULE_DIR_ . '/rkrregistroampliado/uploads',
            "update" => $update
        ));

        return $this->display(__FILE__, '/views/templates/hook/AdminCustomers.tpl');
    }


    public function hookDisplayCustomerAccount($params)
    {


        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $licensechecked = $this->checkLicenseKey();
            if($licensechecked["respuesta"] == 'be97eb2f6e1bc174f93c77a39aa57b1d' OR $licensechecked["respuesta"] == 'b8a8cccc5724c81d3d4340f622c975ef'){
                return $this->display(__FILE__, '/views/templates/hook/displayCustomerAccount17.tpl');
            }
        } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $licensechecked = $this->checkLicenseKey();
            if($licensechecked["respuesta"] == 'be97eb2f6e1bc174f93c77a39aa57b1d' OR $licensechecked["respuesta"] == 'b8a8cccc5724c81d3d4340f622c975ef'){
                return $this->display(__FILE__, '/views/templates/hook/displayCustomerAccount16.tpl');
            }
        }

        $update = null;
        $cust_id = (int)$this->context->cookie->id_customer;
        if (empty($cust_id)) {
            return;
        }

        if (Tools::isSubmit('submitIdentity')) {
            $update = $this->postUpdateResponses($cust_id, false);
        }

        $id_shop = (int) Context::getContext()->shop->id;
        $bufferResponses = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'reponses WHERE id_customer = ' . (int) $cust_id);
        $responses = array();
        foreach ($bufferResponses as $response) {
            $responses[$response['id_champ']] = $response;
        }

        $this->context->controller->addCSS(($this->_path) . 'views/css/jquery.datetimepicker.css');
        $this->context->controller->addJS(($this->_path) . 'views/js/jquery.datetimepicker.js');
        $this->smarty->assign(array(
            "bootstrap" => (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<') || $this->bootstrap === false) ? false : true,
            "fields" => $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop),
            "groupesList" => Group::getGroups(Context::getContext()->language->id),
            "filetypeName" => InputForm::getFileTypeName($this),
            "responses" => $responses,
            "update" => $update
        ));

        $licensechecked = $this->checkLicenseKey();
        if($licensechecked["respuesta"] == 'be97eb2f6e1bc174f93c77a39aa57b1d' OR $licensechecked["respuesta"] == 'b8a8cccc5724c81d3d4340f622c975ef'){
          return $this->display(__FILE__, '/views/templates/hook/displayCustomerIdentityForm15.tpl');
        }
    }

    public function getContent()
    {

        $this->installFixes();


        if (Tools::isSubmit('submitNewField')) {
            $this->postProcessNewInput();
        } elseif (Tools::isSubmit('submitUpdateField') && Tools::isSubmit('id')) {
            $this->postProcessUpdateField();
        } elseif (Tools::isSubmit('deleterkr_ra_campos') && Tools::isSubmit('id')) {
            $this->postProcessDeleteInput(Tools::getValue('id'));
        } elseif (Tools::isSubmit('deleterkr_ra_campos') && Tools::isSubmit('id')) {
            $this->postProcessDeleteInput(Tools::getValue('id'));
        } elseif (Tools::isSubmit('statusrkr_ra_campos') && Tools::isSubmit('id')) {
            $this->postProcessUpdateFieldStatus(Tools::getValue('id'));
        }

        // Check the license key.
        $license_key = $this->getLicenseKey();
        $checkLicense = $this->checkLicenseKey();

        // Check real license.
        if($license_key){
          $this->context->smarty->assign([
              'license_key' => $license_key['license'],
              'activated' => $checkLicense['msg'],
              'color' => $checkLicense['color']
          ]);
        }

        // Rekire - Luis Jordán :: La única finalidad será para pintar la license key
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/getcontent.tpl');

        // Rekire - Luis Jordán :: Formulario de edición de campos,
        if (Tools::isSubmit('newInput')) {
            $output .= $this->renderForm(InputForm::getForm($this, 'Add new field'), InputForm::getNewInputValues(), 'submitNewField', true, false);
        } elseif (Tools::isSubmit('updaterkr_ra_campos') && Tools::isSubmit('id')) {
            $form_structure = InputForm::getForm($this, 'Actualización de valores', true);
            $form = $this->renderForm($form_structure, InputForm::getUpdateFieldFormValues($this), 'submitUpdateField', true, false, true);
            $output = $output . $form;
        } elseif (Tools::isSubmit('insert_licensekey') ) {
            $output .= $this->setLicenseKey($_POST['licensekey']);

            if($license_key){
                $checkLicense = $this->checkLicenseKey();

                $this->context->smarty->assign([
                  'license_key' => $_POST['licensekey'],
                  'activated' => $checkLicense['msg'],
                  'color' => $checkLicense['color']
                ]);
            }
            $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/getcontent.tpl');
        }

        $output .= $this->renderInputList();

        $this->context->controller->addCSS(($this->_path) . 'views/css/content.css');
        $this->context->controller->addCSS(($this->_path) . 'views/css/jquery.tagsinput.css');
        $this->context->controller->addJS(($this->_path) . 'views/js/jquery.tagsinput.js');

        return $output;
    }

    public function getLicenseKey(){
      $result = Db::getInstance()->executeS('SELECT license FROM ' . $this->_prefixDb . 'licenserkr');

      return $result[0];
    }

    public function setLicenseKey($license){

      $result = $this->getLicenseKey();

      if($result){
        // UPDATE
        $result = Db::getInstance()->update($this->_prefixOnly . 'licenserkr', array(
            'license' => pSQL($license)
        ), "id = 1");

        return 1;
      }
      else{
          $result = Db::getInstance()->insert($this->_prefixOnly . 'licenserkr', array(
              'license' => pSQL($license),
          ));

          return 2;
      }

      return 0;
    }

    public function checkLicenseKey(){
      $license_key_site = $this->getLicenseKey();

      // Comprobamos que la licencia sea correcta.
      // $license_key = License::checkLicenseKeyPlus($license_key_site);
      $license_key_ob = new License;
      $license_key = $license_key_ob->checkLicenseKeyPlus($license_key_site);
      // var_dump($license_key);
      // die();

      return $license_key;

    }

    protected function renderForm($form, $form_values, $action, $cancel = false, $back_url = false, $update = false)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = $action;

        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        if ($update == true) {
            $helper->currentIndex .= '&id=' . (int) Tools::getValue('id');
        }

        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $form_values,
            'id_language' => $this->context->language->id,
            'languages' => $this->context->controller->getLanguages(),
            'back_url' => $back_url,
            'show_cancel_button' => $cancel,
        );

        return $helper->generateForm($form);
    }


    protected function renderInputList()
    {
        $helper = new HelperList();

        $helper->title = $this->l('Listado de campos');
        $helper->table = "rkr_ra_campos";
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->identifier = 'id';

        // Rekire - Luis Jordán :: Dejamos tan solo la opción de modificar.
        // $helper->actions = array('edit', 'delete');
        $helper->actions = array('edit');

        // Rekire - Luis Jordán :: Aquí es donde se pintan los valores de la lista de filtros.
        $values = InputForm::getInputListValues($this);
        $helper->listTotal = count($values);
        $helper->tpl_vars = array('show_filters' => false);


        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        return $helper->generateList($values, InputForm::getInputList($this));
    }

    protected function renderFrontForm($form, $form_values, $action, $cancel = false, $back_url = false, $update = false)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = $action;

        $helper->currentIndex = $this->context->link->getPageLink('authentication', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        if ($update == true) {
            $helper->currentIndex .= '&id=' . (int) Tools::getValue('id');
        }

        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $form_values,
            'id_language' => $this->context->language->id,
            'languages' => $this->context->controller->getLanguages(),
            'back_url' => $back_url,
            'show_cancel_button' => $cancel,
        );

        return $helper->generateForm($form);
    }

    protected function postProcessNewInput()
    {
        if ($this->isNewInputValid() == true) {
            $type = Tools::getValue('type');
            $description = Tools::getValue('description');
            $name = Tools::getValue('name');
            $order = Tools::getValue('order', 0);
            if ($type === 'group') {
                $groupes = array();
                for ($i=0; $i<= 99; $i++) {
                    if (Tools::getValue('groupSelector_'. $i)) {
                        $groupes[] = $i;
                    }
                }
                $fieldsSelector = serialize($groupes);
            } elseif ($type === 'upload') {
                $fileTypes = array();
                for ($i=0; $i<= 99; $i++) {
                    if (Tools::getValue('filesType_'. $i)) {
                        $fileTypes[] = $i;
                    }
                }
                $fieldsSelector = serialize($fileTypes);
            } else {
                $fieldsSelector = Tools::getValue('fieldsSelector');
            }
            $required = (int) Tools::getValue('required');
            $id_shop = (int) Context::getContext()->shop->id;

            $result = Db::getInstance()->insert($this->_prefixOnly . 'ra_campos', array(
                'id_shop' => (int) $id_shop,
                'name' => pSQL($name),
                'desc' => pSQL($description),
                'type' => pSQL($type),
                'order' => (int) $order,
                'active' => true,
                'required' => (bool) $required,
                'fieldsSelector' => pSQL($fieldsSelector),
            ));

            if ($result) {
                $this->_successMsg[] = $this->l('New input added successfully');
            } else {
                $this->_errorMsg[] = $this->l('Error: form is incomplete');
            }

            return $result;
        }
    }

    protected function postProcessUpdateField()
    {
        if (Tools::isSubmit('id') == false) {
            return false;
        }

        $type = Tools::getValue('type');
        $description = Tools::getValue('description');
        $name = Tools::getValue('name');

        if ($type === 'group') {
            $groupes = array();
            for ($i=0; $i<= 99; $i++) {
                if (Tools::getValue('groupSelector_'. $i)) {
                    $groupes[] = $i;
                }
            }
            $fieldsSelector = serialize($groupes);
        } elseif ($type === 'upload') {
            $fileTypes = array();
            for ($i=0; $i<= 99; $i++) {
                if (Tools::getValue('filesType_'. $i)) {
                    $fileTypes[] = $i;
                }
            }
            $fieldsSelector = serialize($fileTypes);
        } else {
            $fieldsSelector = Tools::getValue('fieldsSelector');
        }
        $order = Tools::getValue('order', 0);
        $required = Tools::getValue('required');
        $active = (int) Tools::getValue('active');
        $id_shop = (int) Context::getContext()->shop->id;
        $id_field = (int) Tools::getValue('id');

        $result = Db::getInstance()->update($this->_prefixOnly . 'ra_campos', array(
            'id_shop' => (int) $id_shop,
            'name' => pSQL($name),
            'desc' => pSQL($description),
            'type' => pSQL($type),
            'order' => (int) $order,
            'active' => (bool) $active,
            'required' => (bool) $required,
            'fieldsSelector' => pSQL($fieldsSelector),
        ), "id = " . (int) $id_field . "");

        if ($result) {
            $this->_successMsg[] = $this->l('New input added successfully');
        } else {
            $this->_errorMsg[] = $this->l('Error: form is incomplete');
        }

        return $result;
    }

    protected function postProcessUpdateFieldStatus()
    {
        if (Tools::isSubmit('id') == false) {
            return false;
        }

        $id_field = (int) Tools::getValue('id');

        Db::getInstance()->execute('UPDATE ' . $this->_prefixDb . 'ra_campos SET `active` = IF (`active`, 0, 1) WHERE `id` = \'' . (int) $id_field . '\'');

        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
                . '&token=' . Tools::getAdminTokenLite('AdminModules'));
    }

    protected function isNewInputValid()
    {
        if (Tools::isSubmit('type') == true && Tools::isSubmit('description') == true && Tools::isSubmit('name') == true) {
            return true;
        }

        return false;
    }

    protected function postProcessDeleteInput($id_field)
    {
        Db::getInstance()->execute('DELETE FROM ' . $this->_prefixDb . 'ra_campos WHERE `id` = \'' . (int) $id_field . '\'');

        return Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false)
                        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
                        . '&token=' . Tools::getAdminTokenLite('AdminModules'));
    }

    public function postUpdateResponses($id_cust, $backoffice = true)
    {
        $cust = new Customer((int) $id_cust);
        if (empty($id_cust) || empty($cust->id)) {
            return;
        }

        $id_shop = (int) Context::getContext()->shop->id;
        $fields = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'ra_campos WHERE active = 1 and id_shop = ' . (int) $id_shop);
        $bufferResponses = $this->db->executeS('SELECT * FROM ' . $this->_prefixDb . 'reponses WHERE id_customer = ' . (int) $cust->id);

        $responses = array();
        foreach ($bufferResponses as $buff) {
            $responses[(int) $buff['id_champ']] = $buff;
        }

        $return = true;
        foreach ($fields as $field) {

            if  ($field['type'] === 'group') {
                $groupes_allowed = unserialize($field["fieldsSelector"]);
                $groupes = array();
                foreach ($groupes_allowed as $group_id) {
                    if (Tools::getValue('rkr_'. (int)$field['id'] .'_'. $group_id)) {
                        $groupes[] = $group_id;
                    }
                }

                $response = serialize($groupes);
                $this->save_field($response, $responses, $field, $cust->id);

                $cust->updateGroup($groupes);
            } elseif (Tools::getValue('rkr_' . (int) $field['id']) !== false) {

                if ($backoffice === false) {
                }

                $response = null;
                if ($field['type'] === "dropdown_multi") {
                    $response = serialize(Tools::getValue('rkr_' . (int) $field['id']));
                } else {
                    $response = Tools::getValue('rkr_' . (int) $field['id']);
                }

                $this->save_field($response, $responses, $field, $cust->id);
            }

            elseif ($field['type'] === "checkbox") {
                $this->save_field("", $responses, $field, $cust->id);
            }
        }

        return $return;
    }




    protected function save_field($response, $old_responses, $field, $customer_id)
    {
        if (!empty($old_responses[(int) $field['id']])) { // Mise à jour du champ
            Db::getInstance()->update($this->_prefixOnly . 'reponses', array(
          'reponse' => pSQL($response),
        ), 'id = ' . (int) $old_responses[(int) $field['id']]['id']);
        } else { // Ajout d'un nouveau champ
            Db::getInstance()->insert($this->_prefixOnly . 'reponses', array(
          'id_customer' => (int) $customer_id,
          'id_champ' => (int) $field['id'],
          'reponse' => pSQL($response),
        ));
        }
    }


    protected function installFixes()
    {

        $this->fixSubmitAccountBeforeAliasMissing();
    }

    protected function fixSubmitAccountBeforeAliasMissing()
    {
        if (version_compare(_PS_VERSION_, '1.7.1', '<')) {
            return false;
        }
        $exist = $this->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'hook_alias
                                             WHERE alias="actionSubmitAccountBefore"
                                             AND name="actionBeforeSubmitAccount"');
        if (empty($exist)) {
            $this->db->execute('
            insert into `' . _DB_PREFIX_ . 'hook_alias` (alias, name)
                   values (\'actionSubmitAccountBefore\', \'actionBeforeSubmitAccount\')
        ');
        }
        return true;
    }



    protected function createTables()
    {

        $res = true;
         // Rekire - Luis Jordán :: Creamos la tabla donde se guardarán los campos.
        $res &= (bool) $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . $this->_prefixDb . 'ra_campos` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `id_shop` INT NOT NULL,
                `name` VARCHAR(255) NULL,
                `desc` VARCHAR(255) NULL,
                `type` VARCHAR(45) NULL,
                `fieldsSelector` TEXT NULL,
                `order` INT NULL,
                `active` BOOLEAN DEFAULT FALSE,
                `required` BOOLEAN DEFAULT FALSE,
                ' . /* `form_id` INT NOT NULL, */ '
                PRIMARY KEY (`id`))
            ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        // Rekire - Luis Jordán :: Creamos la tabla donde se guardarán los resultados.
        $res &= (bool) $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . $this->_prefixDb . 'reponses` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `reponse` TEXT NULL,
                `id_champ` INT NOT NULL,
                `id_customer` INT NOT NULL,
                PRIMARY KEY (`id`))
            ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        $res &= (bool) $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . $this->_prefixDb . 'licenserkr` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `license` TEXT NULL,
                PRIMARY KEY (`id`))
            ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        // Rekire - Luis Jordán :: Eliminamos los campos antiguos si tuviera.
        $res &= (bool) $this->db->execute('
           DELETE FROM `' . $this->_prefixDb . 'ra_campos`;
        ');

        // Comprobamos que no exista ningún registro en tabla
        $registros = $this->db->executeS('SELECT count(*) as total FROM ' . $this->_prefixDb . 'licenserkr');
        if($registros[0]['total'] <= 0){
            $license_demo = License::setLicenseDemo();
             $res &= (bool) $this->db->execute('
                INSERT INTO `' . $this->_prefixDb . 'licenserkr` (`license`)
                VALUES
                    ("'.$license_demo.'");
            ');
        }

        // Rekire - Luis Jordán :: Insertamos los campos para usar en dirección.
         $res &= (bool) $this->db->execute('
            INSERT INTO `' . $this->_prefixDb . 'ra_campos` (`id`, `id_shop`, `name`, `desc`, `type`, `fieldsSelector`, `order`, `active`, `required`)
            VALUES
                (1, 1, \'Address\', \'\', \'text\', \'\', 1, 0, 0),
                (2, 1, \'Address Complement\', \'\', \'text\', \'\', 2, 0, 0),
                (3, 1, \'Zip/Postal Code\', \'\', \'text\', \'\', 3, 0, 0),
                (4, 1, \'City\', \'\', \'text\', \'\', 4, 0, 0),
                (5, 1, \'Other\', \'\', \'text\', \'\', 5, 0, 0),
                (6, 1, \'Phone\', \'\', \'text\', \'\', 6, 1, 0),
                (7, 1, \'Mobile phone\', \'\', \'text\', \'\', 7, 1, 0),
                (8, 1, \'VAT number\', \'\', \'text\', \'\', 8, 1, 0),
                (9, 1, \'Identification number\', \'\', \'text\', \'\', 9, 1, 0),
                (10, 1, \'Company\', \'\', \'text\', \'\', 10, 1, 0);
        ');

        return $res;
    }

    /*
     * borramos las tablas
     */

    protected function deleteTables()
    {

        return (bool) $this->db->execute('
    			DROP TABLE IF EXISTS `' . $this->_prefixDb . 'ra_campos`, `' . $this->_prefixDb . 'reponses`;
    		');
    }
}
