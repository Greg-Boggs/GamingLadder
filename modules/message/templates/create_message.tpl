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
	<div class = "column" style = "margin-left: 5%;">
	    {html_entity->message_box_menu selected=0}
	</div>
	<div class = "column" style = "width: auto;">
	    <form action="" method = "post">
	        <div class = "block_create_message">
                <div class = "wrapper">
                    <div>
				        <strong>Reciever:</strong>
			        </div>
				    <div class = "block_select_user block">    
                        <input type = "text" name = "reciever" value = "{$reciever->get_name()}" onkeyup = "javascript: getUsers(this, $('#players'));" class = "value_list" />
                        <div id = "players" class = "value_list"></div>
                        {if $errors}<br />{$errors.reciever}{/if}
				    </div>
				</div>
                <div class = "wrapper">
                    <div>
					    <strong>Topic:</strong>
					</div>
					<div class = "block">
					    <input name = "topic" id = "topic" class = "textfield" value = "{$topic}" maxlength="64" class = "value_list" />
                        {if $errors}<br />{$errors.topic}{/if}
					</div>
                </div>
				<div class = "wrapper">
				    <div>
					    <strong>Message:</strong>
					</div>
					<div class = "block">
					    <textarea name = "content">{$message->get_content()}</textarea>
                        {if $errors}<br />{$errors.content}{/if}
					</div>
				</div>
			</div>
            <input name = "form" type = "hidden" value = "1" />
            <input type = "submit" class = "button" value = "Send" />
		    {if $user->get_is_admin()}
		        <input type = "checkbox" value = "a" name = "admin_sent" /> Send as Admin
		    {/if}
        </form>
	</div>
{/if}
<div style = "clear: both;">
</div>