<?php /* Smarty version 2.6.26, created on 2010-03-13 17:14:00
         compiled from view.tpl */ ?>
<?php $this->assign('sender', $this->_tpl_vars['topic']->get_sender()); ?>
<?php $this->assign('reciever', $this->_tpl_vars['topic']->get_reciever()); ?>
Message <strong><?php echo $this->_tpl_vars['topic']->get_topic(); ?>
</strong>&nbsp;
(<a href = "message.php?action=thread&topic=<?php echo $this->_tpl_vars['topic']->get_id(); ?>
">Show dialog</a>)<br />
Sender: <a href = "profile.php?name=<?php echo $this->_tpl_vars['sender']->get_name(); ?>
"><?php echo $this->_tpl_vars['sender']->get_name(); ?>
</a><br />
Reciever: <a href = "profile.php?name=<?php echo $this->_tpl_vars['reciever']->get_name(); ?>
"><?php echo $this->_tpl_vars['reciever']->get_name(); ?>
</a>
<hr />
<?php echo $this->_tpl_vars['message']->get_content(); ?>

<hr />
<a href = "javascript: history.back();">Back</a>