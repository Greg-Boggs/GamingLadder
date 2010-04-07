{if $user->get_is_admin()}
    {literal}
        <script type = "text/javascript">
	        function getUsers(field, dest) {
		        autoComplete(
			        field.value, 
				    'message.php?action=get_players', 
				    dest, 
				    field
			    );
		    }
        </script>
    {/literal}
    <table>
        <tr>
            <td align="right">
                <strong>Select player</strong>
                <br />
                <span style = "font-size: 10pt">Start writing username</span>
            </td>
            <td valign="top">
                <div>
				    <form action = "" method = "post">
                        <input type = "text" name = "player" value = "{$player->get_name()}" onkeyup = "javascript: getUsers(this, $('#players'));" class = "value_list" />
                        <div id = "players" class = "value_list">
                        </div>
				        <input type = "submit" value = "Show" />
					</form>
                </div>
            </td>
        </tr>
	</table>
{/if}
<a href = "message.php?action=show_message_box&amp;player={$player->get_name()}">{if $box=='inbox'}<u>Inbox</u>{else}Inbox{/if}</a>
&nbsp;|&nbsp;
<a href = "message.php?action=show_message_box&box=outbox&amp;player={$player->get_name()}">{if $box=='outbox'}<u>Outbox</u>{else}Outbox{/if}</a>
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
		<input type = "hidden" name = "player" value = "{$player->get_name()}" />
		{if $topics}
		    {html_entity->paginate total=$total url=$url items_per_page=$items_per_page}
		{/if}
		<hr />
        <a href = "javascript: document.forms[{if $user->get_is_admin()}1{else}0{/if}].submit();">Delete selected</a>
		{if $user->get_is_admin()}
		    <input type = "checkbox" name = "totally" value = "1" /> from database.
		{/if}
    </form>
</div>