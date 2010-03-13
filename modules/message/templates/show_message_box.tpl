<a href = "message.php?action=show_message_box">{if $box=='inbox'}<u>Inbox</u>{else}Inbox{/if}</a>
&nbsp;|&nbsp;
<a href = "message.php?action=show_message_box&box=outbox">{if $box=='outbox'}<u>Outbox</u>{else}Outbox{/if}</a>
<div>
    {foreach from=$topics item="topic"}
        <a href = "message.php?action=view&topic={$topic->get_id()}">{$topic->get_topic()}</a><br />
    {foreachelse}
        No messages in <strong>{$box}</strong>
    {/foreach}
</div>