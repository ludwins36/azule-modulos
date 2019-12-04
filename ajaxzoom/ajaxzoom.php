<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, ajaxzoom.php
*
*  Copyright: Copyright (c) 2010-2019 Vadim Jacobi
*  License Agreement: https://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.21.0
*  Date: 2019-07-22
*  Review: 2019-07-22
*  URL: https://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: https://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license   https://www.ajax-zoom.com/index.php?cid=download
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

$current_dir = dirname(__FILE__);

if (!defined('_PS_CORE_DIR_')) {
    define('_PS_CORE_DIR_', realpath($current_dir.'/..'));
}

class AjaxZoom extends Module
{
    private $_html = '';
    private $fields_list = array();
    public static $tmpdir;
    public static $arcdir;
    public static $axzmh;
    public static $zoom;
    private $max_image_size = 99999999;
    private $az_pic_dirs = array('cache', 'tmp', 'zoomgallery', 'zoommap', 'zoomthumb', 'zoomtiles_80', 'json');

    public $product_output = false;
    public $az_opt_prefix;
    public $ps_version_15;
    public $ps_version_16;
    public $ps_version_17;
    public $mouseover_settings;
    public $categories;
    public $config_vendor = array();
    public $extended_product_settings = false;

    public function __construct()
    {
        $this->name = 'ajaxzoom';
        $this->tab = 'front_office_features';
        $this->version = '1.21.0';
        $this->version_date = '2019-07-22';
        $this->author = 'AJAX-ZOOM';
        $this->limited_countries = array();
        $this->module_key = 'ae0ae095e6e3d4af68e4a09f73624cbd';
        $this->ps_versions_compliancy = array('min' => '1.4.999', 'max' => '1.7.999');
        $this->bootstrap = true;

        $this->ps_version_15 = version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<');
        $this->ps_version_16 = version_compare(_PS_VERSION_, '1.6', '>=') && version_compare(_PS_VERSION_, '1.7', '<');
        $this->ps_version_17 = version_compare(_PS_VERSION_, '1.7', '>=');

        if ($this->ps_version_15) {
            $this->az_opt_prefix = '';
        } else {
            $this->az_opt_prefix = 'AJAXZOOM';
        }

        $this->loadingVar();
        parent::__construct();

        $this->displayName = $this->l('AJAX-ZOOM');
        $this->description = $this->l('Responsive mouseover zoom viewer with optional 360 spin feature, fullscreen view, thumbs slider and videos handling');
    }

