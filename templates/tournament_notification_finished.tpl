Dear player! Tournament <a href = "tournament.php?action=view_tournament&amp;tid={$tournament->get_id()}" title = "Go to the tournament">{$tournament->get_name()}</a> is finished!
{assign var="winner" value=$table->get_winner()}
{if $user->get_player_id() == $winner->get_player_id()}
    <h1>You are the winner!</h1>
{else}
    <h2>{if $winner->get_player_id() > 0}<a href = "profile.php?name={$winner->get_name()}">{$winner->get_name()}</a>{else}{$winner->get_name()}{/if} is winner.</h2>
{/if}