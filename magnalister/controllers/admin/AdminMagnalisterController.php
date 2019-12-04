<?php
/**
 *
 * @author magnalister
 * @copyright 2010-2017 RedGecko GmbH -- http://www.redgecko.de
 * @license Released under the MIT License (Expat)
 */

class AdminMagnalisterController extends AdminController
{

    private $s_rendered_html = '';

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    public function initContent()
    {
        if (Tools::getValue('action') == 'resizeImage') {
            //just resize image and create new image in img/magnalister/product
            $this->resizeImage();
            die();
        }
        parent::initContent();
        define('_LANG_ISO_', $this->context->language->iso_code);
        define('_LANG_ID_', $this->context->language->id);
        $plugin_path = dirname(__FILE__).'/../../lib/Core/ML.php';
        if (!file_exists($plugin_path) && file_exists(dirname(__FILE__).'/../../../../magnalister/Core/ML.php')) {//is for git of magnalister
            $plugin_path = dirname(__FILE__).'/../../../../magnalister/Core/ML.php';
        }
        require_once $plugin_path;
        $admin_dir = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_dir);
        define('_PS_ADMIN_PATH_', __PS_BASE_URI__.$admin_webpath.'/');
        if ($this->s_rendered_html == '') {
            $this->s_rendered_html = ML::gi()->run();
        }
        /* @var $s_client_version string will be added to url as parameter to avoid browser-cache */
        $s_client_version = MLSetting::gi()->get('sClientBuild');
        $bl_absolute = (int)MLShop::gi()->getShopVersion() >= 6;
        foreach (MLSetting::gi()->get('aCss') as $s_file) {
            $css_path = MLHttp::gi()->getResourceUrl('css_'.$s_file, $bl_absolute);
            $this->addCSS(sprintf($css_path, $s_client_version), 'all');
        }
        foreach (MLSetting::gi()->get('aJs') as $s_file) {
            $this->addJs(sprintf(MLHttp::gi()->getResourceUrl('js_'.$s_file, $bl_absolute), $s_client_version));
        }
        if (MLRequest::gi()->data('ajax')) {
            exit();
        }
        $script_add_body_class = "<script type='text/javascript'> $(document).ready(function(){ $('body')";
        foreach (MLSetting::gi()->get('aBodyClasses') as $s_class) {
            $script_add_body_class .= ".addClass('$s_class')";
        }
        $script_add_body_class .= '});</script>';
        $this->context->smarty->assign('magnalister', $this->s_rendered_html.$script_add_body_class);
        $this->setTemplate('magnalister.tpl');
    }

    public function createTemplate($tpl_name)
    {
        $s_filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.$tpl_name;
        return $this->context->smarty->createTemplate($s_filename, $this->context->smarty);
    }

    protected function resizeImage()
    {
        $a_image = Tools::getValue('ml');
        ImageManager::resize($a_image['src'], $a_image['dst'], $a_image['x'], $a_image['y']);
    }
}
