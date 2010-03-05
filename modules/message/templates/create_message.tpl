{if $sent}
    Your message is sent! Wait, you will automatically redirect...
    Click <a href = 'message.php?action=show_message_box'>here</a> to nredirect manualy.
    {html_entity->redirect url='message.php?action=show_message_box'}
{else}
    <form action="" method = "post">
        <table>
            <tr>
                <td>
                    <strong>Reciever:</strong>
                </td>
                <td>
                    <select name = "reciever">
                        {foreach from=$users item="user"}
                            <option value = "{$user->get_player_id()}" {if $reciever == $user->get_player_id()}selected{/if}>{$user->get_name()}</option>
                        {/foreach}
                    </select>
                    {if $errors}<br />{$errors.reciever}{/if}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Topic:</strong>
                </td>
                <td>
                    <input name = "topic" id = "topic" value = "{$topic}" maxlength="64" />
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
    </form>
{/if}