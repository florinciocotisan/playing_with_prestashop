{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
	{if isset($params.type) && $params.type == 'image_banner' && $tr.$key}
		<img src="{$params.path}{$tr.type}/{$tr.$key}" width="100px">
	{else}
		{$smarty.block.parent}
	{/if}
{/block}