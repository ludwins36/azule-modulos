<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, image360.php
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

if (!class_exists('FileUploaderCore')) {
    require_once(_PS_ROOT_DIR_.'/FileUploader.php');
}

class AjaxZoomImage360ModuleFrontController extends ModuleFrontController
{
    public $max_image_size = 999999999;
    public $az_opt_prefix;

    public function initContent()
    {
        $cookies = new Cookie('psAdmin');

        if (!$cookies->id_employee) {
            echo 'You are not logged in as administrator. Operation has been aborted.';
            exit;
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->az_opt_prefix = '';
        } else {
            $this->az_opt_prefix = 'AJAXZOOM';
        }

        if (Tools::getValue('action') == 'addProductImage360') {
            $this->uploadImage();
        } elseif (Tools::getValue('action') == 'deleteProductImage360') {
            $this->deleteImage();
        } elseif (Tools::getValue('action') == 'deleteSet') {
            $this->deleteSet();
        } elseif (Tools::getValue('action') == 'addSet') {
            $this->addSet();
        } elseif (Tools::getValue('action') == 'getImages') {
            $this->getImages();
        } elseif (Tools::getValue('action') == 'saveSettings') {
            $this->saveSettings();
        } elseif (Tools::getValue('action') == 'setActive') {
            $this->setActive();
        } elseif (Tools::getValue('action') == 'set360Status') {
            $this->set360Status();
        } elseif (Tools::getValue('action') == 'getCropJSON') {
            $this->getCropJSON();
        } elseif (Tools::getValue('action') == 'saveCropJSON') {
            $this->saveCropJSON();
        } elseif (Tools::getValue('action') == 'saveHotspotJSON') {
            $this->saveHotspotJSON();
        } elseif (Tools::getValue('action') == 'getHotspotJSON') {
            $this->getHotspotJSON();
        } elseif (Tools::getValue('action') == 'clearAzImageCache') {
            $this->clearAzImageCache();
        } elseif (Tools::getValue('action') == 'addVideo') {
            $this->addVideo();
        } elseif (Tools::getValue('action') == 'deleteVideo') {
            $this->deleteVideo();
        } elseif (Tools::getValue('action') == 'setVideoStatus') {
            $this->setVideoStatus();
        } elseif (Tools::getValue('action') == 'saveSettingsVideo') {
            $this->saveSettingsVideo();
        } elseif (Tools::getValue('action') == 'updateAz') {
            $this->updateAz();
        } elseif (Tools::getValue('action') == 'updateAzReq') {
            $this->updateAzReq();
        } elseif (Tools::getValue('action') == 'refreshPicturesList') {
            $this->refreshPicturesList();
        } elseif (Tools::getValue('action') == 'getHotspotPictureJSON') {
            $this->getHotspotPictureJSON();
        } elseif (Tools::getValue('action') == 'saveHotspotPictureJSON') {
            $this->saveHotspotPictureJSON();
        } elseif (Tools::getValue('action') == 'setHotspotStatus') {
            $this->setHotspotStatus();
        } elseif (Tools::getValue('action') == 'saveProductAzSettings') {
            $this->saveProductAzSettings();
        }

        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();
    }

    public function saveCropJSON()
    {
        $tmp = Ajaxzoom::setCropJSON(Tools::getValue('id_360'), Tools::getValue('json'));

        die(Tools::jsonEncode(array(
            'status' => $tmp
        )));
    }

    public function getCropJSON()
    {
        echo AjaxZoom::getCropJSON(Tools::getValue('id_360'));
        exit;
    }

    public function saveHotspotJSON()
    {
        $tmp = Ajaxzoom::setHotspotJSON(Tools::getValue('id_360'), Tools::getValue('json'));

        die(Tools::jsonEncode(array(
            'status' => $tmp
        )));
    }

    public function getHotspotJSON()
    {
        echo AjaxZoom::getHotspotJSON(Tools::getValue('id_360'));
        exit;
    }

    public function getTmpDir()
    {
        return $this->tmpdir;
    }

