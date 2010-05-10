{literal}
    <script type = "text/javascript">
	    function get_players() {
		    $('#joined_players').load(
			    'tournament.php?action=get_joined_players',
				{tid: {/literal}{$tournament->get_id()}{literal}},
				function() {
				    $('#joined_players').show();
				}
			);
		}
		function get_stroke() {
		    $('#tournament_stroke').load(
			    'tournament.php?action=get_stroke',
				{tid: {/literal}{$tournament->get_id()}{literal}},
				function() {
				    $('#tournament_stroke').show();
				}
			);
		}
	</script>
{/literal}
<h1>{$tournament->get_name()}</h1>
<h3>Information</h3>
{$tournament->get_information()}
<h3>Rules</h3>
{$tournament->get_rules()}
<table>
    <tr>
	    <td align = "right">
		    <strong>Type:</strong>
		</td>
		<td>
		    {$tournament->get_system_type()}
		</td>
	</tr>
	<tr>
	    <td align = "right">
		    <strong>{if !$state.value}Sign up will end{else}{if $state.value==2}Playing ended{else}Playing will end{/if}{/if}:</strong>
		</td>
		<td>
		    {if !$state.value}{$tournament->get_date("sign_up_ends", ".")}{else}{$tournament->get_date("play_ends", ".")}{/if}
		</td>
	</tr>
	<tr>
	    <td align = "right">
		    <strong>State:</strong>
		</td>
		<td>
		    {$state.title}
		</td>
	</tr>
</table>
<div class = "list">
    <div class = "list_header" onclick = "javascript: get_players();">
	    <strong>Joined users</strong>&nbsp;({$tournament->get_joined_participants()} of {$tournament->get_max_participants()} required)
	</div>
	<div class = "list_content" id = "joined_players">
	</div>
</div>
<div class = "list">
    <div class = "list_header" onclick = "javascript: get_stroke();">
	    <strong>Tournament Stroke</strong>
	</div>
	<div class = "list_content" id = "tournament_stroke">
	</div>
</div>
<a href = "javascript: history.back();">Back</a>