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

<section id="rkr-front-wrap" style="display: none;">

    {foreach from=$fields item=field name=rkr_field}
      {include file='module:rkrregistroampliado/views/templates/hook/_customer-form.tpl'}
    {/foreach}

</section>

<script type="text/javascript">
  // Rekire - Luis Jordán :: Lo desactivo por lo que pueda pasar.
  // var C4YCreateForm = true;
</script>
