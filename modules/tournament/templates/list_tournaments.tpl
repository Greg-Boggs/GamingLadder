{literal}
    <script type = "text/javascript">
	    //<![CDATA[
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
					    $('#join_to_' + tid).html('<img src = "images/signed_up.png" alt = "Signed up" title = "Signed up" />');
					}
				}
			})
		}
		//]]>
{/literal}
{if $user && $user->get_is_admin()}
    {literal}
		function delete_tournament(tid) {
		    $.ajax({
			    url: 'tournament.php?action=delete_tournament',
				data: {tid: tid},
				success: function(json) {
				    eval('var result = ' + json + ';');
					if (result.error) {
					    alert(result.error);
					}
					else {
					    window.location.reload();
					}
				}
			})
		}
    {/literal}
{/if}
</script>
<div class = "tournament_list">
	<table width = "100%">
	    <tr>
	        <th>
		        &nbsp;
		    </th>
	        <th>
		        Tournament
		    </th>
		    <th>
		        Type
		    </th>
		    <th>
		        Number of participants
		    </th>
		    <th>
		        State
		    </th>
			{if $user && $user->get_is_admin()}
			    <th>
				    &nbsp;
				</th>
			    <th>
				    &nbsp;
				</th>
			{/if}
		</tr>
        {foreach from=$tournaments item="tournament" key="key"}
		    {assign var="state" value=$tournament->get_state()}
	        <tr {cycle name="lines" values='class="selected",'}>
			    <td>
				    {$key+1}
				</td>
			    <td>
				    <a href = "tournament.php?action=view_tournament&amp;tid={$tournament->get_id()}">{$tournament->get_name()}</a>
				</td>
				<td>
				    {$tournament->get_system_type()}
				</td>
				<td>
				    {$tournament->get_joined_participants()} of {$tournament->get_max_participants()}
				</td>
				<td align = "center" width = "5%">
			        <div class = "join" id = "join_to_{$tournament->get_id()}">
					    {if $user && !$state.value}
						    {if $tournament->is_user_joined($user->get_player_id())}<img src = "images/signed_up.png" alt = "Signed up" title = "Signed up" />{else}<a href = "javascript: join_tournament({$tournament->get_id()});" title = "Join the tournament"><img src = "images/sign_up.png" alt = "Sign up" title = "Sign up" /></a>{/if}
						{else}
						    {if $state.value < 2}
							    <img src = "images/playing.png" alt = "Tournament is playing" title = "Tournament is playing" />
							{else}
							    <img src = "images/played.png" alt = "Tournament is played" title = "Tournament is played" />
							{/if}
						{/if}
					</div>
				</td>
				{if $user && $user->get_is_admin()}
				    <td align = "right">
				        <a href = "tournament.php?action=create_tournament&amp;tid={$tournament->get_id()}" title = "Edit tournament"><img src = "images/edit.png" alt = "[delete]" /></a>
				    </td>
			        <td align = "right">
				        <a href = "javascript: delete_tournament({$tournament->get_id()});" title = "Remove tournament"><img src = "images/deleted.png" alt = "[delete]" /></a>
				    </td>
			    {/if}
			</tr>
        {foreachelse}
            <tr>
			    <td>
	                No tournaments!
				</td>
			</tr>
        {/foreach}
	</table>
    <div style = "width: 100%; text-align: right;">
	    <a href = "tournament.php">All tournaments</a>
	    &nbsp;|&nbsp;
	    <a href = "tournament.php?state={$states[0]}">New tournaments</a>
	    &nbsp;|&nbsp;
	    <a href = "tournament.php?state={$states[1]}">Playing tournaments</a>
	    &nbsp;|&nbsp;
	    <a href = "tournament.php?state={$states[2]}">Finished tournaments</a>
	</div>
	{if $user && $user->get_is_admin()}
	    <br />
	    <a href = "tournament.php?action=create_tournament" title = "Create a tournament"><img src = "images/add.png" alt = "Create a tournament" />&nbsp;Create a tournament</a>
	{/if}
</div>
<div style = "clear: both;">
</div>
{if $tournaments}
    {html_entity->paginate total=$total url=$url items_per_page=$items_per_page}
{/if}
<hr />
<div style = "clear: both;">
</div>