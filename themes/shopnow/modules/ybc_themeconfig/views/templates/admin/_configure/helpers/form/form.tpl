{*
* 2007-2022 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{extends file="helpers/form/form.tpl"}
{block name="input_row"}
    {if $input.name=='BEGIN_FORM' || $input.name=='END_FORM'}    
        {if $input.name=='BEGIN_FORM'}<div class="ybc-form"><div class="ybc-form-left">{if isset($input.html)}{$input.html|escape:'html':'UTF-8'}{/if}</div><div class="ybc-form-right">{/if}
        {if $input.name=='END_FORM'}
            <input type="hidden" name="submited_tab" value="{if isset($smarty.get.submited_tab)}{$smarty.get.submited_tab|escape:'html':'UTF-8'}{else}ybc_tab_general{/if}" /></div></div>
            <script type="text/javascript">
                {literal}
                $(document).ready(function(){                   
                    if($('.ybc_fancy').length > 0)
                    {
                        $('.ybc_fancy').fancybox();
                    }
                });
                {/literal}
            </script>
        {/if}
    {else}
        <div class="ybc-form-group{if isset($input.group) && $input.group} {$input.group|escape:'html':'UTF-8'}{/if} {if isset($input.separator) && $input.separator} ybc-tc-separator{/if} {if isset($input.is_custom_color) && $input.is_custom_color} ybc_custom_color{/if}">
            {$smarty.block.parent}
            {if isset($input.info) && $input.info}
                <div class="ybc_tc_info alert alert-warning">{$input.info|escape:'html':'UTF-8'}</div>
            {/if}
        </div>
    {/if}
{/block}
{block name="field"}   
    {if $input.type != 'backgroundimg' && $input.type != 'file' && $input.name!='BEGIN_FORM' && $input.name!='END_FORM' && $input.name!='IMPORT_DATA'}
        {$smarty.block.parent}
    {elseif $input.name=='IMPORT_DATA'}
        <div class="col-lg-9">
            {if isset($input.sections) && $input.sections}
                {foreach from=$input.sections item='section'}
                    <input type="checkbox" name="IMPORT_DATA[]" value="{$section.id|escape:'html':'UTF-8'}" id="IMPORT_DATA_{$section.id|escape:'html':'UTF-8'}" /> <label style="font-weight: normal;" for="IMPORT_DATA_{$section.id|escape:'html':'UTF-8'}">{$section.name|escape:'html':'UTF-8'}</label><br />
                {/foreach}
                <input type="submit" name="SUBMIT_IMPORT" value="{l s='Import sample data' mod='ybc_themeconfig'}" class="button btn btn-default" id="ybc_submit_import" />
                {if isset($devMode) && $devMode}
                    <input type="submit" name="SUBMIT_EXPORT" value="{l s='Export sample data to sql files' mod='ybc_themeconfig'}" class="button btn btn-default" id="ybc_submit_export" />
                {/if}
                <div style="display: none;" id="ybc_import_warning_msg">{l s='You are going to overried your old data of the selected section(s). Do you confirm?'  mod='ybc_themeconfig'}</div>
                <div style="display: none;" id="ybc_export_warning_msg">{l s='You are going to overried all sample data. Do you confirm?'  mod='ybc_themeconfig'}</div>
                <div class="ybc-tc-import-loading"><img src="{$module_path|escape:'html':'UTF-8'}img/loading-admin.gif" /></div>
            {/if}
        </div>
    {elseif $input.type == 'backgroundimg'}
        <div class="col-lg-9">
            <input type="hidden" id="YBC_TC_BG_IMG" name="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value.YBC_TC_BG_IMG|escape:'html':'UTF-8'}" />
            <ul style="float: left; padding: 0; margin-top: 5px;">
                {if $input.bgs}
                    {foreach from=$input.bgs item='bg'}
                        <li style="list-style: none; cursor: pointer; display: inline-block; margin: 0 2px;"><span class="ybc-img-span" rel={$bg|escape:'html':'UTF-8'} style="width: 50px; height: 50px; display: inline-block; background: url('{$base_url|escape:'html':'UTF-8'}modules/ybc_themeconfig/bgs/{$bg|escape:'html':'UTF-8'}.png'); border: 1px solid {if $fields_value.YBC_TC_BG_IMG==$bg}#0176B5{else}#eee{/if};"></span></li>
                    {/foreach}
                {/if}
            </ul>
        </div>        
    {elseif $input.type == 'file' &&  isset($input.display_img) && $input.display_img}
        {$smarty.block.parent}
        <label class="control-label col-lg-3" style="font-style: italic;">{l s='Uploaded image: ' mod='ybc_themeconfig'}</label>
        <div class="col-lg-9">
    		<a  class="ybc_fancy" href="{$input.display_img|escape:'html':'UTF-8'}"><img title="{l s='Click to see full size image' mod='ybc_themeconfig'}" style="display: inline-block; max-width: 200px;" src="{$input.display_img|escape:'html':'UTF-8'}" /></a>
            {if isset($input.img_del_link) && $input.img_del_link && !(isset($input.required) && $input.required)}
                <a onclick="return confirm('{l s='Do you want to delete this image?' mod='ybc_themeconfig'}');" style="display: inline-block; text-decoration: none!important;" href="{$input.img_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
            {/if}
        </div>
    {elseif $input.type == 'file'}
        {$smarty.block.parent}
    {/if}
    
    <script type="text/javascript">
        $(document).ready(function(){
            $('.ybc-img-span').click(function(){
                $('#YBC_TC_BG_IMG').val($(this).attr('rel'));
                $('.ybc-img-span').css('border-color','#eee');
                $(this).css('border-color','#0176B5');
            });
        });
    </script>
{/block}

{block name="footer"}
    {capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
	{if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
		<div class="panel-footer">
            {if isset($reset_url) && $reset_url}
                <a title="{l s='Reset to default parameters' mod='ybc_themeconfig'}" onclick="return confirm('{l s='You are going to reset all parameters to default. Do you confirm?' mod='ybc_themeconfig'}');" class="btn btn-default" href="{$reset_url|escape:'html':'UTF-8'}"><i class="process-icon-refresh"></i>{l s='Reset' mod='ybc_themeconfig'}</a>
            {/if}
            <div class="ybc-tc-loading"><img src="{$module_path|escape:'html':'UTF-8'}img/loading-admin.gif" /></div>
            {if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
			<button type="submit" value="1"	id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']|escape:'html':'UTF-8'}{else}{$table|escape:'html':'UTF-8'}_form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']|escape:'html':'UTF-8'}{else}{$submit_action|escape:'html':'UTF-8'}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}" class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']|escape:'html':'UTF-8'}{else}btn btn-default pull-right{/if}">
				<i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']|escape:'html':'UTF-8'}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']|escape:'html':'UTF-8'}
			</button>
			{/if}            
		</div>
	{/if}
{/block}