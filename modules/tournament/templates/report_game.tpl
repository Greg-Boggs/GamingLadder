Game is reported!
{if $winner}
    Winner is {$winner->get_name()}!
{/if}
<div>
    <a href = "tournament.php?action=view_tournament&amp;tid={$tid}">Back</a>
</div>