<a href = "message.php?action=show_message_box">{if $box=='inbox'}<u>Inbox</u>{else}Inbox{/if}</a>
&nbsp;|&nbsp;
<a href = "message.php?action=show_message_box&box=outbox">{if $box=='outbox'}<u>Outbox</u>{else}Outbox{/if}</a>
<div>
    <form action = "message.php?action=delete_message" method = "post">
        <table>
            {foreach from=$topics item="topic"}
                <tr>
                    <td>
                        <a href = "message.php?action=view&topic={$topic->get_id()}">{$topic->get_topic()}</a>
                    </td>
                    <td>
                        <input type = "checkbox" name = "messages[]" value = "{$topic->get_id()}" /><br />
                    </td>
                </tr>
            {foreachelse}
                <tr><td></td></tr></table>
                <table>
                    <tr>
                        <td>
                            No messages in <strong>{$box}</strong>
                        </td>
                    </tr>
            {/foreach}
        </table>
        <input type = "hidden" name = "box" value = "{$box}" />
        <a href = "javascript: document.forms[0].submit();">Delete selected</a>
    </form>
</div>