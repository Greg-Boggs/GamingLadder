<?php /* Smarty version 2.6.26, created on 2010-05-10 15:20:53
         compiled from delete_message.tpl */ ?>
Messages deleted! Wait, you will automatically redirect...
Click <a href = '<?php echo $this->_tpl_vars['url']; ?>
'>here</a> to redirect manualy.
<?php echo $this->_reg_objects['html_entity'][0]->redirect($this->_tpl_vars['url']);?>