    public function initAzMouseoverSettings()
    {
        if (!$this->mouseover_settings) {
            include dirname(__FILE__).'/AzMouseoverSettings.php';
            $this->configVendor();
            $this->mouseover_settings = new AzMouseoverSettings(array(
                'config_vendor' => $this->config_vendor,
                'vendor' => 'PrestaShop',
                'exclude_opt_vendor' => array(
                    'axZmPath',
                    'lang',
                    'images',
                    'images360',
                    //'videos',
                    'enableNativeSlider',
                    'enableCssInOtherPages',
                    'default360settingsEmbed',
                    'defaultVideoYoutubeSettings',
                    'defaultVideoVimeoSettings',
                    'defaultVideoDailymotionSettings',
                    'defaultVideoVideojsSettings',
                    //'defaultVideoVideojsJS'
                ),
                //'exclude_cat_vendor' => array('video_settings'),
                'config_extend' => array(
                    'displayInTab' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'useful' => true,
                        'type' => 'bool',
                        'isJsObject' => false,
                        'isJsArray' => false,
                        'display' => 'switch',
                        'height' => null,
                        'default' => false,
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                Display 360 / 3D / videos in tab contant. 
                                Image section will be not affected and display the default or template enabled image viewer.
                            ',
                            'DE' => '
                                Anzeige von 360 / 3D / Videos im Registerinhalt. 
                                Der Bildbereich ist nicht betroffen und zeigt den standardmäßigen oder für Vorlagen aktivierten Bildbetrachter.
                            '
                        )
                    ),
                    'inTabPosition' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'useful' => false,
                        'type' => 'string',
                        'isJsObject' => false,
                        'isJsArray' => false,
                        'display' => 'select',
                        'height' => null,
                        'default' => 'last',
                        'options' => array(
                            array('first', 'first'),
                            array('last', 'last'),
                            array('afterFirst', 'afterFirst'),
                            array('beforeLast', 'beforeLast')
                        ),
                        'comment' => array(
                            'EN' => '
                                Tab position if displayInTab option is enabled. 
                                Possible values are "first", "last", "afterFirst"
                            ',
                            'DE' => '
                                Position des Registers, wenn displayInTab Option eingeschaltet ist. 
                                Mögliche Werte sind "first", "last", "afterFirst", "beforeLast"
                            '
                        )
                    ),
                    'displayInSelector' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'useful' => true,
                        'type' => 'string',
                        'isJsObject' => false,
                        'isJsArray' => false,
                        'display' => 'text',
                        'height' => null,
                        'default' => '',
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                Display 360 / 3D / videos wherever you want. 
                                The 360 viewer will be appended to the "jQuery" selector, e.g. if you want to append 
                                the viewer to an element with CSS class "product-description", 
                                enter .product-description into this field. "inTabPosition" option must be disabled.
                                More about selectors: 
                                https://api.jquery.com/category/selectors/
                            ',
                            'DE' => '
                                Zeigen Sie 360 / 3D / Videos an, wo immer Sie möchten. 
                                Der 360-Viewer wird an den "jQuery" -Selector hinzugefügt, 
                                z.B. wenn Sie den Viewer an ein Element mit der CSS-Klasse "product-description" anhängen möchten, 
                                geben Sie .product-description in dieses Feld ein. 
                                Die Option "inTabPosition" muss deaktiviert sein. 
                                Mehr über Selektoren: https://api.jquery.com/category/selectors/
                            '
                        )
                    ),
                    'displayInSelectorAppend' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'useful' => false,
                        'type' => 'bool',
                        'isJsObject' => false,
                        'isJsArray' => false,
                        'display' => 'switch',
                        'height' => null,
                        'default' => true,
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                If "displayInSelector" is set and this option is enabled, 
                                the viewer will be appended to the container (after all elements already there). 
                                If this option is disabled, the viewer will be prepended (placed before all elements).
                            ',
                            'DE' => '
                                Wenn "displayInSelector" festgelegt ist und diese Option aktiviert ist, 
                                wird der Viewer (nach allen bereits vorhandenen Elementen) an den Container angehängt. 
                                Wenn diese Option deaktiviert ist, wird der Viewer vorangestellt (vor allen Elementen platziert).
                            '
                        )
                    ),
                    'displayInAzOpt' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'useful' => false,
                        'type' => 'string',
                        'isJsObject' => true,
                        'isJsArray' => false,
                        'display' => 'textarea',
                        'height' => null,
                        'default' => '
{
    "mouseScrollEnable": true,
    "scroll": false,
    "spinNoInit": {
        "enabled": true
    }
}',
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                Set AJAX-ZOOM options if "displayInSelector" or "displayInTab" options are enabled. 
                                For more details on how to set it, please see "azOptions360" option.
                            ',
                            'DE' => '
                                Set AJAX-ZOOM options if "displayInSelector" or "displayInTab" options are enabled.
                                For more details on how to set it, please see "azOptions360" option.
                            '
                        )
                    ),
                    'uploadNoCompress' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'type' => 'bool',
                        'isJsObject' => false,
                        'isJsArray' => false,
                        'display' => 'switch',
                        'height' => null,
                        'default' => true,
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                Do not compress regularly uploaded images. On default 
                                Prestashop compresses it but for AJAX-ZOOM this is bad and is not needed, 
                                as these images never load into cache anyway.
                            ',
                            'DE' => '
                                Do not compress regularly uploaded images. On default 
                                Prestashop compresses it but for AJAX-ZOOM this is bad and is not needed, 
                                as these images never load into cache anyway.
                            '
                        )
                    ),
                    'headerZindex' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'type' => 'int',
                        'isJsObject' => false,
                        'isJsArray' => false,
                        'display' => 'text',
                        'height' => null,
                        'default' => 1005,
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                Instantly set z-index for header ellement with ID "header". 
                                It will be not changed if z-index of header is already higher than this value. 
                                Particularly this setting is useful when none standard template 
                                has a lower z-index and the mega menu das not cover AJAX-ZOOM or is not 
                                navigateable.
                            ',
                            'DE' => '
                                Instantly set z-index for header ellement with ID "header". 
                                It will be not changed if z-index of header is already higher than this value. 
                                Particularly this setting is useful when none standard template 
                                has a lower z-index and the mega menu das not cover AJAX-ZOOM or is not 
                                navigateable.
                            '
                        )
                    ),
                    'showAllImgBtn' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'type' => 'bool',
                        'isJsObject' => false,
                        'isJsArray' => false,
                        'display' => 'switch',
                        'height' => null,
                        'default' => true,
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                Enable "Show all images" link in PrestaShop 1.7
                            ',
                            'DE' => '
                                Enable "Show all images" link in PrestaShop 1.7
                            '
                        )
                    ),
                    'showAllImgTxt' => array(
                        'prefix' => $this->az_opt_prefix,
                        'important' => false,
                        'type' => 'string',
                        'isJsObject' => true,
                        'isJsArray' => false,
                        'display' => 'textarea',
                        'height' => null,
                        'default' => '{
    "en" : "Show all images",
    "de" : "Alle Bilder anzeigen",
    "fr" : "Toutes les images",
    "es" : "todas las Imágenes"
}',
                        'options' => null,
                        'comment' => array(
                            'EN' => '
                                Text if "showAllImgBtn" is enabled.
                            ',
                            'DE' => '
                                Text if "showAllImgBtn" is enabled.
                            '
                        )
                    ),

                )
            ));
        }
    }

    public function install()
    {
        if (!$this->createDir()) {
            $this->_errors[] = ' - '._PS_ROOT_DIR_
            .$this->l('/modules/ajaxzoom/pic is not writable by PHP. Please change chmod.');

            return false;
        }

        AjaxZoom::downloadAxZm();

        // Install Module
        if (!$this->check()
        || !parent::install()
        || !$this->registerHook('header')
        || !$this->registerHook('displayFooterProduct')
        || !$this->registerHook('displayRightColumnProduct')
        //|| !$this->registerHook('moduleRoutes')
        || !Ajaxzoom::alterTable('add')
        || !$this->initSettings()
        || !$this->registerHook('actionProductUpdate')
        //|| !$this->registerHook('actionProductSave')
        //|| !$this->registerHook('actionProductAdd')
        || !$this->registerHook('actionProductDelete')
        || !$this->registerHook('displayAdminProductsExtra')
        || !$this->registerHook('actionAdminControllerSetMedia')
        || !$this->batchSubMenu()
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        // Uninstall Module
        if (!parent::uninstall()
        || !$this->unregisterHook('header')
        || !$this->unregisterHook('displayFooterProduct')
        || !$this->unregisterHook('displayRightColumnProduct')
        //|| !$this->unregisterHook('moduleRoutes')
        || !$this->unregisterHook('actionProductUpdate')
        //|| !$this->unregisterHook('actionProductSave')
        //|| !$this->unregisterHook('actionProductAdd')
        || !$this->unregisterHook('actionProductDelete')
        || !$this->unregisterHook('displayAdminProductsExtra')
        || !$this->unregisterHook('actionAdminControllerSetMedia')
        || !Ajaxzoom::alterTable('remove')
        || !$this->uninstallBatchManu()
        //|| !$this->removeDir()
        ) {
            return false;
        }

        return true;
    }

    public function uninstallBatchManu()
    {
        $q = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tab`
            WHERE `module` = \''.$this->name.'\'');

        if (empty($q)) {
            return true;
        }

        foreach ($q as $k) {
            if (isset($k['id_tab'])) {
                if (Ajaxzoom::tableExists('id_tab')) {
                    Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'access
                        WHERE `id_tab` = '.(int)$k['id_tab']);
                }

                if (Ajaxzoom::tableExists('tab_lang')) {
                    Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'tab_lang
                        WHERE `id_tab` = '.(int)$k['id_tab']);
                }

                if (Ajaxzoom::tableExists('tab')) {
                    Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'tab
                        WHERE `id_tab` = '.(int)$k['id_tab']);
                }
            }
        }

        return true;
    }

    public function batchSubMenu()
    {
        if (Ajaxzoom::tableExists('tab')) {
            $q = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tab`
                WHERE class_name = \'AdminCatalog\'');
        } else {
            return true;
        }

        if (empty($q) || !is_array($q) || !isset($q[0])) {
            return true;
        }

        $topItem = $q[0];
        $smarttab = new Tab();
        $smarttab->name[$this->context->language->id] = 'AJAX-ZOOM Batch Tool';
        $languages = Language::getLanguages(true);

        // Need this for e.g. PS 1.6.1.4 (throws error on $tab->name being empty)
        foreach ($languages as $k) {
            if (isset($k['id_lang'])) {
                $smarttab->name[$k['id_lang']] = 'AJAX-ZOOM Batch Tool';
            }
        }

        $smarttab->class_name = 'AdminAjaxzoom';
        $smarttab->id_parent = $topItem['id_tab'];
        $smarttab->module = $this->name;
        $smarttab->add();

        return true;
    }

    public function deleteAzCurrentConfig()
    {
        foreach (array_keys($this->fields_list) as $key_configuration) {
            Configuration::deleteByName($key_configuration);
        }
    }

    public function deleteAzAllConfig()
    {
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE
            name != \'AJAXZOOM_LICENSE\' AND name LIKE \'AJAXZOOM_%\'');
    }

    public function getAzSettingsList()
    {
        $conf_arr = array();
        $conf = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'configuration`');
        foreach ($conf as $k => $v) {
            if ($k >= 0) {
                array_push($conf_arr, $v['name']);
            }
        }

        return $conf_arr;
    }

    public static function downloadAxZm($update = false)
    {
        $dir = _PS_ROOT_DIR_.'/modules/ajaxzoom/';
        $backups_dir = $dir.'/backups';

        if ($update !== true) {
            $update = false;
        }

        if ($update) {
            if (!is_dir($backups_dir)) {
                if (@mkdir($backups_dir)) {
                    @chmod($backups_dir, 0777);
                }
            }

            if (!is_writable($backups_dir)) {
                return array(
                    // do not use $this->l in a static method
                    'error' => $backups_dir.' is not writable by PHP'
                );
            }
        }

        // download axZm if not exists
        if (($update || !file_exists($dir.'axZm')) && is_writable($dir.'pic/tmp/')) {
            $latest_ver = 'https://www.ajax-zoom.com/download.php?ver=latest&module=prestashop';
            if ($update) {
                $latest_ver .= '&update=1';
            }

            $remoteFileContents = Tools::file_get_contents($latest_ver);

            if ($remoteFileContents != false) {
                $localFilePath = $dir.'pic/tmp/jquery.ajaxZoom_ver_latest.zip';
                $target_bck_dir = '';

                file_put_contents($localFilePath, $remoteFileContents);

                $zip = new ZipArchive();
                $zip->open($localFilePath);
                $zip->extractTo($dir.'pic/tmp/');
                $zip->close();

                if ($update && file_exists($dir.'axZm')) {
                    $target_bck_dir = $backups_dir.'/axZm_'.(microtime(true)*10000);
                    @rename($dir.'axZm', $target_bck_dir);
                }

                rename($dir.'pic/tmp/axZm', $dir.'axZm');
                unlink($localFilePath);

                if ($update) {
                    return array(
                        'success' => 1,
                        'backupdir' => $target_bck_dir
                    );
                }
            }
        }
    }

    public function initSettings($reset = false)
    {
        $slist = $this->getAzSettingsList();

        foreach ($this->fields_list as $key => $data) {
            if (isset($data['default'])) {
                if ($this->ps_version_15) {
                    if ($key && Tools::strlen($key) > 32) {
                        continue;
                    }
                }

                if ($reset === true) {
                    Configuration::updateValue($key, $data['default']);
                } else {
                    // Do not update already existing fileds after reset
                    // Reset of the config fields is now implemented within the configuration page itself
                    if (!in_array($key, $slist)) {
                        Configuration::updateValue($key, $data['default']);
                    }
                }
            }
        }

        return true;
    }

    public function check()
    {
        $ok = true;

        $extensions = get_loaded_extensions();
        $ioncube = false;

        foreach ($extensions as $v) {
            if (stristr($v, 'ioncube')) {
                $ioncube = true;
            }
        }

        if (!$ioncube) {
            if (!ini_get('enable_dl')) {
                $la_ei = $this->l(' - It seems that ionCube loaders are not installed ');
                $la_ei .= ' '.$this->l(' and because dynamically loaded extensions are not enabled it is essential ');
                $la_ei .= ' '.$this->l(' to take care about this problem!!!');
                $this->_errors[] = $la_ei;
            } else {
                $this->_errors[] = $this->l(' - It seems that ionCube loaders are not installed on your server.');
            }

            $la_ih = $this->l(' - Please make sure Ioncube loaders are installed. ');
            $la_ih .= ' '.$this->l(' You can download the loaders and the "Loader Wizard" (PHP script to help with installation) ');
            $la_ih .= ' '.$this->l(' - for free at https://www.ioncube.com/loaders.php. ');

            $this->_errors[] = $la_ih;
            $ok = false;
        }

        // Check if axZm dir exists
        if (!file_exists(_PS_ROOT_DIR_.'/modules/ajaxzoom/axZm')) {
            $this->_errors[] = $this->l(' - Please download the latest version of AJAX-ZOOM at http://www.ajax-zoom.com/index.php?cid=download and upload only the "axZm" folder with AJAX-ZOOM script to the /modules/ajaxzoom/ directory.');
            $ok = false;
        }

        // Check if pic folder is writable
        if (!is_writable(_PS_ROOT_DIR_.'/modules/ajaxzoom/pic')) {
            $this->_errors[] = $this->l(' - The directory /modules/ajaxzoom/pic must be writable by PHP. Please change chmod to e.g. 0775 or 0777');
            $ok = false;
        }

        return $ok;
    }

    public static function deleteImageAZcache($file)
    {
        // Validator issue
        $axzmh = ''; // cannot change name as it come from external app (include below)
        $zoom = array();

        // Include all classes
        include_once(_PS_ROOT_DIR_.'/modules/ajaxzoom/axZm/zoomInc.inc.php');

        if (!Ajaxzoom::$axzmh) {
            Ajaxzoom::$axzmh = $axzmh; // cannot change name as it come from external app (include above)
            Ajaxzoom::$zoom = $zoom;
        }

        // What to delete
        $arr_del = array('In' => true, 'Th' => true, 'tC' => true, 'mO' => true, 'Ti' => true);

        // Remove all cache
        if (is_object(Ajaxzoom::$axzmh)) {
            Ajaxzoom::$axzmh->removeAxZm(Ajaxzoom::$zoom, $file, $arr_del, false);
        }
    }

    public static function clearProductImagesAXCache($id_product)
    {
        $tmp = Db::getInstance()->ExecuteS('SELECT *
            FROM `'._DB_PREFIX_.'ajaxzoomproductsimages` 
            WHERE id_product = '.(int)$id_product);

        if (isset($tmp[0]) && isset($tmp[0]['images']) && !empty($tmp[0]['images'])) {
            $images = explode(',', $tmp[0]['images']);

            foreach ($images as $image) {
                AjaxZoom::deleteImageAZcache($image);
            }
        }
    }

    public static function deleteSet($id_360set)
    {
        $id_360 = Ajaxzoom::getSetParent($id_360set);
        $id_product = Ajaxzoom::getSetProduct($id_360);

        // clear AZ cache
        $images = Ajaxzoom::get360Images($id_product, $id_360set);

        foreach ($images as $image) {
            AjaxZoom::deleteImageAZcache($image['filename']);
        }

        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoom360set` WHERE id_360set = '.$id_360set);

        $tmp = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360set` WHERE id_360 = '.$id_360);

        if (!$tmp) {
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoom360` WHERE id_360 = '.$id_360);
        }

        $path = _PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360.'/'.$id_360set;

        Tools::deleteDirectory($path);
    }

    public function productCombinations($id_product)
    {
        $id_lang = Context::getContext()->language->id;
        $product = new Product($id_product);

        // Build attributes combinations
        $combinations = $product->getAttributeCombinations($id_lang);
        $comb_array = array();

        if (is_array($combinations)) {
            // $combination_images = $product->getCombinationImages($id_lang);

            foreach ($combinations as $combination) {
                $id_product_attribute = $combination['id_product_attribute'];

                // needed in tab360-settings
                $comb_array[$id_product_attribute]['id_product_attribute'] = $id_product_attribute;
                $comb_array[$id_product_attribute]['attributes'][] = array(
                    $combination['group_name'],
                    $combination['attribute_name'],
                    $combination['id_attribute']
                );
            }
        }

        if (isset($comb_array) && !empty($comb_array)) {
            foreach ($comb_array as $id_product_attribute => $product_attribute) {
                $list = '';

                // In order to keep the same attributes order
                asort($product_attribute['attributes']);

                foreach ($product_attribute['attributes'] as $attribute) {
                    $list .= $attribute[0].' - '.$attribute[1].', ';
                }

                $list = rtrim($list, ', ');
                $comb_array[$id_product_attribute]['name'] = $list;
            }
        }

        return $comb_array;
    }

    public function getNumPlainImages()
    {
        $db = Db::getInstance();
        $numImages = $db->query('SELECT count(*) as numImg FROM `'._DB_PREFIX_.'image`')->fetchAll();
        return $numImages[0]['numImg'];
    }

    public function rSearch($folder, $pattern)
    {
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
        $fileList = array();

        foreach ($files as $file) {
            $fileList = array_merge($fileList, $file);
        }

        return $fileList;
    }

    public function getNum360Images()
    {
        $dst = _PS_ROOT_DIR_.'/img/p/360';
        return count($this->rSearch($dst, '/\.(jpeg|jpg|png|gif|bmp|tif|tiff)/i'))/2;
    }

    public function createDir()
    {
        $res3 = false;

        if (!file_exists(_PS_IMG_DIR_.'p/360')) {
            $res1 = mkdir(_PS_IMG_DIR_.'p/360', 0775);
            @chmod(_PS_IMG_DIR_.'p/360', 0775); // errors are handeled elsewhere
        } else {
            if (!is_writable(_PS_IMG_DIR_.'p/360')) {
                $res1 = @chmod(_PS_IMG_DIR_.'p/360', 0775); // errors are handeled elsewhere
            } else {
                $res1 = true;
            }
        }

        if (is_writable(_PS_IMG_DIR_.'p/360') && !file_exists(_PS_IMG_DIR_.'p/360/.htaccess')) {
            file_put_contents(_PS_IMG_DIR_.'p/360/.htaccess', 'deny from all');
        }

        if (!file_exists(_PS_ROOT_DIR_.'/'.Ajaxzoom::$tmpdir.'ajaxzoom')) {
            $res2 = mkdir(_PS_ROOT_DIR_.'/'.Ajaxzoom::$tmpdir.'ajaxzoom', 0775);
            @chmod(_PS_ROOT_DIR_.'/'.Ajaxzoom::$tmpdir.'ajaxzoom', 0775); // errors are handeled elsewhere
        } else {
            if (!is_writable(_PS_ROOT_DIR_.'/'.Ajaxzoom::$tmpdir.'ajaxzoom')) {
                $res2 = @chmod(_PS_ROOT_DIR_.'/'.Ajaxzoom::$tmpdir.'ajaxzoom', 0775); // errors are handeled elsewhere
            } else {
                $res2 = true;
            }
        }

        foreach ($this->az_pic_dirs as $dir) {
            $path = _PS_ROOT_DIR_.'/modules/ajaxzoom/pic/'.$dir;

            if (!file_exists($path)) {
                $res3 = mkdir($path, 0775);
                @chmod($path, 0775); // errors are handeled elsewhere
            } else {
                if (!is_writable($path)) {
                    $res3 = @chmod($path, 0775); // errors are handeled elsewhere
                } else {
                    $res3 = true;
                }
            }

            if (!$res3) {
                break;
            }
        }

        @chmod(_PS_ROOT_DIR_.'/modules/ajaxzoom/zip', 0775); // errors are handeled elsewhere

        return $res1 && $res2 && $res3;
    }

    public function removeDir()
    {
        //return Tools::deleteDirectory(_PS_IMG_DIR_.'p/360');
        Tools::deleteDirectory(_PS_ROOT_DIR_.'/'.Ajaxzoom::$tmpdir.'ajaxzoom');

        foreach ($this->az_pic_dirs as $dir) {
            $path = _PS_ROOT_DIR_.'/modules/ajaxzoom/pic/'.$dir;
            Tools::deleteDirectory($path);
        }

        return true;
    }

    public static function alterTable($method)
    {
        switch ($method) {
            case 'add':
                $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ajaxzoom360` 
                    (`id_360` int(11) NOT NULL AUTO_INCREMENT, 
                    `id_product` int(11) NOT NULL, `name` varchar(255) NOT NULL, `num` int(11) NOT NULL DEFAULT \'1\', 
                    `settings` text NOT NULL, `status` tinyint(1) NOT NULL DEFAULT \'0\', 
                    `combinations` text NOT NULL, `crop` TEXT NOT NULL, `hotspots` TEXT NOT NULL, 
                    PRIMARY KEY (`id_360`)) 
                    ENGINE=InnoDB 
                    DEFAULT CHARSET=utf8; ';

                $sql .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ajaxzoom360set` 
                    (`id_360set` int(11) NOT NULL AUTO_INCREMENT, `id_360` int(11) NOT NULL, 
                    `sort_order` int(11) NOT NULL, PRIMARY KEY (`id_360set`)) 
                    ENGINE=InnoDB 
                    DEFAULT CHARSET=utf8 
                    AUTO_INCREMENT=1; ';

                $sql .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ajaxzoomproducts` 
                    (`id_product` int(11) NOT NULL, 
                    PRIMARY KEY (`id_product`)) 
                    ENGINE=InnoDB 
                    DEFAULT CHARSET=utf8; ';

                $sql .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ajaxzoomproductsimages` 
                    ( `id_product` int(11) NOT NULL, 
                    `images` text NOT NULL, 
                    PRIMARY KEY (`id_product`)) 
                    ENGINE=InnoDB 
                    DEFAULT CHARSET=utf8; ';

                $sql .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ajaxzoomvideo` 
                    (`id_video` int(11) NOT NULL AUTO_INCREMENT,
                    `uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                    `id_product` int(11) NOT NULL,
                    `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                    `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
                    `thumb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                    `settings` text COLLATE utf8_unicode_ci NOT NULL,
                    `status` tinyint(1) NOT NULL DEFAULT \'1\',
                    `combinations` text COLLATE utf8_unicode_ci NOT NULL,
                    `auto` tinyint(1) NOT NULL DEFAULT \'1\',
                    `data` text CHARACTER SET utf16 NOT NULL,
                    PRIMARY KEY (`id_video`),
                    KEY `id_product` (`id_product`),
                    KEY `uid` (`uid`)) 
                    ENGINE=InnoDB 
                    DEFAULT CHARSET=utf8; ';

                $sql .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ajaxzoomimagehotspots` 
                    (`id` int(11) NOT NULL AUTO_INCREMENT, 
                    `id_media` int(11) NOT NULL,
                    `id_product` int(11) NOT NULL,
                    `image_name` varchar(255) NOT NULL,
                    `hotspots_active` int(1) NOT NULL DEFAULT 1,
                    `hotspots` text NOT NULL,
                    PRIMARY KEY (`id`)) 
                    ENGINE=InnoDB 
                    DEFAULT CHARSET=utf8 
                    COLLATE=utf8_unicode_ci; ';

                $sql .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ajaxzoomproductsettings` 
                    (`id_product` int(11) NOT NULL, 
                    `psettings` text NOT NULL, 
                    `psettings_embed` text NOT NULL, 
                    PRIMARY KEY (`id_product`)) 
                    ENGINE=InnoDB 
                    DEFAULT CHARSET=utf8 
                    COLLATE=utf8_unicode_ci; ';

                break;

            case 'remove':
                /*
                $sql = 'DROP TABLE '. _DB_PREFIX_.'ajaxzoom360;
                    DROP TABLE '._DB_PREFIX_.'ajaxzoom360set;
                    DROP TABLE '._DB_PREFIX_.'ajaxzoomproducts;
                    DROP TABLE '._DB_PREFIX_.'ajaxzoomproductsimages;
                    DROP TABLE '._DB_PREFIX_.'ajaxzoomproductvideo';
                    DROP TABLE '._DB_PREFIX_.'ajaxzoomimagehotspots';
                    DROP TABLE '._DB_PREFIX_.'ajaxzoomproductsettings';
                ';
                */

                $sql = 'SELECT * FROM '._DB_PREFIX_.'ajaxzoom360';
                break;
        }

        if (!Db::getInstance()->Execute($sql)) {
            return false;
        }

        return true;
    }

    public function getValues()
    {
        if (empty($this->_config)) {
            foreach (array_keys($this->fields_list) as $key_configuration) {
                $this->_config[$key_configuration] = Configuration::get($key_configuration);
            }
        }
    }

    public function hookmoduleRoutes()
    {
        ini_set('post_max_size', '256M');
        ini_set('upload_max_filesize', '256M');
        ini_set('memory_limit', '512M');

        return array();
    }

    public function hookActionAdminControllerSetMedia()
    {
        if ($this->context->controller->controller_name == 'AdminModules'
            && ($this->smarty->getTemplateVars('module_name') == 'ajaxzoom'
                || Tools::getValue('module_name') == 'ajaxzoom')
        ) {
            $this->context->controller->addCSS(
                $this->_path.'views/css/font-awesome/css/font-awesome.min.css',
                'all'
            );

            if ($this->ps_version_17) {
                $this->context->controller->addCSS(
                    $this->_path.'axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.css',
                    'all'
                );

                $this->context->controller->addJS(
                    $this->_path.'axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.js'
                );
            }

            $this->context->controller->addJS(
                $this->_path.'axZm/extensions/jquery.axZm.openAjaxZoomInFancyBox.js'
            );
        }

        if ($this->context->controller->controller_name == 'AdminProducts'
            || $this->context->controller->controller_name == 'AdminAjaxzoom'
        ) {
            $this->context->controller->addCSS(
                $this->_path.'views/css/font-awesome/css/font-awesome.min.css',
                'all'
            );

            $this->context->controller->addCSS(
                $this->_path.'views/css/axZm-PS-backend.css',
                'all'
            );

            if (version_compare(_PS_VERSION_, '1.7.3', '>=')) {
                $this->context->controller->addCSS(
                    $this->_path.'views/js/tooltipster/css/tooltipster.bundle.min.css',
                    'all'
                );

                $this->context->controller->addCSS(
                    $this->_path.'views/css/axZm-PS-backend-1.7.3.css',
                    'all'
                );

                $this->context->controller->addJS(
                    $this->_path.'views/js/ajaxzoom_ps_back-1.7.3.js'
                );

                $this->context->controller->addJS(
                    $this->_path.'views/js/tooltipster/js/tooltipster.bundle.min.js'
                );
            }

            if ($this->ps_version_15) {
                $this->context->controller->addCSS(
                    $this->_path.'views/css/axZm-PS-backend-15.css',
                    'all'
                );
            }

            $this->context->controller->addCSS(
                $this->_path.'views/css/jquery.editable-select.css',
                'all'
            );

            $this->context->controller->addJS(
                $this->_path.'views/js/jquery.editable-select.js'
            );

            $this->context->controller->addJS(
                $this->_path.'axZm/extensions/jquery.axZm.openAjaxZoomInFancyBox.js'
            );

            $this->context->controller->addJS(
                $this->_path.'views/js/ajaxzoom_ps_back.js'
            );

            if ($this->ps_version_17) {
                $this->context->controller->addCSS(
                    $this->_path.'axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.css',
                    'all'
                );

                $this->context->controller->addJS(
                    $this->_path.'axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.js'
                );
            }
        }

        if ($this->ps_version_17) {
            // add necessary javascript to products back office
            if ($this->context->controller->controller_name == 'AdminProducts') {
                $bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme)
                ? $this->context->employee->bo_theme
                : 'default');

                if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
                    $bo_theme = 'default';
                }

                $this->context->controller->addJs(
                    __PS_BASE_URI__
                    .$this->context->controller->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js'
                );

                $this->context->controller->addJs(
                    __PS_BASE_URI__
                    .$this->context->controller->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js'
                );

                $this->context->controller->addJs(
                    __PS_BASE_URI__
                    .$this->context->controller->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js'
                );

                $this->context->controller->addJs(
                    __PS_BASE_URI__.
                    $this->context->controller->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js'
                );

                $this->context->controller->addJS(__PS_BASE_URI__.'js/vendor/spin.js');

                $this->context->controller->addJS(__PS_BASE_URI__.'js/vendor/ladda.js');
            }
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if ($this->ps_version_17) {
            $output = '';
            $output .= $this->settingsBlock($params['id_product']);
            $output .= $this->setList($params['id_product']);
            $output .= $this->imageList($params['id_product']);
            $output .= $this->videosBlock($params['id_product']);
            $output .= $this->pictureList($params['id_product']);
            return $output;
        } else {
            $output = '';
            $output .= $this->settingsBlock();
            $output .= $this->setList();
            $output .= $this->imageList();
            $output .= $this->videosBlock();
            $output .= $this->pictureList();
            return $output;
        }
    }

    public static function getBaseDir()
    {
        return __PS_BASE_URI__;
    }

    public function hookActionProductDelete($params)
    {
        /*
        $aaa = (isset($params['id_product']) ? $params['id_product'] : '0')."\r\n\r---\n\r\n";
        file_put_contents(dirname(__FILE__).'/log.txt', $aaa, FILE_APPEND);
        */

        $sets = $this->getSets($params['id_product']);

        foreach ($sets as $set) {
            Ajaxzoom::deleteSet($set['id_360set']);
        }

        $id_product = (int)$params['id_product'];

        Ajaxzoom::clearProductImagesAXCache($params['id_product']);

        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomproducts`
            WHERE id_product = '.$id_product);

        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomproductsimages`
            WHERE id_product = '.$id_product);

        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomvideo`
            WHERE id_product = '.$id_product);

        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomimagehotspots`
            WHERE id_product = '.$id_product);
    }

    public function logWriteImages($id = '', $image_array = array())
    {
        if (!empty($image_array) && !empty($id)) {
            Db::getInstance()->Execute('REPLACE INTO `'._DB_PREFIX_.'ajaxzoomproductsimages` (id_product, images)
                VALUES('.(int)$id.', \''.join(',', $image_array).'\')');
        }
    }

    public function logImages($params = array(), $product = null)
    {
        if (!isset($params) || !isset($params['id_product']) || empty($params['id_product'])) {
            return;
        }

        $image_array = array();
        $product = new Product($params['id_product']);
        $images = $product->getImages((int)$this->context->language->id);

        if (!empty($images)) {
            foreach ($images as $data) {
                $image = new Image($data['id_image']);
                $image_array[] = $image->id.'.'.$image->image_format;
            }

            $this->logWriteImages($params['id_product'], $image_array);
        }
    }

    public function hookActionProductUpdate($params = array())
    {
        $this->logImages($params);
    }

    /*
    public function hookActionProductSave($params = array())
    {
        $this->logImages($params);
    }

    public function actionProductAdd($params = array())
    {
        $this->logImages($params);
    }
    */

    public function settingsBlock($id_product = null)
    {
        if (!$id_product) {
            $id_product = (int)Tools::getValue('id_product');
        }

        if (Validate::isLoadedObject($product = new Product($id_product))) {
            $sets_groups = $this->getSetsGroups($product->id);

            $this->smarty->assign(array(
                'sets_groups' => $sets_groups,
                'id_product' => $product->id,
                'comb' => $this->productCombinations($product->id),
                'active' => $this->isProductActive($product->id) ? 1 : 0,
                'ps_version' => _PS_VERSION_,
                'ps_version_15' => $this->ps_version_15,
                'ps_version_16' => $this->ps_version_16,
                'ps_version_17' => $this->ps_version_17,
                'az_plugin_opt' => $this->getPluginOptNameLabel(),
                'az_plugin_prod_opt' => Ajaxzoom::getProductPluginOpt($product->id)
            ));

            return $this->display(__FILE__, 'views/templates/admin/tab360-settings.tpl');
        }
    }

    public function isProductActive($id_product)
    {
        return !Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoomproducts`
            WHERE id_product = '.(int)$id_product);
    }

    public function pictureList($id_product = null)
    {
        if (!$id_product) {
            $id_product = (int)Tools::getValue('id_product');
        }

        $picures = $this->picturesBackendList($id_product);

        if (Validate::isLoadedObject(new Product($id_product))) {
            $this->smarty->assign(array(
                'id_product' => $id_product,
                'base_url' => AjaxZoom::getBaseDir(),
                'base_uri' => __PS_BASE_URI__,
                'base_dir' => AjaxZoom::getBaseDir(),
                'ps_version' => _PS_VERSION_,
                'ps_version_15' => $this->ps_version_15,
                'ps_version_16' => $this->ps_version_16,
                'ps_version_17' => $this->ps_version_17,
                'az_pictures' => $picures
            ));

            return $this->display(__FILE__, 'views/templates/admin/tab-pictures.tpl');
        }
    }

    public function picturesBackendList($id_product = null)
    {
        $image_array = array();

        if (!$id_product) {
            return $image_array;
        }

        $product = new Product($id_product);
        $images = $product->getImages((int)$this->context->language->id);

        foreach ($images as $image) {
            $image_obj = new Image($image['id_image']);

            $path = $this->realUri().'img/p/'.Image::getImgFolderStatic($image['id_image']);
            $path .= $image['id_image'].'.'.$image_obj->image_format;

            $thumb = AjaxZoom::getBaseDir().'modules/ajaxzoom/axZm/zoomLoad.php?';
            $thumb .= 'azImg='.$path;
            $thumb .= '&width=100&height=100&thumbMode=contain';

            $image_array[$image_obj->id] = array(
                'id' => $image_obj->id,
                'name' => $image_obj->id.'.'.$image_obj->image_format,
                'path' => $path,
                'thumb' => $thumb
            );
        }

        $sql = Db::getInstance()->ExecuteS('SELECT *
            FROM `'._DB_PREFIX_.'ajaxzoomimagehotspots`
            WHERE id_product = '.(int)$id_product);

        foreach ($sql as $row) {
            if (isset($image_array[$row['id_media']])) {
                $image_array[$row['id_media']]['active'] = (int)$row['hotspots_active'];
                $image_array[$row['id_media']]['hotspots'] = 1;
            }
        }

        return $image_array;
    }

    public function imageList($id_product = null)
    {
        if (!$id_product) {
            $id_product = (int)Tools::getValue('id_product');
        }

        if (Validate::isLoadedObject($product = new Product($id_product))) {
            $shops = false;

            if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP) {
                $current_shop_id = (int)$this->context->shop->id;
            } else {
                $current_shop_id = 0;
            }

            $languages = Language::getLanguages(true);

            if ($this->ps_version_15) {
                $this->smarty->assign(array(
                    'id_product' => $product->id,
                    'token' => Tools::safeOutput(Tools::getValue('token')),
                    'max_image_size' => $this->max_image_size / 1024 / 1024,
                    'table' => '',
                    'current_shop_id' => '',
                    'base_dir' => AjaxZoom::getBaseDir(),
                    'ps_version' => _PS_VERSION_,
                    'ps_version_15' => $this->ps_version_15,
                    'ps_version_16' => $this->ps_version_16,
                    'ps_version_17' => $this->ps_version_17
                ));

                $image_uploader_form = $this->display(__FILE__, 'views/templates/admin/uploader.tpl');
            } else {
                $image_uploader = new HelperImageUploader('file360');
                $image_uploader->setTemplateDirectory(dirname(__FILE__));
                $image_uploader->setTemplate('views/templates/admin/ajax.tpl');

                $url = Context::getContext()->link->getModuleLink('ajaxzoom', 'image360', array(
                    'ajax' => 1,
                    'id_product' => $product->id,
                    'action' => 'addProductImage360'
                ));

                $multiple = !($this->getUserBrowser() == 'Apple Safari' && Tools::getUserPlatform() == 'Windows');
                $image_uploader->setMultiple($multiple)->setUseAjax(true)->setUrl($url);

                $image_uploader_form = $image_uploader->render();
            }

            $this->smarty->assign(array(
                'product' => $product,
                'id_product' => $product->id,
                'iso_lang' => $languages[0]['iso_code'],
                'token' => Tools::safeOutput(Tools::getValue('token')),
                'max_image_size' => $this->max_image_size / 1024 / 1024,
                'up_filename' => (string)Tools::getValue('virtual_product_filename_attribute'),
                'currency' => $this->context->currency,
                'current_shop_id' => $current_shop_id,
                'languages' => $languages,
                'default_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
                'image_uploader' => $image_uploader_form,
                'shops' => $shops,
                //'images' => $images,
                //'countImages' => count($images)
                'images' => array(),
                'countImages' => 0,
                'base_dir' => AjaxZoom::getBaseDir(),
                'ps_version' => _PS_VERSION_,
                'ps_version_15' => $this->ps_version_15,
                'ps_version_16' => $this->ps_version_16,
                'ps_version_17' => $this->ps_version_17
            ));

            return $this->display(__FILE__, 'views/templates/admin/tab360.tpl');
        }
    }

    public function setList($id_product = null)
    {
        if (!$id_product) {
            $id_product = (int)Tools::getValue('id_product');
        }

        $sets_groups = $this->getSetsGroups($id_product);
        $sets = $this->getSets($id_product);

        $this->smarty->assign(array(
            'sets_groups' => $sets_groups,
            'sets' => $sets,
            'files' => $this->getArcList(),
            'id_product' => $id_product,
            'base_url' => AjaxZoom::getBaseDir(),
            'base_uri' => __PS_BASE_URI__,
            'ps_version' => _PS_VERSION_,
            'ps_version_15' => $this->ps_version_15,
            'ps_version_16' => $this->ps_version_16,
            'ps_version_17' => $this->ps_version_17
        ));

        return $this->display(__FILE__, 'views/templates/admin/tab360-sets.tpl');
    }

    public function videosBlock($id_product = null)
    {
        if (!$id_product) {
            $id_product = (int)Tools::getValue('id_product');
        }

        $videos = AjaxZoom::getVideos($id_product);

        $this->smarty->assign(array(
            'id_product' => $id_product,
            'base_url' => AjaxZoom::getBaseDir(),
            'base_uri' => __PS_BASE_URI__,
            'base_dir' => AjaxZoom::getBaseDir(),
            'videos' => $videos,
            'token' => Tools::safeOutput(Tools::getValue('token')),
            'comb' => $this->productCombinations($id_product),
            'languages' => Language::getLanguages(true),
            'ps_version' => _PS_VERSION_,
            'ps_version_15' => $this->ps_version_15,
            'ps_version_16' => $this->ps_version_16,
            'ps_version_17' => $this->ps_version_17
        ));

        return $this->display(__FILE__, 'views/templates/admin/tab-videos.tpl');
    }

    public static function getVideos($id_product)
    {
        $r = array();
        $sql = Db::getInstance()->ExecuteS('SELECT *
            FROM `'._DB_PREFIX_.'ajaxzoomvideo`
            WHERE id_product = '.(int)$id_product.'
            ORDER BY id_video ASC');

        foreach ($sql as $row) {
            $r[$row['id_video']] = $row;
        }

        return $r;
    }

    public function getSetsGroups($id_product)
    {
        return Db::getInstance()->ExecuteS('SELECT g.*, COUNT(g.id_360)
            AS qty, s.id_360set
            FROM `'._DB_PREFIX_.'ajaxzoom360` g
            LEFT JOIN `'._DB_PREFIX_.'ajaxzoom360set` s ON g.id_360 = s.id_360
            WHERE g.id_product = '.(int)$id_product.'
            GROUP BY g.id_360');
    }

    public static function getSetsGroupsStatic($id_product)
    {
        return Db::getInstance()->ExecuteS('SELECT g.*, COUNT(g.id_360) AS qty, s.id_360set
            FROM `'._DB_PREFIX_.'ajaxzoom360` g
            LEFT JOIN `'._DB_PREFIX_.'ajaxzoom360set` s ON g.id_360 = s.id_360
            WHERE g.id_product = '.(int)$id_product.'
            GROUP BY g.id_360');
    }

    public function getSets($id_product)
    {
        $sets = Db::getInstance()->ExecuteS('SELECT s.*, g.name, g.id_360, g.status
            FROM `'._DB_PREFIX_.'ajaxzoom360set` s, `'._DB_PREFIX_.'ajaxzoom360` g
            WHERE g.id_360 = s.id_360 AND g.id_product = '.(int)$id_product.'
            ORDER BY g.name, s.id_360set'); // ORDER BY g.name, s.sort_order');

        foreach ($sets as &$set) {
            $thumb = AjaxZoom::getBaseDir().'modules/ajaxzoom/axZm/zoomLoad.php';
            $thumb .= '?qq=1&azImg360='.__PS_BASE_URI__
                .'img/p/360/'.$id_product.'/'.$set['id_360'].'/'.$set['id_360set'];

            $thumb .= '&width=100&height=100&thumbMode=contain';

            if (file_exists(_PS_ROOT_DIR_.'/img/p/360/'.$id_product.'/'.$set['id_360'].'/'.$set['id_360set'])) {
                $set['path'] = $thumb;
            } else {
                $set['path'] = AjaxZoom::getBaseDir().'modules/ajaxzoom/views/img/no_image-100x100.jpg';
            }
        }

        return $sets;
    }

    public static function get360Images($id_product, $id_360set = '')
    {
        $files = array();
        $id_360 = Ajaxzoom::getSetParent($id_360set);
        $dir = _PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360.'/'.$id_360set;

        if (file_exists($dir) && $handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (Tools::substr($entry, 0, 1) != '.' && $entry != '__MACOSX') {
                    $files[] = $entry;
                }
            }

            closedir($handle);
        }

        sort($files);

        $res = array();

        foreach ($files as $entry) {
            $tmp = explode('.', $entry);
            $ext = end($tmp);
            $name = preg_replace('|\.'.$ext.'$|', '', $entry);
            $thumb = AjaxZoom::getBaseDir().'modules/ajaxzoom/axZm/zoomLoad.php?';
            $thumb .= 'azImg='.__PS_BASE_URI__.'img/p/360/'.$id_product.'/'.$id_360.'/'.$id_360set.'/'.$entry
                .'&width=100&height=100&qual=90';

            $res[] = array(
                'thumb' => $thumb,
                'filename' => $entry,
                'id' => $name,
                'ext' => $ext
            );
        }

        return $res;
    }

    public static function getSetProduct($id_360)
    {
        if ($tmp = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360`
            WHERE id_360 = '.(int)$id_360)) {
            return $tmp[0]['id_product'];
        }
    }

    public static function getSetParent($id_360set)
    {
        if ($tmp = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360set`
            WHERE id_360set = '.(int)$id_360set)) {
            return $tmp[0]['id_360'];
        }
    }

    public function getCSV($input, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        if (function_exists('str_getcsv')) {
            return str_getcsv($input, $delimiter, $enclosure, $escape);
        } else {
            $temp = fopen('php://memory', 'rw');
            fwrite($temp, $input);
            fseek($temp, 0);
            $r = fgetcsv($temp, 0, $delimiter, $enclosure);
            fclose($temp);
            return $r;
        }
    }

    public function isOnlyProductActive($id)
    {
        $this->getValues();

        if (!$this->_config[$this->az_opt_prefix.'_DISPLAYONLYFORTHISPRODUCTID']) {
            return true;
        }

        $arr = $this->getCSV($this->_config[$this->az_opt_prefix.'_DISPLAYONLYFORTHISPRODUCTID']);

        if (in_array($id, $arr)) {
            return true;
        }

        return false;
    }

    public function tplProduct($params, $hk = false)
    {
        if (!$params) {
            $params = array();
        }

        $id_product = (int)Tools::getValue('id_product');
        $smarty = $this->smarty;
        $content_only = (int)$smarty->getTemplateVars('content_only');

        if ($hk == 'hookDisplayFooterProduct' && $content_only == 1) {
            return;
        }

        if ($this->product_output === true) {
            return;
        }

        $this->product_output = true;
        $this->getValues();

        if ($this->_config[$this->az_opt_prefix.'_ENABLEINFRONTDETAIL'] == 'false') {
            return;
        }

        if (!$this->isProductActive($id_product)) {
            return;
        }

        if (!$this->isOnlyProductActive($id_product)) {
            return;
        }

        $attribute_id = '';
        $lang_iso = '';
        $combination_images = array();
        $this->_config = $this->extendProductIndividualSettings($this->_config, $id_product);
        $display_in_tab = $this->toBool($this->_config[$this->az_opt_prefix.'_DISPLAYINTAB']);
        $display_in_selector = $this->_config[$this->az_opt_prefix.'_DISPLAYINSELECTOR'];
        $display_in_selector_append = $this->toBool($this->_config[$this->az_opt_prefix.'_DISPLAYINSELECTORAPPEND']);
        if ($display_in_tab) {
            $display_in_selector = '';
        }

        if ($this->ps_version_17) {
            $tpl_vars = $smarty->getTemplateVars('product');

            if (method_exists($tpl_vars, 'getAttributeCombinations')) {
                $tpl_vars_attr = $tpl_vars->getAttributeCombinations();
                $attribute_id = isset($tpl_vars_attr['id_product_attribute'])
                    ? (int)$tpl_vars_attr['id_product_attribute']
                    : 0;
            } elseif (isset($tpl_vars['id_product_attribute'])) {
                $attribute_id = (int)$tpl_vars['id_product_attribute'];
            } else {
                $attribute_id = 0;
            }

            $combination_images_tmp = $smarty->getTemplateVars('combinationImages');

            if (!empty($combination_images_tmp) && is_array($combination_images_tmp)) {
                $combination_images = array(0 => array());
                foreach ($combination_images_tmp as $k => $v) {
                    $combination_images[$k] = array();
                    foreach ($v as $val) {
                        if (!in_array($val['id_image'], $combination_images[0])) {
                            array_push($combination_images[0], (int)$val['id_image']);
                        }

                        array_push($combination_images[$k], (int)$val['id_image']);
                    }
                }
            }
        }

        $lang_iso = $this->context->language->iso_code;

        $init_param = $this->mouseover_settings->getInitJs(array(
            'cfg' => $this->_config,
            'window' => 'window.',
            'holder_object' => 'jQuery.axZm_psh',
            'exclude_opt' => array(),
            'exclude_cat' => array('video_settings'),
            'ovrprefix' => $this->az_opt_prefix,
            'differ' => true,
            'min' => true
        ));

        $videos = $this->videosJson($id_product);

        // $this->_config are strings!
        $smarty_frontend_vars = array(
            'ajaxzoom_displayInTab' => $display_in_tab,
            'ajaxzoom_displayInSelector' => $display_in_selector,
            'ajaxzoom_displayInSelectorAppend' => $display_in_selector_append,
            'ajaxzoom_inTabPosition' => $this->_config[$this->az_opt_prefix.'_INTABPOSITION'],
            'ajaxzoom_displayInAzOpt' => $this->_config[$this->az_opt_prefix.'_DISPLAYINAZOPT'],
            'ajaxzoom_imagesJSON' => $display_in_tab || $display_in_selector ? '{}' : $this->imagesJson($id_product),
            'ajaxzoom_images360JSON' => $this->images360Json($id_product),
            'ajaxzoom_videosJSON' => $videos = $this->videosJson($id_product),
            'ajaxzoom_ps_version' => _PS_VERSION_,
            'ajaxzoom_ps_version_15' => $this->ps_version_15,
            'ajaxzoom_ps_version_16' => $this->ps_version_16,
            'ajaxzoom_ps_version_17' => $this->ps_version_17,
            'ajaxzoom_combination_images' => Tools::jsonEncode($combination_images),
            'ajaxzoom_attribute_id' => $attribute_id,
            'ajaxzoom_modules_dir' => $this->getModulesDir(),
            'ajaxzoom_lang_iso' => $lang_iso,
            'ajaxzoom_initParam' => $init_param,
            'ajaxzoom_divID' => $this->_config[$this->az_opt_prefix.'_DIVID'],
            'ajaxzoom_galleryDivID' => $this->_config[$this->az_opt_prefix.'_GALLERYDIVID'],
            'ajaxzoom_headerZindex' => $this->_config[$this->az_opt_prefix.'_HEADERZINDEX'],
            'ajaxzoom_showAllImgBtn' => $this->toBool($this->_config[$this->az_opt_prefix.'_SHOWALLIMGBTN']),
            'ajaxzoom_showAllImgTxt' => $this->mouseover_settings->minifyJs(
                $this->_config[$this->az_opt_prefix.'_SHOWALLIMGTXT']
            ),
            'ajaxzoom_content_only' => $content_only,
            'ajaxzoom_videojs' => $this->productHasVideoHtml5($videos),
            'ajaxzoom_videojsfiles' =>
                (array)Tools::jsonDecode($this->_config[$this->az_opt_prefix.'_DEFAULTVIDEOVIDEOJSJS'])
        );

        $smarty->assign($smarty_frontend_vars);
        return $this->display(__FILE__, 'views/templates/front/ajaxzoom.tpl');
    }

    public function hookDisplayFooterProduct($params)
    {
        $id_product = (int)Tools::getValue('id_product');
        if ($id_product) {
            $this->_config = $this->extendProductIndividualSettings($this->_config, $id_product);
            return $this->tplProduct($params, 'hookDisplayFooterProduct');
        }
    }

    public function hookDisplayRightColumnProduct($params)
    {
        $id_product = (int)Tools::getValue('id_product');
        if ($id_product) {
            $this->_config = $this->extendProductIndividualSettings($this->_config, $id_product);
            if (!$this->toBool($this->_config[$this->az_opt_prefix.'_DISPLAYINTAB'])) {
                return $this->tplProduct($params, 'hookDisplayRightColumnProduct');
            }
        }
    }

    public function getModulesDir()
    {
        if (!($this->ps_version_15 || $this->ps_version_16)) {
            $module_dir = __PS_BASE_URI__.str_replace(_PS_ROOT_DIR_, '', _PS_MODULE_DIR_);
            $module_dir = str_replace('//', '/', $module_dir);
            $module_dir = str_replace('\\\\', '\\', $module_dir);
            return $module_dir;
        } else {
            return $this->smarty->getTemplateVars()['modules_dir'];
        }
    }

    public function realUri()
    {
        return str_replace($this->context->shop->virtual_uri, '', __PS_BASE_URI__);
    }

    public function getPicturesHotspots($id_product)
    {
        $sql = Db::getInstance()->ExecuteS('SELECT *
            FROM `'._DB_PREFIX_.'ajaxzoomimagehotspots`
            WHERE id_product = '.(int)$id_product.'
            AND hotspots_active = 1');

        $hotspots = array();

        foreach ($sql as $row) {
            $hotspots[$row['id_media']] = $row;
        }

        return $hotspots;
    }

    public function imagesJson($id_product)
    {
        $product = new Product($id_product);

        $json = '{';
        $cnt = 1;
        $images = $product->getImages((int)$this->context->language->id);
        $hotspots = $this->getPicturesHotspots((int)$id_product);
        $image_array = array();

        if (!empty($images)) {
            foreach ($images as $image) {
                //$img = 'http://'.$link->getImageLink($product->link_rewrite, $product->id.'-'.$image['id_image']);

                $image_obj = new Image($image['id_image']);
                $image_array[] = $image_obj->id.'.'.$image_obj->image_format;

                $id_image = $image['id_image'];

                $path = $this->realUri().'img/p/'.Image::getImgFolderStatic($image['id_image']);
                $path .= $id_image.'.'.$image_obj->image_format;

                //$p = parse_url($img);

                $title = isset($image['legend']) ? $image['legend'] : '';
                $title = str_replace('"', '&#34;', $title);
                $title = str_replace('\'', '&#39;', $title);

                $json .= '"'.$id_image.'": {"img": "'.$path.'", "order": "'.$cnt.'", "title": "'.$title.'"';
                if (isset($hotspots[$id_image])
                    && $hotspots[$id_image]['hotspots']
                    && $hotspots[$id_image]['hotspots'] != '{}'
                ) {
                    $hotspots[$id_image]['hotspots'] = trim(preg_replace('/\s+/', ' ', $hotspots[$id_image]['hotspots']));
                    $hotspots[$id_image]['hotspots'] = str_replace('\\\'', '\'', $hotspots[$id_image]['hotspots']);
                    $hotspots[$id_image]['hotspots'] = str_replace('\'', '\\\'', $hotspots[$id_image]['hotspots']);
                    $json .= ', "hotspotFilePath": '.$hotspots[$id_image]['hotspots'];
                }

                $json .='}';
                $cnt++;

                if ($cnt != count($images) + 1) {
                    $json .= ', ';
                }
            }

            // in PS < 1.7 there is no "beforeDeleteProduct" hook where we can request image names
            // as it is executet after deletion
            // we need to know image names for which AZ made cache
            $this->logWriteImages($id_product, $image_array);
        }

        $json .= '}';

        return $json;
    }

    public static function set360status($id_360, $status)
    {
        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoom360`
            SET status = '.(int)$status.'
            WHERE id_360 = '.(int)$id_360);
    }

    public static function getCropJSON($id_360)
    {
        $db = Db::getInstance();
        $check_crop_field = $db->query('SHOW FIELDS FROM `'._DB_PREFIX_.'ajaxzoom360`');
        if ($check_crop_field) {
            $check_crop_field_fetch = $check_crop_field->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('crop', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `crop` TEXT NOT NULL');
            }

            if (!in_array('hotspots', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `hotspots` TEXT NOT NULL');
            }
        }

        if ($row = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360`
            WHERE id_360 = '.(int)$id_360)) {
            return Tools::stripslashes($row['crop']);
        } else {
            return '[]';
        }
    }

    public static function setCropJSON($id_360, $json)
    {
        $db = Db::getInstance();
        $check_crop_field = $db->query('SHOW FIELDS FROM `'._DB_PREFIX_.'ajaxzoom360`');
        if ($check_crop_field) {
            $check_crop_field_fetch = $check_crop_field->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('crop', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `crop` TEXT NOT NULL');
            }

            if (!in_array('hotspots', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `hotspots` TEXT NOT NULL');
            }
        }

        // Tools::getValue('json') makes e.g. out of \n (linebreak) n which is wrong,
        // we need to use $_POST['json'] here, as Tools::getValue() returns broken code for a third party tool
        // It is used only for admin over admin controller
        $json = $_POST['json']; // cannot change, see above comment
        $json = preg_replace('/\s+/S', " ", $json);

        if (!get_magic_quotes_gpc()) {
            $json = addslashes($json);
        }

        $db->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoom360` SET crop = \''.$json.'\' WHERE id_360 = '.(int)$id_360);

        return $db->Affected_Rows();
    }

    public static function getHotspotJSON($id_360)
    {
        $db = Db::getInstance();
        $check_crop_field = $db->query('SHOW FIELDS FROM `'._DB_PREFIX_.'ajaxzoom360`');

        if ($check_crop_field) {
            $check_crop_field_fetch = $check_crop_field->fetchAll(PDO::FETCH_COLUMN);

            if (!in_array('crop', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `crop` TEXT NOT NULL');
            }

            if (!in_array('hotspots', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `hotspots` TEXT NOT NULL');
            }
        }

        $row = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360` WHERE id_360 = '.(int)$id_360);

        if ($row['hotspots']) {
            return Tools::stripslashes($row['hotspots']);
        } else {
            return '{}';
        }
    }

    public static function setHotspotJSON($id_360, $json)
    {
        $db = Db::getInstance();
        $check_crop_field = $db->query('SHOW FIELDS FROM `'._DB_PREFIX_.'ajaxzoom360`');
        if ($check_crop_field) {
            $check_crop_field_fetch = $check_crop_field->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('crop', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `crop` TEXT NOT NULL');
            }

            if (!in_array('hotspots', $check_crop_field_fetch)) {
                $db->Execute('ALTER TABLE `'._DB_PREFIX_.'ajaxzoom360` ADD `hotspots` TEXT NOT NULL');
            }
        }

        // Tools::getValue('json') makes e.g. out of \n (linebreak)
        // n which is wrong, we need to use $_POST['json'] here
        // It is used only for admin over admin controller
        $json = $_POST['json']; // cannot change, see above comment
        $json = preg_replace('/\s+/S', " ", $json);

        if (!get_magic_quotes_gpc()) {
            $json = addslashes($json);
        }

        $db->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoom360` SET hotspots = \''.$json.'\' 
            WHERE id_360 = '.(int)$id_360);

        return $db->Affected_Rows();
    }

    public static function getHotspotPictureJSON($id_media)
    {
        $row = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ajaxzoomimagehotspots`
            WHERE id_media = '.(int)$id_media);

        if ($row['hotspots']) {
            return Tools::stripslashes($row['hotspots']);
        } else {
            return '{}';
        }
    }

    public static function setHotspotPictureJSON($id_product, $id_media, $json)
    {
        $db = Db::getInstance();

        // Tools::getValue('json') makes e.g. out of \n (linebreak)
        // n which is wrong, we need to use $_POST['json'] here
        // It is used only for admin over admin controller
        $json = $_POST['json']; // cannot change, see above comment
        $json = preg_replace('/\s+/S', " ", $json);

        if (!get_magic_quotes_gpc()) {
            $json = addslashes($json);
        }

        $row = $db->getRow('SELECT count(*) as num FROM `'._DB_PREFIX_.'ajaxzoomimagehotspots` 
            WHERE id_media = '.(int)$id_media.' AND id_product='.(int)$id_product);

        if (!$json || $json == '{}') {
            $db->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomimagehotspots` 
                WHERE id_media = '.(int)$id_media.' AND id_product='.(int)$id_product);

            return 1;
        } else {
            if ($row['num']) {
                $db->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoomimagehotspots` SET hotspots = \''.$json.'\' 
                    WHERE id_media = '.(int)$id_media.' AND id_product='.(int)$id_product);
            } else {
                $db->Execute('INSERT INTO `'._DB_PREFIX_.'ajaxzoomimagehotspots` SET hotspots = \''.$json.'\',
                    id_media = '.(int)$id_media.', id_product='.(int)$id_product);
            }

            return $db->Affected_Rows();
        }
    }

    public function videosJson($id_product)
    {
        $videos = AjaxZoom::getVideos($id_product);
        $ret = array();
        $i = 0;

        $lang = Context::getContext()->language->iso_code;
        if ($lang) {
            $lang = Tools::strtolower($lang);
        }

        foreach ($videos as $k => $v) {
            $i++;
            $uid = $videos[$k]['uid'];
            $data = (array)Tools::jsonDecode($v['data']);

            $data = (array)Tools::jsonDecode($v['data']);
            if ($lang && !empty($data) && is_array($data) && isset($data['uid']) && is_object($data['uid'])) {
                if (!empty($data['uid']->{$lang}) && trim($data['uid']->{$lang}) != '') {
                    $uid = trim($data['uid']->{$lang});
                }
            }

            $ret[$i] = array(
                'key' => $uid,
                'settings' => (array)Tools::jsonDecode($v['settings']),
                'combinations' => explode(',', $v['combinations']),
                'type' => $v['type']
            );
        }

        $ret = Tools::jsonEncode($ret);

        return $ret;
    }

    public function images360Json($id_product)
    {
        $sets_groups = $this->getSetsGroups($id_product);

        $arr = array();

        foreach ($sets_groups as $group) {
            if ($group['status'] == 0) {
                continue;
            }

            $settings = $this->prepareSettings($group['settings']);

            if (!empty($settings)) {
                $settings = ', '.$settings;
            }

            if ($group['qty'] > 0) {
                $crop = empty($group['crop']) ? '[]' : trim(preg_replace('/\s+/', ' ', $group['crop']));
                $hotspots = empty($group['hotspots']) ? '{}' : trim(preg_replace('/\s+/', ' ', $group['hotspots']));
                if ($hotspots != '{}') {
                    $hotspots = str_replace('\\\'', '\'', $hotspots);
                    $hotspots = str_replace('\'', '\\\'', $hotspots);
                }

                if ($group['qty'] == 1) {
                    $str = '"'.$group['id_360'].'": {"path": "'.$this->realUri()
                        .'img/p/360/'.$id_product.'/'.$group['id_360'].'/'.$group['id_360set'].'"';
                    $str .= $settings;
                    $str .= ', "combinations": ['.$group['combinations'].']';

                    if ($crop && $crop != '[]') {
                        $str .= ', "crop": '.$crop;
                    }

                    if ($hotspots && $hotspots != '{}') {
                        $str .= ', "hotspotFilePath": '.$hotspots;
                    }

                    $str .= '}';
                } else {
                    $str = '"'.$group['id_360'].'": {"path": "'.$this->realUri()
                        .'img/p/360/'.$id_product.'/'.$group['id_360'].'"';
                    $str .= $settings;
                    $str .= ', "combinations": ['.$group['combinations'].']';

                    if ($crop && $crop != '[]') {
                        $str .= ', "crop": '.$crop;
                    }

                    if ($hotspots && $hotspots != '{}') {
                        $str .= ', "hotspotFilePath": '.$hotspots;
                    }

                    $str .= '}';
                }
                $arr[] = $str;
            }
        }

        $ret = '{'.implode(',', $arr).'}';
        $ret = str_replace('\\n', '', $ret);
        $ret = preg_replace('/\t+/', '', $ret);

        return $ret;
    }

    public function prepareSettings($str)
    {
        $res = array();
        $settings = (array)Tools::jsonDecode($str);

        foreach ($settings as $key => $value) {
            if ($value == 'false' || $value == 'true' || $value == 'null' || is_numeric($value)
                || Tools::substr($value, 0, 1) == '{' || Tools::substr($value, 0, 1) == '['
            ) {
                $res[] = '"'.$key.'": '.$value;
            } else {
                $res[] = '"'.$key.'": "'.$value.'"';
            }
        }

        return implode(', ', $res);
    }

    public function toBool($v)
    {
        if ($v === true || $v == 'true' || $v == 'TRUE' || $v === 1 || $v == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function hookHeader($params)
    {
        if (Dispatcher::getInstance()->getController() == 'product') {
            $id_product = (int)Tools::getValue('id_product');
            $ret = '';
            $this->getValues();

            if ($this->_config[$this->az_opt_prefix.'_ENABLEINFRONTDETAIL'] == 'false') {
                return $ret;
            }

            if (!$this->isProductActive($id_product)) {
                return $ret;
            }

            if (!$this->isOnlyProductActive($id_product)) {
                return $ret;
            }

            $this->_config = $this->extendProductIndividualSettings($this->_config, $id_product);
            if ($this->toBool($this->_config[$this->az_opt_prefix.'_DISPLAYINTAB'])) {
                $this->_config[$this->az_opt_prefix.'_DISPLAYINSELECTOR'] = '';
            }

            if (!$this->_config[$this->az_opt_prefix.'_DISPLAYINSELECTOR']) {
                if (!$this->toBool($this->_config[$this->az_opt_prefix.'_DISPLAYINTAB'])) {
                    if ($this->ps_version_15 || $this->ps_version_16) {
                        $this->context->controller->addCSS($this->_path.'views/css/axZm-PS-frontend-15-16.css', 'all');
                    } else {
                        $this->context->controller->addCSS($this->_path.'views/css/axZm-PS-frontend-17.css', 'all');
                    }
                } else {
                    $this->context->controller->addCSS($this->_path.'views/css/axZm-PS-frontend-tab.css', 'all');
                }
            }

            // AJAX-ZOOM core, needed!
            $this->context->controller->addCSS($this->_path.'axZm/axZm.css', 'all');
            $this->context->controller->addJS(($this->_path).'axZm/jquery.axZm.js');

            // Frontend static script
            $this->context->controller->addJS(($this->_path).'views/js/ajaxzoom_ps_front.js');

            // Include thumbSlider JS & CSS, optional
            // if (Configuration::get($this->az_opt_prefix.'_GALLERYAXZMTHUMBSLIDER') == 'true') {
            $this->context->controller->addJS(
                $this->_path.'axZm/extensions/axZmThumbSlider/lib/jquery.mousewheel.min.js'
            );

            $this->context->controller->addCSS(
                $this->_path.'axZm/extensions/axZmThumbSlider/skins/default/jquery.axZm.thumbSlider.css',
                'all'
            );

            $this->context->controller->addJS(
                $this->_path.'axZm/extensions/axZmThumbSlider/lib/jquery.axZm.thumbSlider.js'
            );
            //}

            // Preloading spinner, optional
            if (Configuration::get($this->az_opt_prefix.'_SPINNER') == 'true') {
                $this->context->controller->addJS(($this->_path).'axZm/plugins/spin/spin.min.js');
            }

            // AJAX-ZOOM mouse over zoom extension, needed!
            $this->context->controller->addCSS(
                $this->_path.'axZm/extensions/axZmMouseOverZoom/jquery.axZm.mouseOverZoom.5.css',
                'all'
            );

            $this->context->controller->addJS(
                $this->_path.'axZm/extensions/axZmMouseOverZoom/jquery.axZm.mouseOverZoom.5.js'
            );

            $this->context->controller->addJS(
                $this->_path.'axZm/extensions/axZmMouseOverZoom/jquery.axZm.mouseOverZoomInit.5.js'
            );

            // Scripts for 360 crop gallery!
            $this->context->controller->addCSS($this->_path.'axZm/extensions/jquery.axZm.expButton.css', 'all');
            $this->context->controller->addJS(($this->_path).'axZm/extensions/jquery.axZm.expButton.js');
            $this->context->controller->addJS(($this->_path).'axZm/extensions/jquery.axZm.imageCropLoad.js');

            if (Configuration::get($this->az_opt_prefix.'_PNGMODECSSFIX') == 'true') {
                $this->context->controller->addCSS(
                    $this->_path.'axZm/extensions/axZmMouseOverZoom/jquery.axZm.mouseOverZoomPng.5.css',
                    'all'
                );
            }

            // Fancybox lightbox javascript, please note: it has been slightly modified for AJAX-ZOOM,
            // only needed if ajaxZoomOpenMode is set to "fancyboxFullscreen" or "fancybox"
            // and Fancybox 2.x is not included already
            if (Configuration::get($this->az_opt_prefix.'_AJAXZOOMOPENMODE') == 'fancyboxFullscreen') {
                $fancybox_ver = $this->getFancyboxVersion();

                // if old fancybox is included replace with modified from AJAX-ZOOM package
                if ($this->ps_version_17 || version_compare($fancybox_ver, '2.0.0') == -1) {
                    if (method_exists($this->context->controller, 'removeJS')) {
                        $this->context->controller->removeJS(
                            __PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.js'
                        );
                    }

                    if (method_exists($this->context->controller, 'removeCSS')) {
                        $this->context->controller->removeCSS(
                            __PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.css'
                        );
                    }

                    // Fancybox 1.3.4 from AJAX-ZOOM package
                    $this->context->controller->addCSS(
                        $this->_path.'axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.css',
                        'all'
                    );
                    $this->context->controller->addJS(
                        $this->_path.'axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.js'
                    );
                }

                // AJAX-ZOOM extension to load AJAX-ZOOM into maximized fancybox,
                // requires jquery.fancybox-1.3.4.css and jquery.fancybox-1.3.4.js, optional
                $this->context->controller->addJS(
                    $this->_path.'axZm/extensions/jquery.axZm.openAjaxZoomInFancyBox.js'
                );
            }

            // IE7 support (at least for AJAX-ZOOM)
            $this->context->controller->addJS(($this->_path).'axZm/plugins/JSON/jquery.json-2.3.min.js');

            /* // does not work for external sources
            if (Configuration::get($this->az_opt_prefix.'_VIDEOHTML5VIDEOJS') == 'true') {
                $videojs = Configuration::get($this->az_opt_prefix.'_DEFAULTVIDEOVIDEOJSJS');

                $videojs_arr = (array)Tools::jsonDecode($videojs);

                if (!empty($videojs_arr)) {
                    foreach ($videojs_arr as $k => $v) {
                        if ($v) {
                            if (stristr($k, 'js')) {
                                $this->context->controller->addJS($v);
                            } elseif (stristr($k, 'css')) {
                                $this->context->controller->addCSS($v);
                            }
                        }
                    }
                }
            }
            */

            return $ret;
        }
    }

    public function getFancyboxVersion()
    {
        $version = '';

        $tmp = implode('', file(_PS_ROOT_DIR_.'/js/jquery/plugins/fancybox/jquery.fancybox.js'));

        preg_match('| fancyBox v(.*?) |', $tmp, $r);

        if (!empty($r[1])) {
            $version = trim($r[1]);
        } else {
            preg_match('| Version: (.*?) |', $tmp, $r);
            if (!empty($r[1])) {
                $version = trim($r[1]);
            }
        }

        return $version;
    }

    public static function cleanStr($str = '')
    {
        if (is_string($str)) {
            return preg_replace('/\s+/S', ' ', $str);
        } else {
            return '';
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitSave')) {
            $this->postProcess();
            if (_PS_VERSION_ >= 1.6) {
                $this->_html .= '<div class="alert alert-success">';
            } else {
                $this->_html .= '<div class="conf">';
            }
            $this->_html .= $this->l('Settings have been saved.');
            $this->_html .= '</div>';
        } elseif (Tools::isSubmit('submitResetSettings') && Tools::getValue('submitResetConfirm') == 'ok') {
            $this->initSettings(true);
            if (_PS_VERSION_ >= 1.6) {
                $this->_html .= '<div class="alert alert-success">';
            } else {
                $this->_html .= '<div class="conf">';
            }
            $this->_html .= $this->l('Settings have been reset to default values.');
            $this->_html .= '</div>';
        }

        $text_settings = '<h2 style="font-size: 150%;">'.$this->displayName.' for PrestaShop<br>Ver. '
            .$this->version.' ('.$this->version_date.')</h2>';

        $text_settings .= '<ul style="padding-left: 15px; list-style-type: disc; ">';
        $text_settings .= '<li style="margin-bottom: 7px;">';
        $text_settings .= $this->l('AJAX-ZOOM is a multipurpose library for displaying (high resolution) images and 360°/3D spins. ');
        $text_settings .= $this->l('This PrestaShop module integrates only one particular implementation (example) from AJAX-ZOOM library into PrestaShop. ');
        $text_settings .= $this->l('In original this example can be found here: ');

        $text_settings .= '<br><a href=https://www.ajax-zoom.com/examples/example32.php target=_blank>
https://www.ajax-zoom.com/examples/example32.php ';

        $text_settings .= '<i class="icon-external-link-sign"></i></a> <br>';
        $text_settings .= $this->l('There you will also find some subtle details about the options which you can configure below. ');
        $text_settings .= $this->l('These options mainly refer to this one implementation / example. ');

        $text_settings .= '</li>';
        $text_settings .= '<li style="margin-bottom: 7px;">';

        $text_settings .= html_entity_decode($this->l('However, AJAX-ZOOM has many other options, which can be set manually in /modules/ajaxzoom/axZm/zoomConfigCustom.inc.php after <br><code>elseif ($_GET[\'example\'] == \'mouseOverExtension360Ver5\')</code> or if you do not want to edit PHP files - most of them can be also set in these formfields as JS plain object '));

        $text_settings .= '<a href="javascript:void(0)" 
            onClick="jQuery.azFindOption(\'azOptions\')">azOptions';
        $text_settings .= '<i class="icon-caret-down"></i></a> && ';
        $text_settings .= '<a href="javascript:void(0)" 
            onClick="jQuery.azFindOption(\'azOptions360\')">azOptions360 ';
        $text_settings .= '<i class="icon-caret-down"></i></a> ';
        $text_settings .= '</li>';
        $text_settings .= '<li style="margin-bottom: 7px;">';
        $text_settings .= $this->l('Depending on the template used you might need to adjust options marked red or with this symbol: ');

        $text_settings .= '<i class="icon-hand-right" style="color: #C9302C; font-size: 150%"></i> ';
        $text_settings .= $this->l('If you will not be able to adjust these options on your own please ');
        $text_settings .= '<a href="https://www.ajax-zoom.com/index.php?cid=contact" target="_blank">';
        $text_settings .= $this->l('ask for support').' <i class="icon-external-link-sign"></i></a>';
        $text_settings .= '</li>';
        $text_settings .= '<li style="margin-bottom: 7px;">';
        $text_settings .= $this->l('Other useful / most common options are marked yellow or with this symbol: ');
        $text_settings .= '<i class="icon-AdminAdmin" style="color: #EC971F; font-size: 150%"></i>';
        $text_settings .= '</li>';
        $text_settings .= '<li style="margin-bottom: 7px;">';
        $text_settings .= $this->l('For more information about this module, latest versions and troubleshooting please see ');

        $text_settings .= '<a href="https://www.ajax-zoom.com/index.php?cid=modules&module=prestashop" 
            target="_blank">';
        $text_settings .= $this->l('module home').' <i class="icon-external-link-sign"></i></a>';
        $text_settings .= '</li>';
        $text_settings .= '</ul>';
        $text_settings .= '<p style="margin-top: 30px;">';
        $text_settings .= '<span style="color: red; font-weight: bold;">New: </span>';
        $text_settings .= $this->l('From module version 1.9.0 you can place only the 360 viewer into a tab. ');
        $text_settings .= $this->l('You can also place the player into any other html element using a jQuery selector. ');
        $text_settings .= $this->l('The options to enable one of these features are: ');
        $text_settings .= '<a href="javascript:void(0)" 
            onClick="jQuery.azFindOption(\'displayInTab\')">displayInTab ';
        $text_settings .= '<i class="icon-caret-down"></i></a> ';
        $text_settings .= ' && ';
        $text_settings .= '<a href="javascript:void(0)" 
            onClick="jQuery.azFindOption(\'displayInSelector\')">displayInSelector ';
        $text_settings .= '<i class="icon-caret-down"></i></a> ';
        $text_settings .= $this->l('Since regular images are not displayed in AJAX-ZOOM player, they are not affected by the license.');
        $text_settings .= '</p>';

        $text_settings .= '<p style="margin-top: 10px;">';
        $text_settings .= '<span style="color: red; font-weight: bold;">'.$this->l('New').': </span>';
        $text_settings .= $this->l('starting from AJAX-ZOOM v. 5.3.0 (core files) you can set AJAX-ZOOM to load the originally uploaded files instead of image tiles at frontend. ');
        $text_settings .= html_entity_decode($this->l('This is an AJAX-ZOOM option - <code>$zoom[\'config\'][\'simpleMode\']</code> and needs to be set in <code>/modules/ajax-zoom/zoomConfigCustomAZ.inc.php</code> file. '));
        $text_settings .= html_entity_decode($this->l('If you are using <b>"simple"</b> license, this option is enabled instantly. '));
        $text_settings .= $this->l('Whether this option is enabled instantly or you have activated it intentionally, in order to this option to work properly, please open <code>/img/p/360/.htaccess</code> file and remove "deny from all" line from this file. ');
        $text_settings .= $this->l('Do not delete the .htaccess file completely, as the file will be recreated on updates if it does not exist. ');
        $text_settings .= '</p>';

        if (_PS_VERSION_ >= 1.6) {
            $this->_html .= '<div class="alert" 
                style="border: #d5d5e6 1px solid; background-color: #FFF; padding: 15px 25px 25px 25px;">';
            $this->_html .= '<button class="close" data-dismiss="alert" type="button">&#10006;</button>';
            $this->_html .= $text_settings;
            $this->_html .= '</div>';
        } else {
            $this->_html .= '<div id="hintAzConfig" 
                style="margin-bottom: 20px; padding: 10px; border: #CCCED7 1px solid">';
            $this->_html .= '<span style="float:right">';
            $this->_html .= '<a id="hideWarn" href="javascript:void(0)" onclick="jQuery(\'#hintAzConfig\').remove()">';
            $this->_html .= '&#10006;';
            $this->_html .= '</a>';
            $this->_html .= '</span>';
            $this->_html .= $text_settings;
            $this->_html .= '</div>';
        }

        $text_reset = $this->l('From module Version 1.2.20 settings are not overwritten to their defaults, when you reset or uninstall / install the module. ');
        $text_reset .= $this->l('If you want or need to reset the settings to defaults of the current installed version, please press the button. ');
        $text_reset .= $this->l('License information is not reset. ');
        $text_reset .= $this->l('All other data like 360 views is not reset either. ');
        $text_reset .= $this->l('This reset functionality is only about the module settings below. ');

        $form_reset = '<form action="index.php?tab='.Tools::safeOutput(Tools::getValue('tab'));
        $form_reset .= '&configure='.Tools::safeOutput(Tools::getValue('configure'));
        $form_reset .= '&token='.Tools::safeOutput(Tools::getValue('token'));
        $form_reset .= '&tab_module='.Tools::safeOutput(Tools::getValue('tab_module'));
        $form_reset .= '&module_name='.Tools::safeOutput(Tools::getValue('module_name'));
        $form_reset .= '&id_tab=1" method="post" class="form defaultForm form-horizontal">';
        $form_reset .= '<div style="margin-top: 10px; margin-bottom: 10px;">';
        $form_reset .= '<input type="hidden" name="submitResetConfirm" id="submitResetConfirm" value="">';
        $form_reset .= '<button name="submitResetSettings" style="width: 100%;"
            onclick="jQuery(\'#submitResetConfirm\').val(\'ok\')" 
            type="submit" value="1" class="_button btn btn-default">';

        $form_reset .= '<i class="fa fa-refresh" style="margin-right: 5px;"></i> '.$this->l('Reset settings');
        $form_reset .= '</button></div>';
        $form_reset .= '</form>';

        if (_PS_VERSION_ >= 1.6) {
            $this->_html .= '<div class="row" style="margin-bottom: 25px;">';
            $this->_html .= '<div class="col-lg-3">';
            $this->_html .= $form_reset;
            $this->_html .= '</div><div class="col-lg-9">';
            $this->_html .= '<div style="margin-left: 10px;">';
            $this->_html .= '<h4>'.$this->l('Reset module settings to default values').'</h4>';
            $this->_html .= $text_reset;
            $this->_html .= '</div></div></div>';
        } else {
            $this->_html .= '<div style="margin-bottom: 20px; padding: 10px; border: #CCCED7 1px solid">';
            $this->_html .= '<h4>'.$this->l('Reset module settings to default values').'</h4>';
            $this->_html .= '<div>'.$text_reset.'</div>';
            $this->_html .= $form_reset;
            $this->_html .= '</div>';
        }

        if (_PS_VERSION_ >= 1.6) {
            $this->_html .= '<div style="text-align: right; margin-bottom: 10px;">
                <a class="btn btn-default" style="margin-left: 15px;" id="az_toggle_all_tabs"
                onclick="jQuery(\'#configForm .toggleMenuSign\').trigger(\'click\')">';
            $this->_html .= '<i class="icon-th-list" style="margin-right: 5px;"></i>'
                .$this->l('Toggle all tabs').'</a></div>';
            $this->_html .= '<div class="panel">';
        }

        $this->displayForm();

        if (_PS_VERSION_ >= 1.6) {
            $this->_html .= '</div>';
        }

        return $this->_html;
    }

    public function updateAxZmTab()
    {
        $ret = '';
        $ret .= '<div style="margin-bottom: 50px; font-style: normal">';
        $ret .= '<h4 style="margin-top: 0">'.$this->l('Please read before taking any actions!').'</h4>';
        $ret .= '<p>';
        $ret .= $this->l('In order the module for PrestaShop to work it requires that the core AJAX-ZOOM files are present in /modules/ajaxzoom/axZm folder. ');
        $ret .= $this->l('When you update the module, the core files do not get updated instantly!');
        $ret .= $this->l('You can download the latest AJAX-ZOOM core files (without examples and test images) from http://www.ajax-zoom.com/index.php?cid=download and unzip the axZm folder (containing in this download) into /modules/ajaxzoom/, previously renaming the old axZm folder to e.g. axZm_old or other name in order to backup it. ');
        $ret .= $this->l('This is how you could do it manually over FTP...');
        $ret .= '</p>';
        $ret .= '<p>';
        $ret .= $this->l('From module version 1.6.0 you can update these core files by pressing on the button below.');
        $ret .= $this->l('Make sure that you or your developer did not make any substantial changes to AJAX-ZOOM core files, which is mostly bad practice, before updating. ');
        $ret .= $this->l('Anyway, before updating the current AJAX-ZOOM core version, located in /modules/ajaxzoom/axZm, it will be backed up into /modules/ajaxzoom/backups/axZm_[timestamp] folder. ');
        $ret .= $this->l('So in case after updating it causes any issues you can always restore the previous version manually. ');
        $ret .= $this->l('However, before restoring, make sure that an eventual problem is not caused by template and js / css files caching (refresh if needed). ');
        $ret .= '</p>';
        $ret .= '<p style="margin-bottom: 30px;">';
        $ret .= $this->l('Latest news about AJAX-ZOOM PrestaShop module (not AJAX-ZOOM core) can be found here: ');
        $ret .= '<a href="https://www.ajax-zoom.com/index.php?cid=modules&module=prestashop" target="_blank">
            https://www.ajax-zoom.com/index.php?cid=modules&module=prestashop</a>';
        $ret .= '</p>';

        $ret .='<table cellspacing="0" cellpadding="0" style="width: 100%">
            <tbody><tr><td style="width: 50%; vertical-align: top;">';
        $ret .= '<h4>'.$this->l('Currently installed AJAX-ZOOM version').'</h4>';

        $az_ver = $this->getAzVersion(true);

        if (isset($az_ver['version']) && $az_ver['version']) {
            foreach ($az_ver as $k => $v) {
                $ret .= Tools::ucfirst($k).': '.$v.'<br>';
            }
        } else {
            $this->l('Cannot read data from /modules/ajaxzoom/axZm/readme.txt');
        }

        $ret .='</td><td style="vertical-align: top;">';
        $ret .= '<h4>'.$this->l('Available AJAX-ZOOM version').'</h4>';
        $output_az = $this->getAzAvailVersion();
        if (empty($output_az) || isset($output_az['error'])) {
            if ($output_az['error'] == 1) {
                $ret .= $this->l('Service currently not available');
            } else {
                $ret .= $this->l('Technical error');
            }
        } else {
            $ret .= 'Version: '.$output_az['version'].'<br>';
            $ret .= 'Date: '.$output_az['date'].'<br>';
            $ret .= 'Review: '.$output_az['review'].'<br>';

            if (isset($az_ver['version'])
                && $az_ver['version']
                && version_compare($output_az['version'], $az_ver['version'], '>') == 1
            ) {
                $ret .= '<br><span style="color: green; font-size: bold; font-size: 120%;">'
                    .$this->l('There is a new version available! ').'</span><br>';

                foreach ($output_az['notes'] as $k => $v) {
                    if (version_compare($k, $az_ver['version'], '>') == 1) {
                        $ret .= '<br><strong>'.$k.'</strong> '.$this->l('release notes').':<br>';
                        $ret .= '<ul>';
                        foreach ($v as $note) {
                            $ret .= '<li>'.$note.'</li>';
                        }

                        $ret .= '</ul>';
                    }
                }

                $ret .= '<hr>';
                $ret .= '<button class="_button btn btn-default" id="az_update_az">
                    Update AJAX-ZOOM core files
                </button>';

                $ret .= '
                <script type="text/javascript">
                /*!
                *  @author         AJAX-ZOOM <support@ajax-zoom.com>
                *  @copyright      2010-2019 AJAX-ZOOM, Vadim Jacobi
                *  @license        https://www.ajax-zoom.com/index.php?cid=download
                */
                var successMsg = "'.$this->l('Update finished. Page will be reloaded in 30 seconds.').'";
                var updateMsg = "'.$this->l('Updating, please wait...').'";
                jQuery("#az_update_az").bind("click", function(e) {
                    e.preventDefault();
                    var new_html = "<div id=\"az_update_status\">"+updateMsg+"</div>";
                    jQuery(this).replaceWith(new_html);
                    jQuery.ajax( {
                        url: "'.AjaxZoom::getBaseDir().'index.php",
                        data: {
                            "action": "updateAz",
                            "token": "'.Tools::safeOutput(Tools::getValue('token')).'",
                            "fc": "module",
                            "module": "ajaxzoom",
                            "controller": "image360",
                            "ajax": 1
                        },
                        type: "GET",
                        success: function(data) {
                            var data = jQuery.parseJSON(data);
                            if (data.success) {
                                jQuery("#az_update_status").html(successMsg);
                                var location_reload_to = setInterval(function() {
                                    var cur_t = jQuery("#az_update_status").html().replace(/[^0-9]/g, "");
                                    cur_t = parseInt(jQuery.trim(cur_t));
                                    if (cur_t == 0) {
                                        clearInterval(location_reload_to);
                                        location.reload(true);
                                    } else {
                                        var str = jQuery("#az_update_status").html();
                                        var new_str = str.replace(cur_t+"", (cur_t-1));
                                        jQuery("#az_update_status").html(new_str);
                                    }
                                }, 1000);
                            } else {
                                jQuery("#az_update_status").html("Update failed");
                            }
                        },
                        error: function(data) {
                            jQuery("#az_update_status").html("Technical error");
                        }
                    } );
                });
                </script>
                ';
            } else {
                $ret .= '<br><span style="color: green; font-size: 120%; font-weight: bold;">';
                $ret .= $this->l('You have latest AJAX-ZOOM core version installed.');
                $ret .= '</span>';
            }
        }

        $ret .='</td></tr></tbody></table>';

        $ret .= '</div>';

        return $ret;
    }

    private function displayForm()
    {
        $this->_html .= '<form action="index.php?tab='.Tools::safeOutput(Tools::getValue('tab'));
        $this->_html .= '&configure='.Tools::safeOutput(Tools::getValue('configure'));
        $this->_html .= '&token='.Tools::safeOutput(Tools::getValue('token'));
        $this->_html .= '&tab_module='.Tools::safeOutput(Tools::getValue('tab_module'));
        $this->_html .= '&module_name='.Tools::safeOutput(Tools::getValue('module_name'));
        $this->_html .= '&id_tab=1&section=" '; // &section=general
        $this->_html .= 'method="post" class="form defaultForm form-horizontal" id="configForm">';

        // todo: find option select
        $arr_opt = array();

        foreach ($this->fields_list as $key => $data) {
            $arr_opt[] = $data['title'];
        }

        foreach ($this->categories as $cat_code => $cat_title) {
            if (_PS_VERSION_ >= 1.6) {
                $icon_class = 'fa fa-cogs';

                if ($cat_code == 'license') {
                    $icon_class = 'fa fa-tags';
                } elseif ($cat_code == 'plugin_settings') {
                    $icon_class = 'fa fa-plug';
                } elseif ($cat_code == 'general_settings') {
                    $icon_class = 'fa fa-cogs';
                } elseif ($cat_code == 'product_tour') {
                    $icon_class = 'fa fa-sitemap';
                } elseif ($cat_code == 'fullscreen_gallery') {
                    $icon_class = 'fa fa-television';
                } elseif ($cat_code == 'mouseover') {
                    $icon_class = 'fa fa-clone';
                } elseif ($cat_code == 'video_settings') {
                    $icon_class = 'fa fa-video-camera';
                }

                if ($cat_code == 'license') {
                    $this->_html .= '<div class="clear">&nbsp;</div>';
                }

                $this->_html .= '<fieldset>';
                $this->_html .= '<div class="panel-heading">';
                $this->_html .= '<a href="javascript:void(0)" 
                    class="toggleMenuSign" style="margin-right: 30px; outline: 0;">';
                $this->_html .= '<i class="icon-minus-sign"></i>';
                $this->_html .= '</a> ';
                $this->_html .= '<span class="toggleMenuSign1">';
                $this->_html .= '<i class="'.$icon_class.'" style="margin-right: 5px;"></i> '.$cat_title.'';
                $this->_html .= '</span> ';
                $this->_html .= '</div>';
            } else {
                $icon_cat = '../img/t/AdminOrderPreferences.gif';

                if ($cat_title == 'License') {
                    $icon_cat = '../img/t/AdminAdmin.gif';
                }

                $this->_html .= '<fieldset><legend><img src="'.$icon_cat.'"> '.$cat_title.'</legend>';
            }

            if ($cat_code == 'license') {
                $this->smarty->assign(array(
                    'licenses' => Configuration::get('AJAXZOOM_LICENSE'),
                    'ps_version' => _PS_VERSION_,
                    'numImg' => $this->getNumPlainImages(),
                    'num360' => $this->getNum360Images()
                ));

                $this->_html .= $this->display(__FILE__, 'views/templates/admin/lic.tpl');
            } else {
                foreach ($this->fields_list as $key => $data) {
                    if (empty($data['category'])) {
                        $data['category'] = 'general';
                    }

                    if (!isset($data['title'])) {
                        $data['title'] = ' ';
                    }

                    if (_PS_VERSION_ < 1.6) {
                        if ($data['category'] == $cat_code) {
                            $important = isset($data['important'])
                                ? 'color: red;'
                                : (isset($data['useful']) ? 'color: #EC971F;' : '');

                            $this->_html .= '<label style="'.$important.'">'.$data['title'].' : </label>';
                            $this->_html .= '<div class="margin-form" data-o="'.(isset($data['title']) ? $data['title'] : '').'">';
                            $this->_html .= $this->formField($key, $data);

                            $p_warn_tag = '<p class="preference_description" 
                                style="padding: 5px; background: #FCF8E3; color: #976D3B;">';

                            if (isset($data['isJsObject'])) {
                                $this->_html .= $p_warn_tag.html_entity_decode(
                                    $this->l('Attention: you are editing JavaScript <b>object</b>! Errors will lead to AJAX-ZOOM not working properly.')
                                ).'</p>';
                            } elseif (isset($data['isJsArray'])) {
                                $this->_html .= $p_warn_tag.html_entity_decode(
                                    $this->l('Attention: you are editing JavaScript <b>array</b>! Errors will lead to AJAX-ZOOM not working properly.')
                                ).'</p>';
                            } elseif (isset($data['isJsFunction'])) {
                                $this->_html .= $p_warn_tag.html_entity_decode(
                                    $this->l('Attention: you are defining JavaScript <b>function</b>! Errors will lead to AJAX-ZOOM not working properly.')
                                ).'</p>';
                            }

                            if (!empty($data['comment'])) {
                                $this->_html .= '<p class="preference_description">'
                                    .str_replace('\\\'', "'", $data['comment']).'</p>';
                            }

                            $this->_html .= '</div>';
                            $this->_html .= '<div class="clear"></div>';
                        }
                    } else {
                        if ($data['category'] == $cat_code) {
                            $important = isset($data['important']) ?
                            '<i class="icon-hand-right" 
                                style="color: #C9302C; font-size: 150%; vertical-align: middle;"></i> ' : '';

                            if (isset($data['useful'])) {
                                $important = '<i class="icon-AdminAdmin" 
                                    style="color: #EC971F; font-size: 150%; vertical-align: middle;"></i>';
                            }

                            $this->_html .= '<div class="form-group" id="azOptions_'.$data['title'].'">';
                            $this->_html .= '<label class="control-label col-lg-3">'
                                .$important.$data['title'].' : </label>';

                            $this->_html .= '<div class="col-lg-9 az_opt_backend" 
                                data-o="'.(isset($data['title']) ? $data['title'] : '').'">'
                                .$this->formField($key, $data).'</div>';

                            $div_warn = '<div class="col-lg-9 col-lg-offset-3"><div class="alert-warning">
                                <h4 style="padding: 5px;">';

                            if (isset($data['isJsObject'])) {
                                $this->_html .= $div_warn
                                .$this->l('Attention: you are editing JavaScript <b>object</b>! Errors will lead to AJAX-ZOOM not working properly.')
                                .'</h4></div></div>';
                            } elseif (isset($data['isJsArray'])) {
                                $this->_html .= $div_warn
                                .$this->l('Attention: you are editing JavaScript <b>array</b>! Errors will lead to AJAX-ZOOM not working properly.')
                                .'</h4></div></div>';
                            } elseif (isset($data['isJsFunction'])) {
                                $this->_html .= $div_warn
                                .$this->l('Attention: you are defining JavaScript <b>function</b>! Errors will lead to AJAX-ZOOM not working properly.')
                                .'</h4></div></div>';
                            }

                            if (!empty($data['comment'])) {
                                $this->_html .= '<div class="col-lg-9 col-lg-offset-3"><div class="help-block">'.
                                html_entity_decode(str_replace('\\\'', "'", $data['comment'])).
                                '</div></div>';
                            }

                            $this->_html .= '</div>';
                        }
                    }
                }
            }

            if (_PS_VERSION_ >= 1.6) {
                $this->_html .= '</fieldset><div class="panel-footer">';
                $this->_html .= '<button name="submitSave" type="submit" 
                    value="'.$this->l('Save').'" class="_button btn btn-default pull-right">';
                $this->_html .= '<i class="process-icon-save"></i> '
                    .$this->l('Save').'</button></div><div class="clear">&nbsp;</div>';
            } else {
                $this->_html .= '</fieldset><div style="height: 5px;">&nbsp;</div>';
                $this->_html .= '<div style="text-align: right;">
                    <input class="button" type="submit" value="'.$this->l('Save').'" name="submitSave">
                </div>';
                $this->_html .= '<div style="height: 20px;">&nbsp;</div>';
            }
        }

        // Server info
        $extensions = get_loaded_extensions();
        $ioncube = false;

        foreach ($extensions as $v) {
            if (stristr($v, 'ioncube')) {
                $ioncube = true;
            }
        }

        if (_PS_VERSION_ >= 1.6) {
            $icon_class = 'fa fa-server';
            $this->_html .= '<fieldset>';
            $this->_html .= '<div class="panel-heading">';
            $this->_html .= '<a href="javascript:void(0)" class="toggleMenuSign" 
                style="margin-right: 30px; outline: 0;">';

            $this->_html .= '<i class="icon-minus-sign"></i></a> ';
            $this->_html .= '<span class="toggleMenuSign1"><i class="'.$icon_class.'" style="margin-right: 5px;"></i> '
                .$this->l('Server / PHP information').'</span>';

            $this->_html .= '</div>';
        } else {
            $icon_cat = '../img/t/AdminOrderPreferences.gif';
            $this->_html .= '<fieldset><legend><img src="'.$icon_cat.'"> '
                .$this->l('Server / PHP information').'</legend>';
        }

        if (_PS_VERSION_ >= 1.6) {
            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">'.$this->l('PHP version').' : </label>';
            $this->_html .= '<div class="col-lg-9"><div class="help-block"> '.phpversion().' </div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">'.$this->l('Sapi').' : </label>';
            $this->_html .= '<div class="col-lg-9"><div class="help-block"> '.PHP_SAPI.' </div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">'.$this->l('Ioncube installed').' : </label>';
            $this->_html .= '<div class="col-lg-9"><div class="help-block"> '
                .($ioncube ? $this->l('yes') : $this->l('no')).' </div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">'.$this->l('disable_functions').' : </label>';
            $this->_html .= '<div class="col-lg-9"><div class="help-block"> '
                .implode(', ', explode(',', ini_get('disable_functions'))).' </div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">'.$this->l('PHP extensions').' : </label>';
            $this->_html .= '<div class="col-lg-9"><div class="help-block">'
                .implode(', ', $extensions).' </div></div>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="form-group">';
            $this->_html .= '<label class="control-label col-lg-3">'.$this->l('Error reporting').' : </label>';
            $this->_html .= '<div class="col-lg-9"><div class="help-block">'
                .$this->errorReportCodes(ini_get('error_reporting')).' </div></div>';
            $this->_html .= '</div>';
        } else {
            $this->_html .= '<label>'.$this->l('PHP version').' : </label>';
            $this->_html .= '<div class="margin-form">';
            $this->_html .= phpversion();
            $this->_html .= '</div>';
            $this->_html .= '<div class="clear"></div>';

            $this->_html .= '<label>'.$this->l('Sapi').' : </label>';
            $this->_html .= '<div class="margin-form">';
            $this->_html .= PHP_SAPI;
            $this->_html .= '</div>';
            $this->_html .= '<div class="clear"></div>';

            $this->_html .= '<label>'.$this->l('Ioncube installed').' : </label>';
            $this->_html .= '<div class="margin-form">';
            $this->_html .= $ioncube ? $this->l('yes') : $this->l('no');
            $this->_html .= '</div>';
            $this->_html .= '<div class="clear"></div>';

            $this->_html .= '<label>'.$this->l('disable_functions').' : </label>';
            $this->_html .= '<div class="margin-form">';
            $this->_html .= implode(', ', explode(',', ini_get('disable_functions')));
            $this->_html .= '</div>';
            $this->_html .= '<div class="clear"></div>';

            $this->_html .= '<label>'.$this->l('PHP extensions').' : </label>';
            $this->_html .= '<div class="margin-form">';
            $this->_html .= implode(', ', $extensions);
            $this->_html .= '</div>';
            $this->_html .= '<div class="clear"></div>';
        }

        $this->_html .= '</fieldset>';

        $this->_html .= '<div class="clear">&nbsp;</div>';
        $this->_html .= '<div class="clear">&nbsp;</div>';

        if (_PS_VERSION_ >= 1.6) {
            $icon_class = 'fa fa-cloud-download';
            $this->_html .= '<fieldset>';
            $this->_html .= '<div class="panel-heading">';
            $this->_html .= '<a href="javascript:void(0)" class="toggleMenuSign" 
                style="margin-right: 30px; outline: 0;">';

            $this->_html .= '<i class="icon-minus-sign"></i></a> ';
            $this->_html .= '<span class="toggleMenuSign1"><i class="'.$icon_class.'" style="margin-right: 5px;"></i> '
                .$this->l('Update AJAX-ZOOM core').'</span>';

            $this->_html .= '</div>';
            $this->_html .= '<div class="form-group">';

            $this->_html .= '<button class="_button btn btn-default" id="az_update_request">
                '.$this->l('Check for available updates').'
                </button>';

            $this->_html .= '</div>';
        } else {
            $icon_cat = '../img/t/AdminOrderPreferences.gif';
            $this->_html .= '<fieldset><legend><img src="'.$icon_cat.'"> '
                .$this->l('Update AJAX-ZOOM core').'</legend>';

            $this->_html .= '<div>';

            $this->_html .= '<button class="_button btn btn-default" id="az_update_request">
                '.$this->l('Check for available updates').'
                </button>';

            $this->_html .= '</div>';

            $this->_html .= '<div class="clear"></div>';
        }

        $this->_html .= '</fieldset>';


        if (_PS_VERSION_ >= 1.6) {
            $this->_html .= '<div class="clear">&nbsp;</div>';
        }

        $this->_html .= '</form>';

        $this->_html .= '
<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license        https://www.ajax-zoom.com/index.php?cid=download
*/
;(function($) {
    $("#az_update_request").bind("click", function(e) {
        e.preventDefault();
        $.ajax( {
            url: "'.AjaxZoom::getBaseDir().'index.php",
            dataType : "html",
            data: {
                "action": "updateAzReq",
                "token": "'.Tools::safeOutput(Tools::getValue('token')).'",
                "fc": "module",
                "module": "ajaxzoom",
                "controller": "image360",
                "ajax": 1
            },
            type: "GET",
            success: function(data) {
                $("#az_update_request").parent().html(data);
            }
        } );
    } );
})(jQuery);
</script>';
        if (_PS_VERSION_ >= 1.6) {
            $this->_html .= '
<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license        https://www.ajax-zoom.com/index.php?cid=download
*/
;(function($) {
    $.azFindOption = function(s) {
        var f = $("#azOptions_" + s);
        if (f.length) {
            if (!f.is(":visible")) {
                var fs = f.closest("fieldset").find(".toggleMenuSign").trigger("click");
                setTimeout(function() {
                    $(window).scrollTop(f.offset()["top"] - 150);
                }, togt + 10 );
            } else {
                $(window).scrollTop(f.offset()["top"] - 150);
            }
        }
    };

    $("#configForm").submit(function(e) {
        var sections = [];
        $(".az_toggleDiv", $("#configForm"))
        .each(function() {
            if ($(this).data("display") != "none") {
                sections.push($(this).data("mn"))
            }
        } );
        var str = sections.join(",");
        str += "&top="+$(window).scrollTop();
        $("#configForm").attr("action", $("#configForm").attr("action") + str);
    } );

    var mn = 0;
    var mns = "'.(Tools::getIsset('section') ? Tools::getValue('section') : '').'".split(",");
    var tt = parseInt("'.(Tools::getIsset('top') ? Tools::getValue('top') : '0').'");
    var togt = 0;

    $("fieldset", $("#configForm"))
    .each(function() {
        mn++;
        var _this = $(this);
        var _thisNext = $(this).next();

        $(".form-group", _this)
        .wrapAll("<div class=az_toggleDiv></div>");

        var az_toggleDiv = $(".az_toggleDiv", _this)
        .data("mn", mn);

        $(".toggleMenuSign, .toggleMenuSign1", _this)
        .css("cursor", "pointer")
        .bind("click", function() {
            az_toggleDiv.toggle( {
                duration: togt,
                start: function() {
                    _thisNext.toggle(200);
                    //icon-minus-sign, icon-plus-sign, icon-chevron-right, icon-chevron-down
                    $(".toggleMenuSign>i", _this)
                    .removeAttr("class")
                    .addClass(az_toggleDiv.data("display") == "none" ? 
                    "icon-minus-sign" : 
                    "icon-plus-sign");
                },
                complete: function() {
                    az_toggleDiv.data("display", az_toggleDiv.css("display"))
                }
            } );
        } )
    } );

    togt = 200;
    mn = 0;
    $("#configForm .toggleMenuSign")
    .each(function() {
        mn++;
        if ($.inArray( mn + "", mns ) == -1) {
            $(this).trigger("click");
        }
    } );

    if (tt > 0) {
        $(document).ready(function() {
            setTimeout(function() {
                $(window).scrollTop(tt)
            }, 0 );
        });
    }

    $(document).ready(function() {
        var a = [];
        var s = $("<select />");
        s.append("<option value=\"\" selected>Options (choose to find)</option>")
        $(".az_opt_backend").each(function() {
            a.push($(this).attr("data-o"));
        } );

        a.sort();

        $(a).each(function(b, c) {
            s.append("<option value="+c+">"+c+"</option>");
        } );

        s
        .css( {
            "max-width": 200,
            "display": "inline-block",
            "margin-left": 10,
            "margin-top": 3,
            "padding": 0,
            "height": "auto"
        } )
        .on("change", function(e) {
            $(this).blur();
            var d = s.val();
            if (d) {
                $.azFindOption(s.val());
            } else {
                $(window).scrollTop(0);
            }
        } )
        .appendTo(".page-subtitle:eq(0)")

        $(".page-head h4.page-subtitle").css("margin-top", 70);
    } );
} )(jQuery);
</script>';
        } else {
            $this->_html .= '
<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license        https://www.ajax-zoom.com/index.php?cid=download
*/
;(function($) {
    $.azFindOption = function(s) {
        var f = $(".margin-form[data-o=\'"+s+"\']");
        if (f.length) {
            $(window).scrollTop(f.offset()["top"] - 50);
        }
    };

} )(jQuery);
</script>';
        }
    }

    public function getAzVersion($return_as_arr = false)
    {
        $txt_file = _PS_ROOT_DIR_.'/modules/ajaxzoom/axZm/readme.txt';
        $version = '';
        $date = '';
        $review = '';

        if (file_exists($txt_file)) {
            $handle = fopen($txt_file, 'r');
            while (($line = fgets($handle)) !== false) {
                if (strstr($line, 'Version:')) {
                    $version = explode(':', $line);
                    $version = trim($version[1]);
                }

                if (strstr($line, 'Date:')) {
                    $date = explode(':', $line);
                    $date = trim($date[1]);
                }

                if (strstr($line, 'Review:')) {
                    $review = explode(':', $line);
                    $review = trim($review[1]);
                }
            }
        }

        $return_arr = array(
            'version' => $version,
            'date' => $date,
            'review' => $review
        );

        if ($return_as_arr === true) {
            return $return_arr;
        }

        return Tools::jsonEncode($return_arr);
    }

    public function getAzAvailVersion()
    {
        $output_az = Tools::file_get_contents('https://www.ajax-zoom.com/getlatestversion.php');
        if ($output_az != false) {
            return (array)Tools::jsonDecode($output_az);
        } else {
            return array('error' => 1);
        }
    }

    private function formField($key, $data)
    {
        $default = Configuration::get($key);
        if ($default == false) {
            $default = isset($data['default']) ? $data['default'] : false;
        }

        $type = isset($data['type']) ? $data['type'] : false;

        switch ($type) {
            case 'select':
                $html = '<select name="'.$key.'">';
                foreach ($data['values'] as $key => $value) {
                    $html .= '<option value="'.$value[0].'" '.($default == $value[0] ? ' selected' : '').'>'
                        .$value[1].'</option>';
                }

                $html .= '</select>';
                break;

            case 'switch':
                if (_PS_VERSION_ < 1.6) {
                    $html = '<input type="radio" name="'.$key.'" id="'.$key.'_on" 
                        value="true" '.($default == 'true' ? 'checked="checked"' : '').'/>';
                    $html .= '<label class="t" for="'.$key.'_on"><img title="Yes" 
                        alt="Yes" src="../img/admin/enabled.gif"></label>';
                    $html .= '<input type="radio" name="'.$key.'" id="'.$key.'_off" 
                        value="false" '.($default == 'false' ? 'checked="checked"' : '').'/>';
                    $html .= '<label class="t" for="'.$key.'_off"><img title="No" 
                        alt="No" src="../img/admin/disabled.gif"></label>';
                } else {
                    $html = '<div class="bootstrap col-lg-9 ">';
                    $html .= '<span class="switch prestashop-switch fixed-width-lg">';
                    $html .= '<input type="radio" name="'.$key.'" 
                        id="'.$key.'_on" value="true" '.($default == 'true' ? 'checked="checked"' : '').'/>';
                    $html .= '<label for="'.$key.'_on">Yes</label>';
                    $html .= '<input type="radio" name="'.$key.'" 
                        id="'.$key.'_off" value="false" '.($default == 'false' ? 'checked="checked"' : '').'/>';
                    $html .= '<label for="'.$key.'_off">No</label>';
                    $html .= '<a class="slide-button btn"></a>';
                    $html .= '</span>';
                    $html .= '<p class="help-block"></p>';
                    $html .= '</div>';
                }

                break;

            case 'textarea':
                if (_PS_VERSION_ < 1.6) {
                    $html = '<textarea style="width: 500px; 
                        height: '.(isset($data['textareaHeight']) ? $data['textareaHeight'] : '120px').'; 
                        resize: vertical;" name="'.$key.'">';
                    $html .= Tools::safeOutput(Tools::getValue($key, $default)).'</textarea>';
                } else {
                    $html = '<textarea style="height: '.(isset($data['textareaHeight'])
                        ? $data['textareaHeight'] : '120px').'; resize: vertical;" name="'.$key.'">';
                    $html .= Tools::safeOutput(Tools::getValue($key, $default)).'</textarea>';
                }

                break;

            default:
                $html = '<input type="text" style="'.(_PS_VERSION_ < 1.6 ? 'width: 500px' : '').'" name="'.$key.'" 
                    value="'.Tools::safeOutput(Tools::getValue($key, $default)).'" />';

                break;
        }

        return $html;
    }

    public function errorReportCodes($error_number)
    {
        $error_description = array();

        $error_codes = array(
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            E_ALL => 'E_ALL'
        );

        foreach ($error_codes as $number => $description) {
            if (($number & $error_number) == $number) {
                $error_description[] = $description;
            }
        }

        return implode(' | ', $error_description);
    }

    public function getArcList()
    {
        $files = array();

        if ($handle = opendir(_PS_ROOT_DIR_.'/'.Ajaxzoom::$arcdir)) {
            while (false !== ($entry = readdir($handle))) {
                if (Tools::substr($entry, 0, 1) != '.'
                && $entry != '__MACOSX'
                && (Tools::strtolower(Tools::substr($entry, -3)) == 'zip'
                || is_dir(_PS_ROOT_DIR_.'/'.Ajaxzoom::$arcdir.$entry))
                ) {
                    array_push($files, $entry);
                }
            }

            closedir($handle);
        }

        return $files;
    }

    public static function extractArc($file)
    {
        $zip = new ZipArchive;
        $res = $zip->open($file);

        if ($res === true) {
            $folder = uniqid(getmypid());
            $path = _PS_ROOT_DIR_.'/'.Ajaxzoom::$tmpdir.'ajaxzoom/'.$folder;
            mkdir($path, 0777);
            @chmod($path, 0777); // errors are handled elsewhere
            $zip->extractTo($path);
            $zip->close();
            return $path;
        } else {
            return false;
        }
    }

    public static function getFolderData($path)
    {
        $files = array();
        $folders = array();

        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if (Tools::substr($entry, 0, 1) != '.' && $entry != '__MACOSX') {
                    if (is_dir($path.'/'.$entry)) {
                        array_push($folders, $entry);
                    } else {
                        array_push($files, $entry);
                    }
                }
            }

            closedir($handle);
        }

        sort($folders);
        sort($files);

        return array(
            'folders' => $folders,
            'files' => $files
        );
    }

    private function postProcess()
    {
        // Saving new configurations
        foreach (array_keys($this->fields_list) as $key) {
            if ($this->ps_version_15) {
                if ($key && Tools::strlen($key) > 32) {
                    continue;
                }
            }

            Configuration::updateValue($key, Tools::getValue($key));
        }

        $this->saveLicenses();
    }

    public function saveLicenses()
    {
        $licenses = array();
        $tmp = Tools::getValue('licenses');
        $count_domain = count($tmp['domain']);

        for ($i = 0; $i < $count_domain; $i++) {
            if ($tmp['domain'][$i] == 'domain_placeholder') {
                continue;
            }

            $licenses[] = array(
                'domain' => $tmp['domain'][$i],
                'type' => $tmp['type'][$i],
                'key' => $tmp['key'][$i],
                'error200' => $tmp['error200'][$i],
                'error300' => $tmp['error300'][$i]
            );
        }

        Configuration::updateValue('AJAXZOOM_LICENSE', Tools::jsonEncode($licenses));
    }

    public function configVendor()
    {
        $this->config_vendor = array(
            'videoHtml5VideoJs' => false,
            'oneSrcImg' => true,
            'heightRatioOneImg' => 1.0,
            'width' => 800,
            'height' => 800,
            'thumbSliderPosition' => 'bottom',
            'galleryAxZmThumbSliderParamHorz' => '{
    "thumbLiStyle": {
        "borderRadius": 3
    },
    "btn": true,
    "btnClass": "axZmThumbSlider_button_new",
    "btnHidden": true,
    "btnOver": false,
    "scrollBy": 1,
    "centerNoScroll": true
}',
            'galleryAxZmThumbSliderParamVert' => '{
    "thumbLiStyle": {
        "borderRadius": 3
    },
    "btn": true,
    "btnClass": "axZmThumbSlider_button_new",
    "btnHidden": true,
    "btnOver": false,
    "scrollBy": 1,
    "centerNoScroll": true
}',
            'axZmCallBacks' => '{
onFullScreenReady: function() {
    // Here you can place you custom code
}
}',
            'cropAxZmThumbSliderParam' => '{
    "btn": true,
    "btnClass": "axZmThumbSlider_button_new",
    "btnHidden": true,
    "centerNoScroll": true,
    "thumbImgWrap": "azThumbImgWrapRound",
    "scrollBy": 1
}',
            'zoomWidth' => '.product-prices|+20',
            'zoomHeight' => '.page-content',
            'showTitle' => true,
            'autoFlip' => 120,
            'fullScreenApi' => true,
            'prevNextArrows' => true,
            'cropSliderThumbAutoMargin' => 7,
            'fsAxZmThumbSliderParam' => '{
    "scrollBy": 1,
    "btn": true,
    "btnClass": "axZmThumbSlider_button_new",
    "btnLeftText": null,
    "btnRightText": null,
    "btnHidden": true,
    "pressScrollSnap": true,
    "centerNoScroll": true,
    "wrapStyle": {
        "borderWidth": 0
    }
}'
        );

        if ($this->ps_version_15 || $this->ps_version_16) {
            $this->config_vendor['zoomWidth'] = (_PS_VERSION_ >= 1.6
                ? '.pb-center-column,.pb-right-column|-16'
                : '#pb-left-column,#right_column|+20');
            $this->config_vendor['zoomHeight'] = (_PS_VERSION_ < 1.6
                ? '375'
                : '.pb-left-column,.pb-center-column,.pb-right-column');
            $this->config_vendor['adjustX'] = (_PS_VERSION_ < 1.6 ? 13 : 15);
        }
    }

    public function getAdminLang()
    {
        $c = Context::getContext();

        if (is_object($c) && isset($c->language) && is_object($c->language) && isset($c->language->iso_code)) {
            return $c->language->iso_code;
        } else {
            return 'en';
        }
    }

    public function loadingVar()
    {
        $this->initAzMouseoverSettings();
        $cfg = $this->mouseover_settings->getConfig();
        $cat = $this->mouseover_settings->getCategories(); // $cat variable is used, see below

        $current_cat = '';
        $field_map = array(
            'textarea' => 'textarea',
            'text' => 'text',
            'switch' => 'switch',
            'select' => 'select'
        );

        $lang = $this->getAdminLang();

        if ($lang) {
            $lang = Tools::strtoupper($lang);
            $lang = Tools::substr($lang, 0, 2);
        } else {
            $lang = 'EN';
        }

        if (!in_array($lang, array('DE', 'EN'))) {
            $lang = 'EN';
        }

        $this->categories = array(
            'license' => $this->l('License')
        );

        foreach ($cfg as $k => $v) {
            $varname = $this->az_opt_prefix.'_'.Tools::strtoupper($k);
            $category = $v['category'];

            if ($category != $current_cat) {
                $current_cat = $category;
                if (!array_key_exists($current_cat, $this->categories)) {
                    $this->categories[$current_cat] = $this->mouseover_settings->cleanComment(
                        $cat[$category]['title'][$lang]
                    );
                }
            }

            $arr_option = array();
            $arr_option['title'] = $k;
            $arr_option['category'] = $category;
            $arr_option['type'] = $field_map[$v['display']];
            $arr_option['comment'] = $this->mouseover_settings->cleanComment($v['comment'][$lang]);

            if (isset($v['default'])) {
                if ($v['default'] === true || $v['default'] === false || $v['default'] === null) {
                    $arr_option['default'] = Tools::strtolower(var_export($v['default'], true));
                } else {
                    $arr_option['default'] = $v['default'];
                }
            } else {
                $arr_option['default'] = '';
            }

            if ($v['display'] == 'select' && is_array($v['options']) && !empty($v['options'])) {
                $arr_option['values'] = $v['options'];
            }

            if (isset($v['isJsObject']) && $v['isJsObject'] == true) {
                $arr_option['isJsObject'] = true;
            }

            if (isset($v['isJsArray']) && $v['isJsArray'] == true) {
                $arr_option['isJsArray'] = true;
            }

            if (isset($v['important']) && $v['important'] == true) {
                $arr_option['important'] = true;
            }

            if (isset($v['useful']) && $v['useful'] == true) {
                $arr_option['useful'] = true;
            }

            $this->fields_list[$varname] = $arr_option;
        }
    }

    public function getUserBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $user_browser = 'unknown';

        if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
            $user_browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $user_browser = 'Mozilla Firefox';
        } elseif (preg_match('/Chrome/i', $user_agent)) {
            $user_browser = 'Google Chrome';
        } elseif (preg_match('/Safari/i', $user_agent)) {
            $user_browser = 'Apple Safari';
        } elseif (preg_match('/Opera/i', $user_agent)) {
            $user_browser = 'Opera';
        } elseif (preg_match('/Netscape/i', $user_agent)) {
            $user_browser = 'Netscape';
        }

        return $user_browser;
    }

    public function productHasVideoHtml5($vid)
    {
        $has = false;
        $vid = (array)Tools::jsonDecode($vid, true);
        if (!empty($vid)) {
            foreach ($vid as $v) {
                if ($v['type'] == 'videojs') {
                    $has = true;
                    break;
                }
            }
        }

        if ($this->_config[$this->az_opt_prefix.'_VIDEOHTML5VIDEOJS'] == 'true' && $has == true) {
            return true;
        } else {
            return false;
        }
    }

    public static function isOctal($x)
    {
        return Tools::strlen($x) > 1 && decoct(octdec($x)) == $x;
    }

    public static function prepareProductSettings($str, $as_obj = false)
    {
        $res = array();

        if (is_array($str)) {
            $settings = $str;
        } else {
            $settings = json_decode($str, true);
        }

        foreach ($settings as $key => $value) {
            $value = trim($value);
            $isnum = is_numeric($value);
            if ($isnum && Ajaxzoom::isOctal($value)) {
                continue;
            }

            $value = str_replace(array("\r", "\n", "\t"), '', $value);
            $value = str_replace('\\', '', $value);
            $value = str_replace('\'', '&#39;', $value);

            if ($value == 'false'
                || $value == 'true'
                || $value == 'null'
                || $isnum
                || Tools::substr($value, 0, 1) == '{'
                || Tools::substr($value, 0, 1) == '['
            ) {
                $res[] = '"'.$key.'":'.$value;
            } else {
                $res[] = '"'.$key.'":"'.$value.'"';
            }
        }

        if ($as_obj) {
            return '{'.implode(',', $res).'}';
        } else {
            return implode(',', $res);
        }
    }

    public function getPluginOptNameLabel($flip = false)
    {
        $opt_arr = array();
        $excl_arr = array(
            'enableInFrontDetail',
            'displayOnlyForThisProductID',
            'defaultVideoVideojsJS',
            'uploadNoCompress',
            'default360settings',
            'pngModeCssFix',
            'headerZindex'
        );

        $this->initAzMouseoverSettings();
        $cfg = $this->mouseover_settings->getConfig();
        $cat = $this->mouseover_settings->getCategories();

        foreach ($cfg as $k => $v) {
            if (in_array($k, $excl_arr)) {
                continue;
            }

            if (!$v) {
                $v = ''; // useless code
            }

            $varname = $this->az_opt_prefix.'_'.Tools::strtoupper($k);
            $opt_arr[$varname] = $k;
        }

        if ($flip === true) {
            return array_flip($opt_arr);
        }

        return $opt_arr;
    }

    public static function getProductPluginOpt($id_product = 0)
    {
        if (!Ajaxzoom::tableExists('ajaxzoomproductsettings')) {
            Ajaxzoom::alterTable('add');
            return '{}';
        }

        $conf = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoomproductsettings`
            WHERE id_product = '.(int)$id_product);

        if (isset($conf[0]) && isset($conf[0]['psettings'])) {
            return $conf[0]['psettings'];
        } else {
            return '{}';
        }
    }

    public static function tableExists($t)
    {
        $chk = Db::getInstance()->ExecuteS('SHOW TABLES LIKE \''._DB_PREFIX_.$t.'\'');
        if (!$chk || ($chk && (!is_array($chk) || !isset($chk[0])))) {
            return false;
        }

        return true;
    }

    public function extendProductIndividualSettings($cfg, $id_product)
    {
        if (!$this->extended_product_settings) {
            $this->extended_product_settings = true;
            $ccfg = Ajaxzoom::getProductPluginOpt($id_product);
            $prefix = $this->az_opt_prefix ? $this->az_opt_prefix.'_' : '';

            if ($ccfg) {
                $ccfg = (array)Tools::jsonDecode($ccfg);

                if (is_array($ccfg) && !empty($ccfg) && is_array($cfg)) {
                    foreach ($ccfg as $k => $v) {
                        $k = Tools::strtoupper($k);
                        if (isset($cfg[$prefix.$k])) {
                            $cfg[$prefix.$k] = $v;
                        }
                    }
                }
            }
        }

        return $cfg;
    }
}

Ajaxzoom::$tmpdir = 'img/tmp/';
Ajaxzoom::$arcdir = 'modules/ajaxzoom/zip/';
Ajaxzoom::$axzmh;
Ajaxzoom::$zoom;
