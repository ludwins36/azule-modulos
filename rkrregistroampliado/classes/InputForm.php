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

class InputForm
{
    const RKR_TABLE = "rkr_ra_campos";
    const PDF_FILE = 1;
    const DOC_FILE = 2; // DOC, DOCX
    const CSV_FILE = 3;
    const PNG_FILE = 4;
    const JPEG_FILE = 5;
    const GIF_FILE = 6;
    const ZIP_FILE = 7;
    const TXT_FILE = 8; // Text file
  const XML_FILE = 9; // XML file

  public static function getFileTypeName($module)
  {
      return array(
          self::PDF_FILE => $module->l("PDF"),
          self::DOC_FILE => $module->l("DOC, DOCX"),
          self::CSV_FILE => $module->l("CSV, XLS, XLSB"),
          self::PNG_FILE => $module->l("PNG"),
          self::JPEG_FILE => $module->l("JPEG"),
          self::GIF_FILE => $module->l("GIF"),
          self::ZIP_FILE => $module->l("ZIP"),
          self::TXT_FILE => $module->l("TXT"),
          self::XML_FILE => $module->l("XML"),
      );
  }

    public static function getFileExtension()
    {
        return array(
          self::PDF_FILE => array("pdf"),
          self::DOC_FILE => array("doc", "docx"),
          self::CSV_FILE => array("csv", "xls", "xlsb"),
          self::PNG_FILE => array("png"),
          self::JPEG_FILE => array("jpeg"),
          self::GIF_FILE => array("gif"),
          self::ZIP_FILE => array("zip"),
          self::TXT_FILE => array("txt"),
          self::XML_FILE => array("xml"),
      );
    }

    public static function getFileTypeMime($id)
    {
        $collection = array(
      self::PDF_FILE => array("application/pdf"),
      self::DOC_FILE => array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/msword"),
      self::CSV_FILE => array("text/csv"),
      self::PNG_FILE => array("image/png"),
      self::JPEG_FILE => array("image/jpeg"),
      self::GIF_FILE => array("image/gif"),
      self::ZIP_FILE => array("application/zip"),
      self::TXT_FILE => array("text/plain"),
      self::XML_FILE => array("text/xml"),
    );

        return (!empty($collection[(int)$id])) ? $collection[(int)$id] : null;
    }

    public static function getForm($module, $title)
    {
        return array(
      array(
        'form' => array(
          'legend' => array(
            'title' => $module->l($title),
            'icon' => 'icon-plus',
          ),
          'input' => self::getFormField($module),
          'submit' => array('title' => $module->l('Save', 'InputForm'), 'type' => 'submit', 'class' => 'btn btn-default pull-right'),
        ),
      ),
    );
    }

