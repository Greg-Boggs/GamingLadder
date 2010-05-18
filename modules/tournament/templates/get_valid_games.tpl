{literal}
    <script type = "text/javascript">
	    function report_game() {
		    $('#v_games').load(
			    'tournament.php?action=report_game',
				{tid: {/literal}{$tid}{literal}, game: $('#gid').val()},
				function(val) {
				    get_stroke();
					$('#valid_games').html(val);
				}
			);
		}
	</script>
{/literal}
<div class = "list thread" style = "margin-top: 0;">
    <form action="" method="post" id = "v_games" onsubmit = "return false">
        {foreach from=$games item="game"}
            <div class = "wrapper {cycle name="lines" values="selected,"}">
                <div class = "message_title" onclick = "javascript: $('#gid').val('{$game->get_reported_on()}');">
			        <input name = "game" type = "radio" value = "{$game->get_reported_on()}" />{$game->get_reported_on()}:&nbsp;You won {$game->get_loser()}
	            </div>
            </div>
        {foreachelse}
            <div>
			    No valid games.
			</div>
        {/foreach}
		<input type = "hidden" name = "tid" value = "{$tid}" />
		<input type = "hidden" name = "gid" id = "gid" value = "" />
        {if $games}
		    <input type = "submit" value = "Report" onclick = "javascript: report_game(); return false;" />
		{/if}
	</form>
</div>
<div style = "clear: both;">
</div>