    public function clearAzImageCache()
    {
        $filename = (int)Tools::getValue('deletedImgID').'.jpg';
        AjaxZoom::deleteImageAZcache($filename);

        die(Tools::jsonEncode(array(
            'status' => 'ok',
            'confirmations' => array('AJAX-ZOOM cache has been deleted for image with ID '
                .(int)Tools::getValue('deletedImgID'))
        )));
    }

    public function set360Status()
    {
        $status = (int)Tools::getValue('status');
        $id_360 = (int)Tools::getValue('id_360');
        Ajaxzoom::set360status($id_360, $status);

        die(Tools::jsonEncode(array(
            'status' => 'ok',
            'confirmations' => array('The status has been updated.'.$status.'-'.$id_360)
        )));
    }

    public function addSet()
    {
        $id_product = (int)Tools::getValue('id_product');
        $name = Tools::getValue('name');

        if (empty($name)) {
            $name = 'Unnamed '.uniqid(getmypid());
        }

        $existing = Tools::getValue('existing');
        $zip = Tools::getValue('zip');
        $delete = Tools::getValue('delete');
        $arcfile = Tools::getValue('arcfile');
        $new_id = '';
        $new_name = '';
        $new_settings = '';

        if (!empty($existing)) {
            $id_360 = $existing;
            $tmp = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360`
                WHERE id_360 = '.(int)$id_360);

            $name = $tmp[0]['name'];
        } else {
            $new_settings = $settings = Configuration::get($this->az_opt_prefix.'_DEFAULT360SETTINGS');

            Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'ajaxzoom360` (id_product, name, settings, status)
                VALUES(\''.$id_product.'\', \''.pSQL($name).'\', \''.$settings.'\', \''.($zip == 'true' ? 1 : 0).'\')');

            $id_360 = (int)Db::getInstance()->Insert_ID();
            $new_id = $id_360;
            $new_name = $name;
        }

        Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'ajaxzoom360set` (id_360, sort_order)
            VALUES(\''.$id_360.'\', 0)');

        $id_360set = (int)Db::getInstance()->Insert_ID();

        $sets = array();

        if ($zip == 'true') {
            $sets = $this->addImagesArc($arcfile, $id_product, $id_360, $id_360set, $delete);
        }

        die(Tools::jsonEncode(array(
            'status' => '0',
            'name' => $name,
            'path' => Ajaxzoom::getBaseDir().'modules/ajaxzoom/views/img/no_image-100x100.jpg',
            'sets' => $sets,
            'id_360' => $id_360,
            'id_product' => $id_product,
            'id_360set' => $id_360set,
            'confirmations' => array('The image set was successfully added.'),
            'new_id' => $new_id,
            'new_name' => $new_name,
            'new_settings' => urlencode($new_settings)
        )));
    }

