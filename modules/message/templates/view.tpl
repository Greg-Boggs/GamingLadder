{assign var="sender" value=$topic->get_sender()}
{assign var="reciever" value=$topic->get_reciever()}
Message <strong>{$topic->get_topic()}</strong><br />
Sender: <a href = "profile.php?name={$sender->get_name()}">{$sender->get_name()}</a><br />
Reciever: <a href = "profile.php?name={$reciever->get_name()}">{$reciever->get_name()}</a><br />
Message was sent at <i>{$topic->get_sent_date()}</i>
<hr />
{$message->get_content()}
<hr />
<strong>Thread of topic:</strong><br />
<div style = "width: 80%;">
    {application->load_module module_name='topic' module_action='thread' param=$topic->get_id()}
</div>
<a href = "javascript: history.back();">Back</a>