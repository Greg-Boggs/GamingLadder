{literal}
    <script type = "text/javascript">
	    function join_tournament(tid) {
		    $.ajax({
			    url: 'tournament.php?action=join',
				data: {tid: tid},
				success: function(json) {
				    eval('var result = ' + json + ';');
					if (result.error) {
					    alert(result.error);
					}
					else {
					    $('#join_to_' + tid).html('Joined');
					}
				}
			})
		}
	</script>
{/literal}
<div class = "tournament_list">
    {if $user && $user->get_is_admin()}
	    <a href = "tournament.php?action=create_tournament" title = "Create a tournament">Create a tournament</a>
	{/if}
	<table width = "100%">
        {foreach from=$tournaments item="tournament"}
		    {assign var="state" value=$tournament->get_state()}
	        <tr {cycle name="lines" values='class="selected",'}>
			    <td>
				    <a href = "tournament.php?action=view_tournament&amp;tid={$tournament->get_id()}">{$tournament->get_name()}</a>
				</td>
				<td align = "center">
			        <div class = "join" id = "join_to_{$tournament->get_id()}">
					    {if !$state.value}
						    {if $tournament->is_user_joined($user->get_player_id())}Joined{else}<a href = "javascript: join_tournament({$tournament->get_id()});" title = "Join the tournament">Join</a>{/if}
						{else}
						    -
						{/if}
					</div>
				</td>
			</tr>
        {foreachelse}
            <tr>
			    <td>
	                No tournaments!
				</td>
			</tr>
        {/foreach}
	</table>
</div>
<div style = "clear: both;">
</div>
{if $tournaments}
    {html_entity->paginate total=$total url=$url items_per_page=$items_per_page}
{/if}
<hr />
<div style = "clear: both;">
</div>