{*
*  Module: jQuery AJAX-ZOOM for Prestashop, ajaxzoom.tpl
*
*  Copyright: Copyright (c) 2010-2019 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.20.0
*  Date: 2019-06-18
*  Review: 2019-06-18
*  URL: http://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*}

{if $ajaxzoom_videojs == true}
<!-- videojs enabled over AJAX-ZOOM module -->
{foreach key=k from=$ajaxzoom_videojsfiles item=f}
{if $f && $k|stristr:"js"}
<script type="text/javascript" src="{$f|escape:'html':'UTF-8'}"></script>
{elseif $f}
<link rel="stylesheet" href="{$f|escape:'html':'UTF-8'}" type="text/css" media="all">
{/if}
{/foreach}
{/if}

<!-- AJAX-ZOOM mouseover block  -->
<div class="axZm_mouseOverWithGalleryContainer" id="axZm_mouseOverWithGalleryContainer" style="display: none;">
	<!-- Parent container for offset to the left or right -->
	<div class="axZm_mouseOverZoomContainerWrap">
		<!-- Alternative container for title of the images -->
		<div class="axZm_mouseOverTitleParentAbove"></div>
		<!-- Container for mouse over image -->
		<div id="{$ajaxzoom_divID|escape:'htmlall':'UTF-8'}" class="axZm_mouseOverZoomContainer">
			<!-- Optional CSS aspect ratio and message to preserve layout before JS is triggered -->
			<div class="axZm_mouseOverAspectRatio">
				<div>
					<span>
					{if $ajaxzoom_displayInTab === true}
						{l s='Loading' mod='ajaxzoom'}
					{else}
						{l s='Image loading' mod='ajaxzoom'}
					{/if}
					</span>
				</div>
			</div>
		</div>
	</div>
	<!-- gallery with thumbs (will be filled with thumbs by javascript) -->
	<div id="{$ajaxzoom_galleryDivID|escape:'htmlall':'UTF-8'}" class="axZm_mouseOverGallery"></div>
</div>

{if $ajaxzoom_ps_version_17 == true && $ajaxzoom_showAllImgBtn == true}
<div style="visibility: hidden; display: none;" id="az_wrapResetImages">
	<i class="material-icons">photo_library</i><span></span>
</div>
{/if}

<script type="text/javascript">
/*!
*  Module: jQuery AJAX-ZOOM for PrestaShop
*  Version: 1.20.0
*  Date: 2019-06-18
*  Review: 2019-06-18
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @url   https://www.ajax-zoom.com/index.php?cid=modules&module=prestashop
*  @license   https://www.ajax-zoom.com/index.php?cid=download
*/

{literal}
;(function(g,b){function c(){if(!e){e=!0;for(var a=0;a<d.length;a++)d[a].fn.call(window,d[a].ctx);d=[]}}function h(){
"complete"===document.readyState&&c()}b=b||window;var d=[],e=!1,f=!1;b[g||"docReady"]=function(a,b){
e?setTimeout(function(){a(b)},1):(d.push({fn:a,ctx:b}),"complete"===document.readyState?setTimeout(c,1):f||
(document.addEventListener?(document.addEventListener("DOMContentLoaded",c,!1),window.addEventListener("load",c,!1))
:(document.attachEvent("onreadystatechange",h),window.attachEvent("onload",c)),f=!0))}})("az_docReady",window);

Function.prototype.axzm_psh_clone||(Function.prototype.axzm_psh_clone=function(){
var c=this,b=function(){return c.apply(this,arguments)},a;for(a in this)this.hasOwnProperty(a)&&(b[a]=this[a]);return b});
{/literal}

{if $ajaxzoom_content_only == 0}
try {
	if (top.location != self.location) { 
		top.location = self.location.href;
	}
} catch(err) { 
	window.az_docReady(function() { 
		if (window.jQuery) {
			var ttt = jQuery('<div style="position: relative; font-size: 150%; cursor: pointer; padding: 7px; background-color: yellow; margin-bottom: 15px; line-height: 1.2em; border: 4px #ff0076 solid;">The shop is loaded within an iframe. For full experience of the AJAX-ZOOM module, please click at this box to view the same product detail page not iframed.</div>');
			ttt.bind('click', function() {
				top.location = self.location.href;
			});

			ttt.insertAfter('h1:eq(0)');
		}
	});
}
{/if}

{if $ajaxzoom_ps_version_15 == true || $ajaxzoom_ps_version_16 == true}
window.axZm_tmp_combination = false;
window.refreshProductImagesClone = window.refreshProductImages.axzm_psh_clone();
window.refreshProductImages = function(a, b, c, d) { 
	{if $ajaxzoom_displayInTab || $ajaxzoom_displayInSelector}
	window.refreshProductImagesClone(a);
	{/if}
	window.axZm_tmp_combination = a;
};
{/if}

window.HELP_IMPROVE_VIDEOJS = false;

window.az_docReady( function() { 
var az_data_init = function() { 
	jQuery.axZm_psh.tabName = "{l s='360 / Video' mod='ajaxzoom'}";
	jQuery.axZm_psh.prevPid = null;
	jQuery.axZm_psh.ps15 = {if $ajaxzoom_ps_version_15}true{else}false{/if};
	jQuery.axZm_psh.ps16 = {if $ajaxzoom_ps_version_16}true{else}false{/if};
	jQuery.axZm_psh.ps17 = {if $ajaxzoom_ps_version_17}true{else}false{/if};
	jQuery.axZm_psh.ps_version = '{$ajaxzoom_ps_version|escape:'htmlall':'UTF-8'}';

	jQuery.axZm_psh.IMAGES_JSON = jQuery.parseJSON('{$ajaxzoom_imagesJSON nofilter}'); {* cannot escape as it is javascript code *}
	jQuery.axZm_psh.CURRENT_IMAGES_JSON = jQuery.extend(true, {}, jQuery.axZm_psh.IMAGES_JSON);

	jQuery.axZm_psh.IMAGES_360_JSON = jQuery.parseJSON('{$ajaxzoom_images360JSON nofilter}'); {* cannot escape as it is javascript code *}
	jQuery.axZm_psh.CURRENT_IMAGES_360_JSON = jQuery.extend(true, {}, jQuery.axZm_psh.IMAGES_360_JSON);

	jQuery.axZm_psh.VIDEOS_JSON = jQuery.parseJSON('{$ajaxzoom_videosJSON nofilter}'); {* cannot escape as it is javascript code *}
	jQuery.axZm_psh.CURRENT_VIDEOS_JSON = jQuery.extend(true, {}, jQuery.axZm_psh.VIDEOS_JSON);

	jQuery.axZm_psh.axZmPath = '{$ajaxzoom_modules_dir|escape:'htmlall':'UTF-8'}ajaxzoom/axZm/';
	jQuery.axZm_psh.shopLang = '{$ajaxzoom_lang_iso|escape:'htmlall':'UTF-8'}';
	jQuery.axZm_psh.initParam = {$ajaxzoom_initParam nofilter}; {* cannot escape as it is javascript code *}
	jQuery.axZm_psh.divID = jQuery.axZm_psh.initParam.divID;
	jQuery.axZm_psh.galleryDivID = jQuery.axZm_psh.initParam.galleryDivID;

	jQuery.axZm_psh.headerZindex = parseInt('{$ajaxzoom_headerZindex|escape:'htmlall':'UTF-8'}');
	jQuery.axZm_psh.showAllImgBtn = {if $ajaxzoom_showAllImgBtn}true{else}false{/if};
	jQuery.axZm_psh.showAllImgTxt = {$ajaxzoom_showAllImgTxt nofilter};
	jQuery.axZm_psh.contentOnly = parseInt('{$ajaxzoom_content_only|escape:'htmlall':'UTF-8'}');

	if (jQuery.axZm_psh.contentOnly == 1) {
		jQuery.axZm_psh.initParam.ajaxZoomOpenMode = 'fullscreen';
		jQuery.axZm_psh.initParam.fullScreenApi = false;
	}

{if $ajaxzoom_ps_version_17 == true}
	jQuery.axZm_psh.combinationImages = {$ajaxzoom_combination_images nofilter};
	jQuery.axZm_psh.ajaxzoom_attribute_id = {$ajaxzoom_attribute_id};
{/if}

	jQuery.axZm_psh.displayInTab = {if $ajaxzoom_displayInTab === true}true{else}false{/if};
	jQuery.axZm_psh.inTabPosition = '{$ajaxzoom_inTabPosition|escape:'htmlall':'UTF-8'}';
	jQuery.axZm_psh.displayInSelector = '{$ajaxzoom_displayInSelector|escape:'htmlall':'UTF-8'}';
	jQuery.axZm_psh.displayInSelectorAppend = {if $ajaxzoom_displayInSelectorAppend}true{else}false{/if};

	jQuery.axZm_psh.displayInAzOpt = null;
{if $ajaxzoom_displayInAzOpt}
	jQuery.axZm_psh.displayInAzOpt = {$ajaxzoom_displayInAzOpt nofilter}; {* cannot escape as it is javascript code *}
{/if}

} ;

if (!window.jQuery || !jQuery.axZm_psh || typeof jQuery.axZm_psh.go !== 'function') { 
	var nnn = 0;
	var az_wait_to_go = setInterval(function() { 
		if (window.jQuery && jQuery.axZm_psh && typeof jQuery.axZm_psh.go == 'function') { 
			clearInterval(az_wait_to_go);
			az_data_init();
			jQuery.axZm_psh.go();
		} else {
			nnn++;
			if (nnn > 150) { 
				clearInterval(az_wait_to_go);
				var mmm=0,msg="AJAX-ZOOM error message: there may be a problem with AJAX-ZOOM JavaScript files. ";window.jQuery||(mmm++,msg+=mmm+". jQuery core is not available on this page. ");jQuery.axZm_psh&&jQuery.axZm_psh.go||(mmm++,msg+=mmm+". One of the AJAX-ZOOM files is not present on this page. ",msg+="Please update / clear JavaScript cache. ");msg+="If nothing helps, please contact AJAX-ZOOM support.";window.console&&window.console.error?window.console.error(msg):alert(msg);
			}
		}
	}, 1000/30);
} else {
	az_data_init();
	jQuery.axZm_psh.go();
}

} );
</script>
