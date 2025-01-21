{block name="content"}
  {*  <div class="panel">
        <div class="panel-heading">
            <h3>{l s='Optimize Profits'}</h3>
        </div>
    </div>*}
    <div class="panel">
        <h3>{$page_header_toolbar_title|escape:'html':'UTF-8'}</h3>
        <p>{$description|default:'Generate and send AI data to the external API.'|escape:'html':'UTF-8'}</p>
        <a href="{$process_url}" class="btn btn-primary">
            <i class="icon-cogs"></i> Process and Send Data
        </a>
    </div>

{/block}