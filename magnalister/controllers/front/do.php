<?php
/**
 *
 * @author magnalister
 * @copyright 2010-2017 RedGecko GmbH -- http://www.redgecko.de
 * @license Released under the MIT License (Expat)
 */

class MagnalisterDoModuleFrontController extends ModuleFrontController
{
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        define('_LANG_ISO_', $this->context->language->iso_code);
        define('_LANG_ID_', $this->context->language->id);
        $plugin_path = dirname(__FILE__).'/../../lib/Core/ML.php';
        if (!file_exists($plugin_path) && file_exists(dirname(__FILE__).'/../../../../magnalister/Core/ML.php')) {//is for git of magnalister
            $plugin_path = dirname(__FILE__).'/../../../../magnalister/Core/ML.php';
        }
        require_once $plugin_path;
        ML::gi()->runFrontend('do');
    }
}
