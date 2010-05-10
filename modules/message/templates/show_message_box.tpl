<div class = "column" style = "margin-left: 5%;">
    {html_entity->message_box_menu selected=1}
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
		<div class = "label">
		    Select player
		</div>
        <div class = "block_select_user block">
            <div>
		        <form action = "" method = "post">
                    <input type = "text" class = "textfield" name = "player" value = "{$player->get_name()}" onkeyup = "javascript: getUsers(this, $('#players'));" class = "value_list" />
                    <div id = "players" class = "value_list">
                    </div>
				    <input type = "submit" class = "button" value = "Show" />
			    </form>
            </div>
	    </div>
    {/if}
	<div class = "label">
        Message box:
	</div>
	<div class = "select_box block">
	    <div {if $box=='inbox'}class = "selected_item"{/if}>
	        <a href = "message.php?action=show_message_box&amp;player={$player->get_name()}">Inbox</a>
		</div>
		<div {if $box=='outbox'}class = "selected_item"{/if}>
            <a href = "message.php?action=show_message_box&amp;box=outbox&amp;player={$player->get_name()}">Outbox</a>
		</div>
    </div>
</div>
<div class = "column" style = "width: 70%;">
    <form action = "message.php?action=delete_message" method = "post">
        <div class = "message_list">
		    <a href = "message.php?box={$box}&amp;player={$player->get_name()}&amp;unread=1">Only unread messages</a>
	        &nbsp;|&nbsp;
	        <a href = "message.php?box={$box}&amp;player={$player->get_name()}">All messages</a>
			<table width = "100%">
            {foreach from=$topics item="topic"}
			    <tr {cycle name="lines" values='class = "selected",'}>
				    <td class = "message_date">
					    <img src = "images/date.png" alt = "[{$topic->get_sent_date()}]" title = "{if $box=='outbox'}Sent{else}Recieved{/if}: {$topic->get_sent_date()}" />
					</td>
					<td class = "message_user" style = "color: #{cycle name="users" values="909786,B2B9A8"};">
					    {if $box=='inbox'}From{else}To{/if}
						{if $topic->get_signature() != 's'}
						    {if $box=='inbox'}{$topic->get_sender()->get_name()}{else}{$topic->get_reciever()->get_name()}{/if}
					    {else}
						    <a href = "/">System</a>
						{/if}
						:&nbsp;
					</td>
                    <td class = "message_title">
                        {if !$topic->get_read_date()}
					        <strong>
					    {/if}
                        <a href = "message.php?action=view&amp;topic={$topic->get_id()}">{$topic->get_topic()}</a>
						{if $user->get_is_admin()}
					        {if ($topic->get_deleted_by_sender() && $box=='outbox') || ($topic->get_deleted_by_reciever() && $box=='inbox')}
					            (<img src = "images/deleted.png" alt = "[deleted]" title = "Deleted" class = "deleted" />)
				            {/if}
					    {/if}
					    {if !$topic->get_read_date()}
					        </strong>
					    {/if}
                    </td>
                    <td align = "right">
				        <input type = "checkbox" name = "messages[]" value = "{$topic->get_id()}" /><br />
                    </td>
				</tr>
            {foreachelse}
                <tr>
				    <td>
			            No messages in <strong>{$box}</strong>
                    </td>
				</tr>
            {/foreach}
        </table>
        <input type = "hidden" name = "box" value = "{$box}" />
		<input type = "hidden" name = "player" value = "{$player->get_name()}" />
		<div style = "clear: both;">
		</div>
		{if $topics}
		    {html_entity->paginate total=$total url=$url items_per_page=$items_per_page}
		{/if}
		<hr />
		<div style = "width: 100%; text-align: right;">
            <a href = "javascript: document.forms[{if $user->get_is_admin()}1{else}0{/if}].submit();">Delete selected</a>
		    {if $user->get_is_admin()}
		        <input type = "checkbox" name = "totally" value = "1" /> from database.
		    {/if}
		</div>
    </form>
</div>
<div style = "clear: both;">
</div>