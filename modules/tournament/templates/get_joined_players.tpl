<div class = "list thread" style = "margin-top: 0;">
    {foreach from=$players item="player"}
        <div class = "wrapper {cycle name="lines" values="selected,"}">
            <div class = "message_title">
                <a href = "profile.php?name={$player->get_name()}">{$player->get_name()}</a>
	        </div>
        </div>
    {foreachelse}
        No players.
    {/foreach}
</div>
<div style = "clear: both;">
</div>