    public function deleteSet()
    {
        $id_360set = (int)Tools::getValue('id_360set');
        $id_360 = Ajaxzoom::getSetParent($id_360set);
        $id_product = Ajaxzoom::getSetProduct($id_360);

        Ajaxzoom::deleteSet($id_360set);

        die(Tools::jsonEncode(array(
            'id_360set' => $id_360set,
            'id_360' => $id_360,
            'path' => _PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360.'/'.$id_360set,
            'removed' => (Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360`
                WHERE id_360 = '.$id_360) ? 0 : 1),
            'confirmations' => array('The 360 image set was successfully removed.')
        )));
    }

    public function deleteImage()
    {
        $id_image = Tools::safeOutput(Tools::getValue('id_image'));
        $id_product = Tools::safeOutput(Tools::getValue('id_product'));
        $id_360set = Tools::safeOutput(Tools::getValue('id_360set'));
        $id_360 = Ajaxzoom::getSetParent($id_360set);
        $tmp = explode('&', Tools::getValue('ext'));
        $ext = Tools::safeOutput(reset($tmp));
        $filename = $id_image.'.'.$ext;

        $dst = _PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360.'/'.$id_360set.'/'.$filename;
        unlink($dst);

        AjaxZoom::deleteImageAZcache($filename);

        die(Tools::jsonEncode(array(
            'status' => 'ok',
            'content' => (object)array('id' => $id_image),
            'confirmations' => array('The image was successfully deleted.')
        )));
    }

    public function getImages()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_360set = (int)Tools::getValue('id_360set');
        $images = Ajaxzoom::get360Images($id_product, $id_360set);

        die(Tools::jsonEncode(array(
            'status' => 'ok',
            'id_product' => $id_product,
            'id_360set' => $id_360set,
            'images' => $images
        )));
    }

    public function saveSettings()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_360 = (int)Tools::getValue('id_360');
        //$active = (int)Tools::getValue('active');
        $names = explode('|', Tools::getValue('names'));
        $values = explode('|', Tools::getValue('values'));
        $combinations = explode('|', Tools::getValue('combinations'));
        $count_names = count($names);
        $settings = array();

        for ($i = 0; $i < $count_names; $i++) {
            $key = $names[$i];
            $value = $values[$i];
            if ($key != 'name_placeholder' && !empty($key)) {
                $settings[$key] = $value;
            }
        }

        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoom360`
            SET settings = \''.Tools::jsonEncode($settings).'\', combinations = \''.implode(',', $combinations).'\'
            WHERE id_360 = '.(int)$id_360);

        // update dropdown
        $sets_groups = Ajaxzoom::getSetsGroupsStatic($id_product);
        $select = '<select id="id_360" name="id_360" class="form-control"><option value="">Select</option>';
        foreach ($sets_groups as $group) {
            $select .= '<option value="'.$group['id_360'].'" ';
            $select .= 'data-settings="'.urlencode($group['settings']).'" ';
            $select .= 'data-combinations="['.urlencode($group['combinations']).']">'.$group['name'].'</option>';
        }

        $select .= '</select>';

        die(Tools::jsonEncode(array(
            'status' => 'ok',
            'select' => $select,
            'id_product' => $id_product,
            'id_360' => $id_360,
            'confirmations' => array('The settings have been updated.')
        )));
    }

    public function setActive()
    {
        $id_product = (int)Tools::getValue('id_product');
        $active = (int)Tools::getValue('active');

        // active/not active
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomproducts` WHERE id_product = '
            .(int)$id_product);

        if ($active == 0) {
            Db::getInstance()->Execute('INSERT INTO  `'._DB_PREFIX_.'ajaxzoomproducts` (id_product) VALUES('
                .(int)$id_product.')');
        }

        die(Tools::jsonEncode(array(
            'status' => 'ok',
            'id_product' => $id_product,
            'confirmations' => array('The settings have been updated.')
        )));
    }

    public function uploadImage()
    {
        // Works this way???
        $post_legend = (array)Tools::getValue('legend');
        $id_product = Tools::getValue('id_product');

        if (isset($post_legend['777'])) {
            $id_360set = (int)$post_legend['777'];
        } else {
            $id_360set = Tools::getValue('id_360set');
        }

        $id_360 = Ajaxzoom::getSetParent($id_360set);
        $folder = $this->createProduct360Folder($id_product, $id_360set);

        if (_PS_VERSION_ >= 1.6) {
            $image_uploader = new HelperImageUploader('file360');
            $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize($this->max_image_size);
            $files = $image_uploader->process();
            $fname = $image_uploader->getName();
        } else {
            $path = _PS_ROOT_DIR_.'/img/tmp/'.Tools::getValue('qqfile');
            $qq = new QqUploadedFileXhr();
            $qq->upload($path);

            $files = array(array(
                'name' => Tools::getValue('qqfile'),
                'save_path' => $path,
                'id_product' => $id_product,
                'id_360' => $id_360,
                'id_360set' => $id_360set,
            ));
            $fname = 'file360';
        }

        foreach ($files as &$file) {
            $shops = Shop::getContextListShopID();
            $json_shops = array();

            foreach ($shops as $id_shop) {
                $json_shops[$id_shop] = true;
            }

            $namorg = $file['name'];
            $file['name'] = $id_product.'_'.$id_360set.'_'.$file['name'];
            $file['name'] = $this->imgNameFilter($file['name']);

            $tmp = explode('.', $file['name']);
            $ext = end($tmp);
            $name = preg_replace('|\.'.$ext.'$|', '', $file['name']);
            $dst = $folder.'/'.$file['name'];
            rename($file['save_path'], $dst);

            //_PS_BASE_URL_.__PS_BASE_URI__
            $thumb = Ajaxzoom::getBaseDir().'modules/ajaxzoom/axZm/zoomLoad.php';
            $thumb .= '?azImg='.__PS_BASE_URI__.'img/p/360/'.$id_product.'/'.$id_360.'/'.$id_360set.'/'.$file['name']
                .'&width=100&height=100&thumbMode=contain';

            $file['status'] = 'ok';
            $file['id'] = $name;
            $file['id_product'] = $id_product;
            $file['id_360'] = $id_360;
            $file['id_360set'] = $id_360set;
            $file['path'] = $thumb;
            $file['nameorg'] = $namorg;
            $file['shops'] = $json_shops;
        }

        if (_PS_VERSION_ >= 1.6) {
            die(Tools::jsonEncode(array($fname => $files)));
        } else {
            die(Tools::jsonEncode($files[0]));
        }
    }

    public function imgNameFilter($filename)
    {
        $filename = preg_replace('/[^A-Za-z0-9_\.-]/', '-', $filename);

        return $filename;
    }

    public function copyImages($id_product, $id_360, $id_360set, $path, $move)
    {
        if (!$id_360 && !$id_360set) { // useless code to validate
            return;
        }

        $files = $this->getFilesFromFolder($path);
        $folder = $this->createProduct360Folder($id_product, $id_360set);

        foreach ($files as $file) {
            $name = $id_product.'_'.$id_360set.'_'.$this->imgNameFilter($file);
            $dst = $folder.'/'.$name;

            if ($move) {
                if (@!rename($path.'/'.$file, $dst)) {
                    copy($path.'/'.$file, $dst);
                } // chmod issue. ok!
            } else {
                copy($path.'/'.$file, $dst);
            }
        }
    }

    public function getFilesFromFolder($path)
    {
        $files = array();

        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..' && $entry != '.htaccess' && $entry != '__MACOSX') {
                    $files[] = $entry;
                }
            }

            closedir($handle);
        }

        return $files;
    }

    public function createProduct360Folder($id_product, $id_360set)
    {
        $id_product = (int)$id_product;
        $id_360set = (int)$id_360set;
        $id_360 = Ajaxzoom::getSetParent($id_360set);

        if (!file_exists(_PS_IMG_DIR_.'p/360/'.$id_product)) {
            mkdir(_PS_IMG_DIR_.'p/360/'.$id_product, 0775);
            @chmod(_PS_IMG_DIR_.'p/360/'.$id_product, 0775); // errors are handled elsewhere
        }

        if (!file_exists(_PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360)) {
            mkdir(_PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360, 0775);
            @chmod(_PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360, 0775); // errors are handled elsewhere
        }

        $folder = _PS_IMG_DIR_.'p/360/'.$id_product.'/'.$id_360.'/'.$id_360set;

        if (!file_exists($folder)) {
            mkdir($folder, 0775);
            @chmod($folder, 0775); // errors are handled elsewhere
        } else {
            @chmod($folder, 0775); // errors are handled elsewhere
        }

        return $folder;
    }

    public function addImagesArc($arcfile, $id_product, $id_360, $id_360set, $delete = '')
    {
        set_time_limit(0);

        $path = _PS_ROOT_DIR_.'/'.Ajaxzoom::$arcdir.$arcfile;
        $dst = is_dir($path) ? $path : Ajaxzoom::extractArc($path);

        // when extract zip archive return false
        if ($dst == false) {
            return false;
        }

        if (is_dir($path) && !is_writable($dst) && $delete == 'true') {
            @chmod($dst, 0777);
        } // try it

        $data = Ajaxzoom::getFolderData($dst);

        $tmp = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ajaxzoom360`
            WHERE id_360 = '.(int)$id_360);

        $name = $tmp[0]['name'];

        //_PS_BASE_URL_.__PS_BASE_URI__
        $thumb = Ajaxzoom::getBaseDir().'modules/ajaxzoom/axZm/zoomLoad.php';
        $thumb .= '?qq=1&azImg360='.__PS_BASE_URI__.'img/p/360/'.$id_product.'/'.$id_360.'/'.$id_360set;
        $thumb .= '&width=100&height=100&thumbMode=contain';

        $sets = array(array(
            'name' => $name,
            'path' => $thumb,
            'id_360set' => $id_360set,
            'id_360' => $id_360,
            'status' => '1'
        ));

        $count_data_folders = count($data['folders']);

        $move = is_dir($path) ? false : true;

        if ($count_data_folders == 0) { // files (360)
            $this->copyImages($id_product, $id_360, $id_360set, $dst, $move);
        } elseif ($count_data_folders == 1) { // 1 folder (360)
            $this->copyImages($id_product, $id_360, $id_360set, $dst.'/'.$data['folders'][0], $move);
        } else {
            // 3d
            $this->copyImages($id_product, $id_360, $id_360set, $dst.'/'.$data['folders'][0], $move);

            // checkr - $i <= $count_data_folders
            for ($i = 1; $i < $count_data_folders; $i++) {
                Db::getInstance()->Execute('INSERT INTO ` '._DB_PREFIX_.'ajaxzoom360set`
                    (id_360, sort_order) VALUES(\''.$id_360.'\', 0)');

                $id_360set = (int)Db::getInstance()->Insert_ID();

                $this->copyImages($id_product, $id_360, $id_360set, $dst.'/'.$data['folders'][$i], $move);

                //_PS_BASE_URL_.__PS_BASE_URI__
                $thumb = Ajaxzoom::getBaseDir().'modules/ajaxzoom/axZm/zoomLoad.php';
                $thumb .= '?qq=1&azImg360='.__PS_BASE_URI__.'img/p/360/'.$id_product.'/'.$id_360.'/'.$id_360set;
                $thumb .= '&width=100&height=100&thumbMode=contain';

                $sets[] = array(
                    'name' => $name,
                    'path' => $thumb,
                    'id_360set' => $id_360set
                );
            }
        }

        // delete temp directory which was created when zip extracted
        if (!is_dir($path) && strstr($dst, 'ajaxzoom')) {
            Tools::deleteDirectory($dst);
        }

        // delete the sourece file (zip/dir) if checkbox is checked
        if ($delete == 'true') {
            if (is_dir($path) && strstr($dst, 'ajaxzoom')) {
                Tools::deleteDirectory($dst);
            } elseif (strstr($path, 'ajaxzoom')) {
                unlink($path);
            }
        }

        return $sets;
    }

    public function addVideo()
    {
        $id_product = (int)Tools::getValue('id_product');
        $name = Tools::getValue('name');
        if (empty($name)) {
            $name = 'Unnamed '.uniqid(getmypid());
        }

        $type = Tools::getValue('type');
        $uid = Tools::getValue('uid');

        Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'ajaxzoomvideo`
            (id_product, name, uid, type, status, settings)
            VALUES(\''.$id_product.'\', \''.pSQL($name).'\',
            \''.$uid.'\', \''.$type.'\', 1, \'{"position":"last"}\')');

        $id_video = (int)Db::getInstance()->Insert_ID();

        die(Tools::jsonEncode(array(
            'status' => '1',
            'name' => $name,
            'uid' => $uid,
            'id_video' => $id_video,
            'type' => $type,
            'id_product' => $id_product,
            'videos' => AjaxZoom::getVideos($id_product),
            'confirmations' => array('New video entry was added.')
        )));
    }

    public function deleteVideo()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_video = (int)Tools::getValue('id_video');
        $del_video = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomvideo`
            WHERE id_product = '.(int)$id_product.' AND id_video = '.(int)$id_video);

        die(Tools::jsonEncode(array(
            'status' => $del_video ? 1 : 0,
            'id_video' => $id_video,
            'id_product' => $id_product,
            'confirmations' => array('Video with ID - '.$id_video.' - deleted.')
        )));
    }

    public function setVideoStatus()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_video = (int)Tools::getValue('id_video');
        $status = (int)Tools::getValue('status');
        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoomvideo` SET status = '.(int)$status.'
            WHERE id_product = '.(int)$id_product.' AND id_video = '.(int)$id_video);

        die(Tools::jsonEncode(array(
            'status' => $status,
            'id_video' => $id_video,
            'id_product' => $id_product,
            'confirmations' => array('Status of video with id - '.$id_video.' - has been changed.')
        )));
    }

    public function saveSettingsVideo()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_video = (int)Tools::getValue('id_video');
        $names = explode('|', Tools::getValue('names'));
        $values = explode('|', Tools::getValue('values'));
        $combinations = explode('|', Tools::getValue('combinations'));

        $count_names = count($names);
        $settings = array();

        for ($i = 0; $i < $count_names; $i++) {
            $key = $names[$i];
            $value = $values[$i];
            if ($key != 'name_placeholder' && !empty($key)) {
                $settings[$key] = $value;
            }
        }

        if (!empty($settings)) {
            $settings = Ajaxzoom::prepareProductSettings($settings, true);
        } else {
            $settings = '{}';
        }

        $name = Tools::getValue('name');
        $uid = Tools::getValue('uid');
        $type = Tools::getValue('type');
        $uid_int = Tools::getValue('uid_int');

        $data = array(
            'uid' => (array)Tools::jsonDecode($uid_int)
        );

        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoomvideo`
            SET
                settings = \''.$settings.'\',
                combinations = \''.implode(',', $combinations).'\',
                name = \''.$name.'\',
                uid = \''.$uid.'\',
                type = \''.$type.'\',
                data = \''.Tools::jsonEncode($data).'\'
            WHERE
                id_video = '.(int)$id_video.'
                AND id_product='.(int)$id_product);

        die(Tools::jsonEncode(array(
            'status' => 'ok',
            'id_product' => $id_product,
            'id_video' => $id_video,
            'videos' => AjaxZoom::getVideos($id_product),
            'confirmations' => array('The settings have been updated.')
        )));
    }

    public function updateAz()
    {
        $ret = AjaxZoom::downloadAxZm(true);
        die(Tools::jsonEncode($ret));
    }

    public function updateAzReq()
    {
        $az = new AjaxZoom;

        die($az->updateAxZmTab());
    }

    public function refreshPicturesList()
    {
        $id_product = (int)Tools::getValue('id_product');
        $az = new AjaxZoom;
        $ret = $az->picturesBackendList($id_product);

        die(Tools::jsonEncode($ret));
    }

    public function setHotspotStatus()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_media = (int)Tools::getValue('id_media');
        $status = (int)Tools::getValue('status');

        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'ajaxzoomimagehotspots`
            SET hotspots_active = '.(int)$status.'
            WHERE id_product = '.(int)$id_product.' AND id_media = '.(int)$id_media);

        die(Tools::jsonEncode(array(
            'status' => $status,
            'id_media' => $id_media,
            'id_product' => $id_product,
            'confirmations' => array('Status of image with id - '.$id_media.' - has been changed.')
        )));
    }

    public function getHotspotPictureJSON()
    {
        echo AjaxZoom::getHotspotPictureJSON(Tools::getValue('id_media'));
        exit;
    }

    public function saveHotspotPictureJSON()
    {
        $tmp = Ajaxzoom::setHotspotPictureJSON(
            Tools::getValue('id_product'),
            Tools::getValue('id_media'),
            Tools::getValue('json')
        );

        die(Tools::jsonEncode(array(
            'status' => $tmp
        )));
    }

    public function saveProductAzSettings()
    {
        $id_product = (int)Tools::getValue('id_product');

        $names = explode('|', Tools::getValue('names'));
        $values = explode('|', Tools::getValue('values'));
        $count_names = count($names);
        $settings = array();

        for ($i = 0; $i < $count_names; $i++) {
            $key = $names[$i];
            $value = $values[$i];
            if ($key != 'name_placeholder' && !empty($key)) {
                $settings[$key] = $value;
            }
        }

        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ajaxzoomproductsettings`
            WHERE id_product = '.(int)$id_product);

        if (!empty($settings)) {
            $settings = Ajaxzoom::prepareProductSettings($settings, true);
            Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'ajaxzoomproductsettings`
                SET psettings = \''.$settings.'\',
                id_product = '.(int)$id_product);
        }

        die(Tools::jsonEncode(array(
            'moduleSettings' => Ajaxzoom::getProductPluginOpt($id_product)
        )));
    }
}
