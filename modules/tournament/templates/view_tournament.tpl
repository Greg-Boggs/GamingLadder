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
	</script>
{/literal}
<h1>{$tournament->get_name()}</h1>
<h3>Information</h3>
{$tournament->get_information()}
<table>
    <tr>
	    <td align = "right">
		    <strong>Type:</strong>
		</td>
		<td>
		    {if $tournament->get_type()}Knock out{else}League{/if}
		</td>
	</tr>
	<tr>
	    <td align = "right">
		    <strong>State:</strong>
		</td>
		<td>
		    {$state}
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
    <div class = "list_header">
	    <strong>Tournament Stroke</strong>
	</div>
	<div class = "list_content">
	</div>
</div>
<a href = "javascript: history.back();">Back</a>