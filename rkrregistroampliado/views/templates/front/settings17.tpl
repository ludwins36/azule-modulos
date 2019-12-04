{*
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
*}

{extends file='page.tpl'}

{block name='page_title'}
  {l s='Additionnal informations' mod='rkrregistroampliado'}
{/block}

{block name='page_content'}

  <form action="{$link->getModuleLink('rkrregistroampliado', 'display')|escape:'htmlall':'UTF-8'}" id="customer-form" class="js-customer-form" method="post">
    <section>
      {block "form_fields"}

        {foreach from=$fields item=field name=rkr_responses}

          {if !empty($responses[$field.id])}
            {assign var="response" value=$responses[$field.id]}
          {else}
            {assign var="response" value=null}
          {/if}

          {include file='module:rkrregistroampliado/views/templates/hook/_customer-form.tpl'}
        {/foreach}
        <input type="hidden" name="submitIdentity" value="1">
      {/block}
    </section>

    <footer class="form-footer clearfix">
      <input type="hidden" name="submitCreate" value="1">
      {block "form_buttons"}
        <button class="btn btn-primary form-control-submit pull-xs-right" data-link-action="save-customer" type="submit">
          {l s='Save' d='Shop.Theme.Actions' mod='rkrregistroampliado'}
        </button>
      {/block}
    </footer>
  </form>
{/block}
