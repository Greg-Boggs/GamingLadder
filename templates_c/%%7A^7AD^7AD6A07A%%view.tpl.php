<?php /* Smarty version 2.6.26, created on 2010-04-04 17:10:29
         compiled from view.tpl */ ?>
<?php $this->assign('sender', $this->_tpl_vars['topic']->get_sender()); ?>
<?php $this->assign('reciever', $this->_tpl_vars['topic']->get_reciever()); ?>
Message <strong><?php echo $this->_tpl_vars['topic']->get_topic(); ?>
</strong><br />
Sender: <a href = "profile.php?name=<?php echo $this->_tpl_vars['sender']->get_name(); ?>
"><?php echo $this->_tpl_vars['sender']->get_name(); ?>
</a><br />
Reciever: <a href = "profile.php?name=<?php echo $this->_tpl_vars['reciever']->get_name(); ?>
"><?php echo $this->_tpl_vars['reciever']->get_name(); ?>
</a><br />
Message was sent at <i><?php echo $this->_tpl_vars['topic']->get_sent_date(); ?>
</i>
<hr />
<?php echo $this->_tpl_vars['message']->get_content(); ?>

<hr />
<strong>Thread of topic:</strong><br />
<div style = "width: 80%;">
    <?php echo $this->_reg_objects['application'][0]->load_module('topic','thread',$this->_tpl_vars['topic']->get_id());?>

</div>
<a href = "javascript: history.back();">Back</a>