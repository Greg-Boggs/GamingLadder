{if $sent}
    Your message is sent! Wait, you will be automatically redirected...
    Click <a href = 'message.php?action=show_message_box'>here</a> to redirect manualy.
    {html_entity->redirect url='message.php?action=show_message_box'}
{else}
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
    <form action="" method = "post">
        <table>
            <tr>
                <td>
                    <strong>Reciever:</strong>
                </td>
                <td>
                    <input type = "text" name = "reciever" value = "{$reciever->get_name()}" onkeyup = "javascript: getUsers(this, $('#players'));" class = "value_list" />
                    <div id = "players" class = "value_list"></div>
                    {if $errors}<br />{$errors.reciever}{/if}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Topic:</strong>
                </td>
                <td>
                    <input name = "topic" id = "topic" value = "{$topic}" maxlength="64" class = "value_list" />
                    <br />
                    {if $errors}<br />{$errors.topic}{/if}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Message:</strong>
                </td>
                <td>
                    <textarea name = "content">{$message->get_content()}</textarea>
                    {if $errors}<br />{$errors.content}{/if}
                </td>
            </tr>
        </table>
        <input name = "form" type = "hidden" value = "1" />
        <input type = "submit" value = "Send" />
		{if $user->get_is_admin()}
		    <input type = "checkbox" value = "a" name = "admin_sent" /> Send as Admin
		{/if}
    </form>
{/if}