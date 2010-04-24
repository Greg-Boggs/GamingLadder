{literal}
    <script type = "text/javascript">
		function collapse(topic_id) {
		    var topic = $('#topic_content_' + topic_id);
		    if (!topic.html().length) {
			    topic.append($('#loader').css('display', 'block'));
			    topic.load(
				    'message.php?action=view_content', 
					{user: {/literal}{$user->get_player_id()}{literal}, topic: topic_id},
				    function (){collapse(topic_id);}
				);
			}
			else {
		        topic.slideToggle(600);
				$('#loader').css('display', 'none');
			}
		}
	</script>
{/literal}
<div class = "message_list thread" style = "margin-top: 0;">
{foreach from=$topics item="topic"}
    {if (!$topic->get_deleted_by_sender() && $user->get_player_id()==$topic->get_sender_id()) ||  (!$topic->get_deleted_by_reciever() && $user->get_player_id()==$topic->get_reciever_id()) || $user->get_is_admin()}
	    {assign var="sender" value=$topic->get_sender()}
        {assign var="reciever" value=$topic->get_reciever()}
	    <div class = "wrapper {cycle name="lines" values="selected,"}" style = "cursor: pointer;" onclick = "javascript: collapse({$topic->get_id()});">
		    <div class = "message_date">
		        <img src = "images/date.png" alt = "[{$topic->get_sent_date()}]" title = "Sent: {$topic->get_sent_date()}" />
			</div>
			<div class = "message_user" style = "color: #{cycle name="users" values="909786,B2B9A8"};">
			    <span>From <a style = "color: #909786;" href = "profile.php?name={$sender->get_name()}">{$sender->get_name()}</a> to <a style = "color:  #909786;" href = "profile.php?name={$reciever->get_name()}">{$reciever->get_name()}</a>:&nbsp;</span>
			</div>
			<div class = "message_title">
                {$topic->get_topic()}
			</div>
			<div class = "message_view">
                <a href = "message.php?action=view&topic={$topic->get_id()}"><img src = "images/view.png" alt = "View message" title = "View message" /></a>
			</div>
        </div>
		<div id = "topic_content_{$topic->get_id()}" style = "display: none;"></div>
	{/if}
{/foreach}
</div>
<div style = "clear: both;">
</div>
{html_entity->loader}
{if $topics}
    {html_entity->paginate total=$total url=$url items_per_page=$items_per_page is_js_url=$is_js}
{/if}