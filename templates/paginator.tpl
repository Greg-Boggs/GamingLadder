<div class = "paginator">
    {if $current_page>0}
	    {assign var="prev" value=$current_page-$items_per_page}
	{else}
	    {assign var="prev" value=0}
	{/if}
    <div>
	    <a href = "{$url}{if $is_js_url}({$prev});{else}&amp;p_c_p={$prev}{/if}" title = "Previous page"><</a>
	</div>
    {foreach from=$pages key="i" item="page"}
        <div class = "page_number">
	        {if $current_page == $page}
			    <strong>
				    {$i}
				</strong>
			{else}
	            <a href = "{$url}{if $is_js_url}({$page});{else}&amp;p_c_p={$page}{/if}" title = "Page #{$i}">{$i}</a>
		    {/if}
	    </div>
    {/foreach}
	{if $current_page<$last_page}
	    {assign var="next" value=$current_page+$items_per_page}
	{else}
	    {assign var="next" value=$last_page}
	{/if}
    <div>
	    <a href = "{$url}{if $is_js_url}({$next});{else}&amp;p_c_p={$next}{/if}" title = "Next page">></a>
	</div>
</div>
<div style = "clear: both;"></div>