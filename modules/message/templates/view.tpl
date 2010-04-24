{assign var="sender" value=$topic->get_sender()}
{assign var="reciever" value=$topic->get_reciever()}
<div class = "column" style = "margin-left: 5%;">
    {html_entity->message_box_menu selected=1}
</div>
<div class = "column">
    <div style = "margin-top: 30px; font-size: 12px;">
	    <div>
            <strong>Sender:</strong> <a href = "profile.php?name={$sender->get_name()}">{$sender->get_name()}</a>
		</div>
        <div>
		    <strong>Reciever:</strong> <a href = "profile.php?name={$reciever->get_name()}">{$reciever->get_name()}</a>
		</div>
		<div>
		    Message was sent at <br />
			<i>{$topic->get_sent_date()}</i>
		</div>
	</div>
</div>
<div style = "clear: both;">
</div>
<div class = "message" style = "margin-left: 5%;">
    <div class = "topic">
        <h1>{$topic->get_topic()}</h1>
	</div>

    <div class = "content">
        <pre>{$message->get_content()}</pre>
	</div>
	<div style = "text-align: right;">
	    <a href = "message.php?action=create_message&amp;reciever={$sender->get_name()}&amp;topic={$topic->get_topic()|escape:'url'}"><img src = "images/reply.png" alt = "" />Reply</a>
	</div>
	<strong>Thread of topic:</strong>
    {application->load_module module_name='topic' module_action='thread' param=$topic->get_id()}
<a href = "javascript: history.back();">Back</a>
</div>