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
		    $('#loader').show();
			var l = $('#loader');
		    $('#tournament_stroke').load(
			    'tournament.php?action=get_stroke',
				{tid: {/literal}{$tournament->get_id()}{literal}},
				function() {
				    $('#tournament_stroke').show();
					$('#tournament_stroke').append(l);
					$('#loader').hide();
				}
			);
		}
		function get_valid_games() {
		    $('#valid_games').load(
			    'tournament.php?action=get_valid_games',
				{tid: {/literal}{$tournament->get_id()}{literal}},
				function() {
				    $('#valid_games').show();
				}
			);
		}
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
					    window.location.reload();
					}
				}
			})
		}
	</script>
{/literal}
<h1>{$tournament->get_name()}</h1>
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
		    <strong>State:</strong>
		</td>
		<td>
		    {$state.title}{if !$tournament->is_user_joined($user->get_player_id())}&nbsp;(<span id = "join"><a href = "javascript: join_tournament({$tournament->get_id()});" title = "Join the tournament">Sign up&nbsp;<img src = "images/sign_up.png" alt = "Sign up" title = "Sign up" /></a></span>){/if}
		</td>
	</tr>
</table>
<h3>Information</h3>
<pre>{$tournament->get_information()}</pre>
<h3>Rules</h3>
<pre>{$tournament->get_rules()}</pre>
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
	    {html_entity->loader text="Load..."}
	</div>
</div>
{if $user && $tournament->is_user_joined($user->get_player_id()) && $state.value < 2}
    <div class = "list">
        <div class = "list_header" onclick = "javascript: get_valid_games();">
	        <strong>Report game</strong>
	    </div>
	    <div class = "list_content" id = "valid_games">
	    </div>
    </div>
{/if}
<div style = "height: 10px;">
</div>
<a href = "tournament.php?action=list_tournaments">Back to the list</a>