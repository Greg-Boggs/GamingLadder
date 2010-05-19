<ul>
    {foreach from=$players item="player"}
        <li {cycle name="lines" values="class = 'selected',"}>
            <a href = "profile.php?name={$player->get_name()}">{$player->get_name()}</a>
        </li>
    {foreachelse}
        No players.
    {/foreach}
</ul>
<div style = "clear: both;">
</div>