{if !$product->id}
    <div class="warn" style="display:block">
        {l s='You need to save this product first before adding dates.' mod='psmoduletemplate'}
    </div>
{else}
        <h4>{l s='Add properties to product' mod='psmoduletemplate'}</h4>
        <div>
			<p>Properties here</p>
        </div>
{/if}
