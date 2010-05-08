<div class = "tournament_list">
    {if $user && $user->get_is_admin()}
	    <a href = "tournament.php?action=create_tournament" title = "Create a tournament">Create a tournament</a>
	{/if}
    {foreach from=$tournaments item="tournament"}
        <div class = "wrapper {cycle name="lines" values="selected,"}">
	        <a href = "tournament.php?action=view&amp;tid={$tournament->get_id()}">{$tournament->get_name()}</a>
        </div>
    {foreachelse}
        <div>
	        No tournaments!
        </div>
    {/foreach}
</div>
<div style = "clear: both;">
{if $tournaments}
    {html_entity->paginate total=$total url=$url items_per_page=$items_per_page}
{/if}
<hr />
<div style = "clear: both;">
</div>