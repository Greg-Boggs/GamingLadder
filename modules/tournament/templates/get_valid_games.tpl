<div class = "list thread" style = "margin-top: 0;">
    <form action="tournament.php?action=report_game" method="post">
        {foreach from=$games item="game"}
            <div class = "wrapper {cycle name="lines" values="selected,"}">
                <div class = "message_title">
			        <input name = "game" type = "radio" value = "{$game->get_reported_on()}" />{$game->get_reported_on()}:&nbsp;You won {$game->get_loser()}
	            </div>
            </div>
        {foreachelse}
            <div>
			    No valid games.
			</div>
        {/foreach}
		<input type = "hidden" name = "tid" value = "{$tid}" />
        {if $games}
		    <input type = "submit" value = "Report" />
		{/if}
	</form>
</div>
<div style = "clear: both;">
</div>