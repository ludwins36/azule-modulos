<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, AdminProductsController.php
*
*  Copyright: Copyright (c) 2010-2018 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.16.0
*  Date: 2018-10-31
*  Review: 2018-10-31
*  URL: http://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

class AdminProductsController extends AdminProductsControllerCore
{
    public function addProductImage360($product, $method = 'auto')
    {
        if ($product && $method) {
            print 'save';
        } else {
            print 'save';
        }

        exit;
    }
}
