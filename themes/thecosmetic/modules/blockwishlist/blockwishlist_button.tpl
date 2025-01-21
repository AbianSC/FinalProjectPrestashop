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
{if isset($wishlists)}
	<div class="wishlist">
      {foreach name=wl from=$wishlists item=wishlist}
          {if $smarty.foreach.wl.first}
						<a class="wishlist_button_list" tabindex="0" data-toggle="popover" data-trigger="focus" title="{l s='Wishlist' mod='blockwishlist'}" data-placement="bottom">
							<i class="fa fa-heart-o" aria-hidden="true"></i>
						</a>
						<div hidden class="popover-content">
						<table class="table" border="1">
						<tbody>
          {/if}
				<tr title="{if isset($wishlist.name)}{$wishlist.name|escape:'html':'UTF-8'}{/if}" value="{if isset($wishlist.id_wishlist)}{$wishlist.id_wishlist|escape:'html':'UTF-8'}{/if}" onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', false, 1, '{if isset($wishlist.id_wishlist)}{$wishlist.id_wishlist|escape:'html':'UTF-8'}{/if}');">
            {if isset($wishlist.name)}
							<td>
                  {l s='Add to %s' sprintf=[$wishlist.name] mod='blockwishlist'}
							</td>
            {/if}
				</tr>
          {if $smarty.foreach.wl.last}
						</tbody>
						</table>
						</div>
          {/if}
          {foreachelse}
				<a href="#" id="wishlist_button_nopop" onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', $('#idCombination').val(), document.getElementById('quantity_wanted').value); return false;" rel="nofollow"  title="{l s='Add to my wishlist' mod='blockwishlist'}">
					<i class="fa fa-heart-o" aria-hidden="true"></i>
				</a>
      {/foreach}
	</div>
{else}
	<div class="wishlist">
		<a class="addToWishlist wishlistProd_{$product.id_product|intval}" href="#" data-rel="{$product.id_product|intval}" onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', false, 1); return false;">

		</a>
	</div>
{/if}