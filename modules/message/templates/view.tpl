{assign var="sender" value=$topic->get_sender()}
{assign var="reciever" value=$topic->get_reciever()}
Message <strong>{$topic->get_topic()}</strong>&nbsp;
(<a href = "message.php?action=thread&topic={$topic->get_id()}">Show dialog</a>)<br />
Sender: <a href = "profile.php?name={$sender->get_name()}">{$sender->get_name()}</a><br />
Reciever: <a href = "profile.php?name={$reciever->get_name()}">{$reciever->get_name()}</a>
<hr />
{$message->get_content()}
<hr />
<a href = "javascript: history.back();">Back</a>