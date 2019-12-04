<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, AdminAjaxzoomController.php
*
*  Copyright: Copyright (c) 2010-2019 Vadim Jacobi
*  License Agreement: https://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.20.0
*  Date: 2019-06-18
*  Review: 2019-06-18
*  URL: https://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: https://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license   https://www.ajax-zoom.com/index.php?cid=download
*/

class AdminAjaxzoomController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->template = 'batchtool.tpl';
    }

    public function createTemplate($tpl_name)
    {
        $ds = DIRECTORY_SEPARATOR;

        if ($this->viewAccess() && $this->override_folder) {
            $tpl_var = $this->override_folder.$tpl_name;

            if (file_exists($this->context->smarty->getTemplateDir(1).$ds.$tpl_var)) {
                return $this->context->smarty->createTemplate(
                    $tpl_var,
                    $this->context->smarty
                );
            } elseif (file_exists($this->context->smarty->getTemplateDir(0).'controllers'.$ds.$tpl_var)) {
                return $this->context->smarty->createTemplate(
                    'controllers'.$ds.$tpl_var,
                    $this->context->smarty
                );
            }
        }

        $this->context->smarty->assign(array(
            'base_url' => AjaxZoom::getBaseDir(),
            'base_uri' => __PS_BASE_URI__,
            'token' => Tools::safeOutput(Tools::getValue('token')),
            'ps_version' => _PS_VERSION_
        ));

        return $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_.'ajaxzoom/views/templates/admin/'.$tpl_name,
            $this->context->smarty
        );
    }
}
