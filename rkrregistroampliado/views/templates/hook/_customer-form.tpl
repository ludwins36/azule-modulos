<div class="form-group row">
  {* Rekire - Luis Jordán :: Asignamos el valor de los lavels a las etiquetas de prestashop
  para que al actualizar los labels de address aplique aquí también *}
  {assign var="nameAddress" value=$field.name|escape:'htmlall':'UTF-8' }

  {if $field.type !== "checkbox"}
    <label for="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="col-md-3 form-control-label">{l s=$nameAddress d='Shop.Forms.Labels'}</label>
  {else}
    <label class="col-md-3 form-control-label"></label>
  {/if}

  <div class="col-md-6 {if in_array($field.type, array("checkbox", "radio", "yesno"))}form-control-valign{/if}">
    {* Simple text *}
    {if $field.type === "text"}
      <input type="text" class="{if $field.required}is_required{/if} validate form-control"
             data-validate="isGenericName" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
             value="{if !empty($smarty.post["rkr_{$field.id}"]) && $smarty.post["rkr_{$field.id}"]}{$smarty.post["rkr_{$field.id}"]|escape:'htmlall':'UTF-8'}{elseif !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />

      {* Textarea *}
    {else if $field.type === "textarea"}
      <textarea name="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                class="{if $field.required}is_required{/if} validate form-control"
                id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                data-validate="isGenericName">{if !empty($smarty.post["rkr_{$field.id}"]) && $smarty.post["rkr_{$field.id}"]}{$smarty.post["rkr_{$field.id}"]|escape:'htmlall':'UTF-8'}{elseif !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}</textarea>

      {* Select simple *}
    {else if $field.type === "select"}
      {assign var=listOption value=";"|explode:$field.fieldsSelector}
      <select name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="{if $field.required}is_required{/if} validate form-control">
        {foreach from=$listOption item=option}
          <option value="{$option|escape:'htmlall':'UTF-8'}"
                  {if !empty($smarty.post["rkr_{$field.id}"]) && $smarty.post["rkr_{$field.id}"] === $option}selected="selected"{*
                *}{elseif !empty($response.reponse) && $response.reponse === $option}selected="selected"{*
                *}{/if}>
            {$option|escape:'htmlall':'UTF-8'}
          </option>
        {/foreach}
      </select>

      {* Select multi *}
    {else if $field.type === "dropdown_multi"}
      {* Liste des options disponible *}
      {assign var=listOption value=";"|explode:$field.fieldsSelector}
      {* Liste des options selectionnées *}
      {if !empty($response.reponse)}
        {assign var=listResponses value=$response.reponse|unserialize}
      {else}
        {assign var=listResponses value=array}
      {/if}

      <select name="rkr_{$field.id|escape:'htmlall':'UTF-8'}[]" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
              class="{if $field.required}is_required{/if} rkr_dropdown_multi form-control"
              data-header-list="{l s='List' mod='rkrregistroampliado'}"
              data-header-selected="{l s='Selected' mod='rkrregistroampliado'}"
              multiple>
        {foreach from=$listOption item=option}
          <option value="{$option|escape:'htmlall':'UTF-8'}"
                  {if !empty($smarty.post["rkr_{$field.id}"]) && in_array($option, $smarty.post["rkr_{$field.id}"])}selected{*
                *}{elseif !empty($response.reponse) && !empty($listResponses) && in_array($option, $listResponses)}selected{/if}>{$option|escape:'htmlall':'UTF-8'}</option>
        {/foreach}
      </select>

      {* Upload *}
    {else if $field.type === "upload"}
      <div>
        {l s='Accepted filetype' mod='rkrregistroampliado'} :
        {foreach from=$field.fieldsSelector|@unserialize item="filetype" name=rkr_field_upload}
        {if !empty($filetypeName[$filetype])}{$filetypeName[$filetype]|escape:'htmlall':'UTF-8'}{/if}
        {if $smarty.foreach.rkr_field_upload.last == false}, {/if}
      {/foreach}
      <input type="file" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}">
    </div>
    <script type="text/javascript">
      $("#rkr_{$field.id|escape:'htmlall':'UTF-8'}").closest("form").attr("enctype", "multipart/form-data");
    </script>

    {* Radio *}
    {else if $field.type === "radio"}
      {assign var=listOption value=";"|explode:$field.fieldsSelector}
      <div>
        {foreach from=$listOption item=option}
          <div class="radio-inline">
            <input type="radio" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="{$option|escape:'htmlall':'UTF-8'}"
                   {if !empty($smarty.post["rkr_{$field.id}"]) && $smarty.post["rkr_{$field.id}"] === $option}checked{*
                 *}{elseif isset($response.reponse) && $response.reponse === $option}checked{/if} />
            {$option|escape:'htmlall':'UTF-8'}
          </div>
        {/foreach}
      </div>

      {* YesNo *}
    {else if $field.type === "yesno"}
      <label class="radio-inline">
        <span class="custom-radio">
          <input type="radio" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="1"
            {if (!empty($smarty.post["rkr_{$field.id}"]) && intval($smarty.post["rkr_{$field.id}"]) == 1) || (!empty($response.reponse) && intval($response.reponse) == 1)}checked{/if} />
          <span></span>
        </span>
        {l s='Yes' mod='rkrregistroampliado'}
      </label>
      <label class="radio-inline">
        <span class="custom-radio">
          <input type="radio" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="0"
            {if (!empty($smarty.post["rkr_{$field.id}"]) && intval($smarty.post["rkr_{$field.id}"]) == 0) || (isset($response.reponse) && intval($response.reponse) == 0)}checked{/if} />
          <span></span>
        </span>
        {l s='No' mod='rkrregistroampliado'}
      </label>

      {* Groupes *}
    {else if $field.type === "group"}
      <ul class="checkbox">
        {assign var="groupes" value=$field.fieldsSelector|@unserialize}
        {if !empty($response.reponse)}
          {assign var="group_response" value=$response.reponse|@unserialize}
        {/if}
        {foreach from=$groupesList item=listGroup name=rkr_field_group}
          {if in_array($listGroup.id_group, $groupes)}
            <li>
              <span class="custom-checkbox">
                <input type="checkbox" class="optin" data-validate="isGenericName"
                       name="rkr_{$field.id|escape:'htmlall':'UTF-8'}_{$listGroup.id_group|escape:'htmlall':'UTF-8'}"
                       id="{$smarty.foreach.rkr_field_group.index|escape:'htmlall':'UTF-8'}_{$field.id|escape:'htmlall':'UTF-8'}"
                       {if !empty($smarty.post["rkr_{$field.id}_{$listGroup.id_group}"]) || (!empty($group_response) && in_array($listGroup.id_group, $group_response))}checked="checked"{/if} />
                <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                <label>{$listGroup.name|escape:'htmlall':'UTF-8'}</label>
              </span>
            </li>
          {/if}
        {/foreach}
      </ul>

      {* Date *}
    {else if $field.type === "date"}
      <div>
        <input type="text" class="{if $field.required}is_required{/if} validate form-control"
               data-validate="isDate" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
               value="{if !empty($smarty.post["rkr_{$field.id}"]) && $smarty.post["rkr_{$field.id}"]}{$smarty.post["rkr_{$field.id}"]|escape:'htmlall':'UTF-8'}{*
                    *}{elseif !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
      </div>
      <script type="text/javascript">
        $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
          lang: 'fr',
          timepicker: false,
          format: 'd-m-Y'
        });
      </script>

      {* Time *}
    {else if $field.type === "time"}
      <div>
        <input type="text" class="{if $field.required}is_required{/if} validate form-control"
               data-validate="isDate" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
               value="{if !empty($smarty.post["rkr_{$field.id}"]) && $smarty.post["rkr_{$field.id}"]}{$smarty.post["rkr_{$field.id}"]|escape:'htmlall':'UTF-8'}{*
                    *}{elseif !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
      </div>
      <script type="text/javascript">
        $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
          lang: 'fr',
          datepicker: false,
          format: 'H:i',
          step: 30
        });
      </script>

      {* DateTime *}
    {else if $field.type === "datetime"}
      <div>
        <input type="text" class="{if $field.required}is_required{/if} validate form-control"
               data-validate="isDate" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
               value="{if !empty($smarty.post["rkr_{$field.id}"]) && $smarty.post["rkr_{$field.id}"]}{$smarty.post["rkr_{$field.id}"]|escape:'htmlall':'UTF-8'}{*
                    *}{elseif !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
      </div>
      <script type="text/javascript">
        $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
          lang: 'fr',
          format: 'd-m-Y H:i',
          step: 30
        });
      </script>

      {* Checkbox *}
    {else if $field.type === "checkbox"}
      <span class="custom-checkbox">
        <input type="checkbox" class="optin" data-validate="isGenericName"
               name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
               {if !empty($smarty.post["rkr_{$field.id}"]) || !empty($response.reponse)}checked="checked"{/if}/>
        <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
        <label>{$field.name|escape:'htmlall':'UTF-8'} {if $field.required}<sup>*</sup>{/if}</label>
      </span>
    {/if}
    {if $field.desc}
      {if in_array($field.type, array("checkbox", "radio", "yesno"))}<br />{/if}
      <span class="form-control-comment">{$field.desc|escape:'htmlall':'UTF-8'}</span>
    {/if}
  </div>

  <div class="col-md-3 form-control-comment">
    {* Simple text *}
    {if $field.type === "text"}
      {if !$field.required} {l s='Opcional' d='Shop.Forms.Labels'} {/if}
    {/if}
  </div>
</div>