    /**
     * Retourne les valeurs de la liste des ra_campos
     * @return type
     */
    public static function getInputListValues()
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $inputs = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . self::RKR_TABLE . '` WHERE `id_shop` = \'' . $id_shop . '\'');

        return $inputs;
    }

    public static function getInputList($module)
    {
        return array(
      'name' => array('title' => $module->l('Field name', 'InputForm'), 'type' => 'text', 'orderby' => false),
      'desc' => array('title' => $module->l('Description', 'InputForm'), 'type' => 'text', 'orderby' => false),
      'order' => array('title' => $module->l('Display order'), 'type' => 'text', 'orderby' => true),
      // 'active' => array('title' => $module->l('Active', 'InputForm'), 'active' => 'status', 'type' => 'bool', 'align' => 'center', 'orderby' => false),
    );
    }

    public static function getFormField($module)
    {
        $groupsOptions = array();
        foreach (Group::getGroups(Context::getContext()->language->id) as $group) {
            $groupsOptions[] = array(
        'id_option' => $group['id_group'],
        'name' => $group['name'],
        'val' => $group['id_group'],
      );
        }

        return array(
          // Rekire - Luis Jordán :: Desactivamos el select Field Type
      'type' => array(
        'label' => $module->l('Field Type', 'InputForm'),
        'name' => 'type',
        'type' => 'hidden',
        'orderby' => false,
        'options' => array(
          'id' => 'id_option',
          'name' => 'name',
          'query' => array(
            array(
              'id_option' => "text",
              'name' => $module->l('Text field', 'InputForm')
            ),
         /*
            array(
              'id_option' => "select",
              'name' => $module->l('Dropdown list (single selection)', 'InputForm')
            ),
           */

          ),
        ),
      ),
      // Rekire - Luis Jordán :: Ponemos los campos Nombre del campo y descripción disabled para que no puedan ser editados
      'name' => array('label' => $module->l('Nombre del campo', 'InputForm'), "name" => "name", 'type' => 'text', 'orderby' => false, 'readonly' => 'readonly'),
      'description' => array('label' => $module->l('Description', 'InputForm'), "name" => "description", 'type' => 'text', 'orderby' => false),
      // 'fieldsSelector' => array(
      //   'label' => $module->l('Values to select', 'InputForm'),
      //   'name' => 'fieldsSelector',
      //   'type' => 'text',
      //   'class' => 'fieldsSelector'
      // ),
      // 'groupSelector' => array(
      //   'label' => $module->l('Groups availables to select', 'InputForm'),
      //   'name' => 'groupSelector',
      //   'type' => 'checkbox',
      //   'class' => 'groupSelector',
      //   'multiple' => true,
      //   'values' => array(
      //     'query' => $groupsOptions,
      //     'id' => 'id_option',
      //     'name' => 'name'
      //   )
      // ),
      // 'filesType' => array(
      //   'type' => 'checkbox',
      //   'label' => $module->l('Accepted filetypes'),
      //   'desc' => $module->l('Choose allowed files types.'),
      //   'name' => 'filesType',
      //   'values' => array(
      //     'query' => array(
      //       array(
      //         "id" => (int) self::PDF_FILE,
      //         "name" => "PDF"
      //       ), array(
      //         "id" => (int) self::DOC_FILE,
      //         "name" => ".doc, .docx"
      //       ), array(
      //         "id" => (int) self::CSV_FILE,
      //         "name" => "CSV"
      //       ), array(
      //         "id" => (int) self::PNG_FILE,
      //         "name" => "PNG"
      //       ), array(
      //         "id" => (int) self::JPEG_FILE,
      //         "name" => "JPEG"
      //       ), array(
      //         "id" => (int) self::GIF_FILE,
      //         "name" => "GIF"
      //       ), array(
      //         "id" => (int) self::ZIP_FILE,
      //         "name" => "ZIP"
      //       ), array(
      //         "id" => (int) self::TXT_FILE,
      //         "name" => "TXT"
      //       ), array(
      //         "id" => (int) self::XML_FILE,
      //         "name" => "XML"
      //       ),
      //     ),
      //     'id' => 'id',
      //     'name' => 'name'
      //   ),
      // ),
      'order' => array(
        'label' => $module->l('Display order', 'InputForm'),
        'name' => 'order',
        'type' => 'text',
        'orderby' => false,
        'desc' => $module->l('Display order (smallest first, 0: last)'),
      ),
      'required' => array(
        'label' => $module->l('Field is required', 'InputForm'),
        'type' => (self::isPrestashop15() ? 'radio' : 'switch'),
        'name' => 'required',
        'class' => 't',
        'is_bool' => true,
        'values' => array(
          array(
            'id' => 'require_on',
            'value' => 1,
            'label' => $module->l('Enabled')
          ),
          array(
            'id' => 'require_off',
            'value' => 0,
            'label' => $module->l('Disabled')
          )
        ),
      ),
      'active' => array(
        'label' => $module->l('Enable this field', 'InputForm'),
        'type' => (self::isPrestashop15() ? 'radio' : 'switch'),
        'name' => 'active',
        'class' => 't',
        'is_bool' => true,
        'values' => array(
          array(
            'id' => 'active_on',
            'value' => 1,
            'label' => $module->l('Enabled')
          ),
          array(
            'id' => 'active_off',
            'value' => 0,
            'label' => $module->l('Disabled')
          )
        ),
      ),
    );
    }

    public static function getNewInputValues()
    {
        return array(
      'name' => Tools::safeOutput(Tools::getValue('name', null)),
      'type' => Tools::safeOutput(Tools::getValue('type', null)),
      'description' => Tools::safeOutput(Tools::getValue('description', null)),
      'fieldsSelector' => Tools::safeOutput(Tools::getValue('fieldsSelector', null)),
      'groupSelector' => Tools::safeOutput(Tools::getValue('groupSelector', null)),
      'filesType' => Tools::safeOutput(Tools::getValue('filesType', null)),
      'order' => (int) Tools::getValue('order', null),
      'required' => Tools::safeOutput(Tools::getValue('required', 1)),
      'active' => Tools::getValue('active', 1),
    );
    }

    public static function getUpdateFieldFormValues($module)
    {
        $id_shop = (int) Context::getContext()->shop->id;

        $id_field = (int) Tools::getValue('id');
        $field = Db::getInstance()->getRow('SELECT * FROM `' . $module->_prefixDb . 'ra_campos`
                                            WHERE `id` = \'' . $id_field . '\'
                                            AND `id_shop` = \'' . $id_shop . '\'');
        // Groupes et type de fichiers
        $groupesBuffer = ($field['type'] === "group") ? Tools::getValue('groupSelector', unserialize($field['fieldsSelector'])) : array();
        $filesTypeBuffer = ($field['type'] === "upload") ? Tools::getValue('fieldsSelector', unserialize($field['fieldsSelector'])) : array();
        // On populate les types de fichiers
        $filesType = array();
        foreach ($filesTypeBuffer as $fieldtype) {
            $filesType['filesType_'.$fieldtype] = true;
        }
        // On populate les groupes
        $groupes = array();
        foreach ($groupesBuffer as $groupe) {
            $filesType['groupSelector_'.$groupe] = true;
        }

        $othersFields = array(
      'name' => Tools::getValue('name', $field['name']),
      'type' => Tools::getValue('type', $field['type']),
      'description' => Tools::getValue('description', $field['desc']),
      'fieldsSelector' => ($field['type'] !== "group" && $field['type'] !== "upload") ? Tools::getValue('fieldsSelector', $field['fieldsSelector']) : "",
      'order' => Tools::getValue('order', $field['order']),
      'required' => Tools::getValue('required', $field['required']),
      'active' => Tools::getValue('active', $field['active']),
    );

        return array_merge((array)$othersFields, (array)$groupes, (array)$filesType);
    }

    public static function isPrestashop15()
    {
        return (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<'));
    }
}
