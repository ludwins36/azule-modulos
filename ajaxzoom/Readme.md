# AJAX-ZOOM Prestashop module

## About
AJAX-ZOOM is a multipurpose library for displaying (high resolution) images and 360°/3D spins with progressive zoom usind image tiles
AJAX-ZOOM mouseover zoom and progressive zoom are fully responsive

## Requirements / Dependencies
- PrestaShop Ver. 1.5.1+ or later (1.6 and 1.7 is fully supported)
- PHP 5.2+
- Free Ioncube loaders or Sourceguardian loaders installed on the server
- AJAX-ZOOM main script files downloaded separately
- If you want to upload high resolution images, then in your php.ini you should change the values for: post_max_size and upload_max_filesize

## Installing
1. Make sure server and PrestaShop requirements are met!
2. Unzip the folder 'ajaxzoom' from this archive to the '/modules/' directory of your PrestaShop installation.
3. Download the latest version of AJAX-ZOOM from http://www.ajax-zoom.com/index.php?cid=download and unzip **only** 'axZm' folder into '/modules/ajaxzoom/' directory.
4. Set chmod for '/modules/ajaxzoom/pic' directory to be writeable by PHP.
5. Activate the module through the 'Modules' menu in your PrestaShop back office panel.
6. Installation is done. Now you can configure the module but it should work out of the box. 
7. In case it does not work as expected please read the instructions in the modules config page.
8. Should you be not able to adjust AJAX-ZOOM to work perfectly in a theme, please contact AJAX-ZOOM support. 

## Handling regular images
For the regular images there is no need to do anything. You can upload them as always. 
However depending on PrestaShop version the original uploaded images might be already compressed by PrestaShop image class.
After activating AJAX-ZOOM this compression is disabled and if you are not satisfied with the quality you might want to reupload your high resolution images.

## Handling 360°/3D
To add 360° images go to 'CATALOG' -> 'Products' panel and select a product. There should be a new AJAX-ZOOM tab. In this tab you will be able to: 
- Define one or more 360° spins for the product.
- Upload 360° images right from the backend **or** 
- Select a zip file which contains images for a 360° set to import. These zip files must be uploaded in '/modules/ajaxzoom/zip/' directory and can be removed later.
- After importing or uploading the images manually you will be able to make some adjustments for this particular 360°, e.g. change rotation speed and many other options available for AJAX-ZOOM.
- Preview 360° in Fancybox before publishing.
- Activate / deactivate 360° view. 
- Select combinations, e.g. color and size for which a 360° view should be shown. You can add more than one 360° view for a product. 

## Licensing
http://www.ajax-zoom.com/index.php?cid=download

