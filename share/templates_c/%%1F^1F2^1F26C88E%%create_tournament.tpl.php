<?php /* Smarty version 2.6.26, created on 2010-05-27 10:01:26
         compiled from create_tournament.tpl */ ?>
<?php if ($this->_tpl_vars['created']): ?>
    Action successfully done! Wait, you will be automatically redirected...
    Click <a href = 'tournament.php?action=list_tournaments'>here</a> to redirect manualy.
    <?php echo $this->_reg_objects['html_entity'][0]->redirect('tournament.php?action=list_tournaments');?>

<?php else: ?>
<?php echo '
    <script type = "text/javascript">
	    function get_filters() {
		    $(\'#loader\').show();
			var l = $(\'#loader\');
		    $(\'#filters\').load(
			    \'tournament.php?action=list_filters\',
				{tid: '; ?>
<?php if ($this->_tpl_vars['tournament']->get_id()): ?><?php echo $this->_tpl_vars['tournament']->get_id(); ?>
<?php else: ?>0<?php endif; ?><?php echo '},
				function() {
				    $(\'#filters\').show();
					$(\'#filters\').prepend(l);
					$(\'#loader\').hide();
				}
			);
		}
	    $(function() {
	        $("#date_signup_start").datepicker();
		    $("#date_signup_end").datepicker();
		    $("#date_play_start").datepicker();
		    $("#date_play_end").datepicker();
			'; ?>
<?php if ($this->_tpl_vars['form']): ?>get_filters();<?php endif; ?><?php echo '
	    });
	</script>
'; ?>

<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><strong><?php echo $this->_tpl_vars['errors']['spam']; ?>
</strong></div><?php endif; ?>
<form action="" method = "post">
<?php if (! $this->_tpl_vars['tournament']->get_id()): ?>
    <div class = "wrapper">
	    <div>
		    <strong>Type:</strong>
		</div>
		<div class = "block">
		    <input type = "radio" name = "type" value = "1" <?php if (! $this->_tpl_vars['tournament']->get_type()): ?>checked = "checked"<?php endif; ?> /> Circular
			&nbsp;
			<input type = "radio" name = "type" value = "2" <?php if ($this->_tpl_vars['tournament']->get_type()): ?>checked = "checked"<?php endif; ?> /> Knock out
		</div>
    </div>
    <div class = "wrapper">
	    <div>
		    <strong>Name:</strong>
		</div>
		<div class = "block">
		    <input type = "text" name = "name" value = "<?php echo $this->_tpl_vars['tournament']->get_name(); ?>
" />
			    <?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['name']; ?>
</div><?php endif; ?>
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Winner title:</strong>
		</div>
		<div class = "block">
		    <input type = "text" name = "winner_title" value = "<?php echo $this->_tpl_vars['winner_title']; ?>
" />
		</div>
    </div>
<?php endif; ?>
	<div class = "wrapper">
	    <div>
		    <strong>Information:</strong>
		</div>
		<div class = "block">
		    <textarea name = "information"><?php echo $this->_tpl_vars['tournament']->get_information(); ?>
</textarea>
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['information']; ?>
</div><?php endif; ?>
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Rules:</strong>
		</div>
		<div class = "block">
		    <textarea name = "rules"><?php echo $this->_tpl_vars['tournament']->get_rules(); ?>
</textarea>
		</div>
    </div>
<?php if (! $this->_tpl_vars['tournament']->get_id()): ?>
	<div class = "wrapper">
	    <div>
		    <strong>Signup dates:</strong>
		</div>
		<div class = "block">
		    Start:&nbsp;<input type = "text" name = "date_signup_start" id = "date_signup_start" value = "<?php echo $this->_tpl_vars['tournament']->get_date('sign_up_starts',"/"); ?>
" />
			End:&nbsp;<input type = "text" name = "date_signup_end" id = "date_signup_end" value = "<?php echo $this->_tpl_vars['tournament']->get_date('sign_up_ends',"/"); ?>
" />
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['date_signup_start']; ?>
</div><?php endif; ?>
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['date_signup_end']; ?>
</div><?php endif; ?>
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Play dates:</strong>
		</div>
		<div class = "block">
		    Start:&nbsp;<input type = "text" name = "date_play_start" id = "date_play_start" value = "<?php echo $this->_tpl_vars['tournament']->get_date('play_starts',"/"); ?>
" />
			End:&nbsp;<input type = "text" name = "date_play_end" id = "date_play_end" value = "<?php echo $this->_tpl_vars['tournament']->get_date('play_ends',"/"); ?>
" />
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['date_play_start']; ?>
</div><?php endif; ?>
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['date_play_end']; ?>
</div><?php endif; ?>
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Number of participants:</strong>
		</div>
		<div class = "block">
		    Min.:&nbsp;<input type = "text" name = "min_participants" value = "<?php echo $this->_tpl_vars['tournament']->get_min_participants(); ?>
" />
			Max.:&nbsp;<input type = "text" name = "max_participants" value = "<?php echo $this->_tpl_vars['tournament']->get_max_participants(); ?>
" />
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['min_participants']; ?>
</div><?php endif; ?>
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['max_participants']; ?>
</div><?php endif; ?>
		</div>
    </div>
<?php endif; ?>
	<div class = "wrapper">
	    <div>
		    <strong>Number of games to play:</strong>
		</div>
		<div class = "block">
		    <input type = "text" name = "games_to_play" value = "<?php if ($this->_tpl_vars['tournament']->get_games_to_play()): ?><?php echo $this->_tpl_vars['tournament']->get_games_to_play(); ?>
<?php else: ?>1<?php endif; ?>" />
			<?php if ($this->_tpl_vars['errors']): ?><div class = "error"><?php echo $this->_tpl_vars['errors']['games_to_play']; ?>
</div><?php endif; ?>
		</div>
    </div>
    <div class = "list">
        <div class = "list_header" onclick = "javascript: get_filters();">
	        <strong>Apply filter</strong>
	    </div>
	    <div class = "list_content" id = "filters">
		    <?php echo $this->_reg_objects['html_entity'][0]->loader("Load...");?>

	    </div>
    </div>
	<div style = "clear: both; height: 10px;">
    </div>
    <input name = "form" type = "hidden" value = "1" />
	<input name = "tid" type = "hidden" value = "<?php echo $this->_tpl_vars['tournament']->get_id(); ?>
" />
    <input type = "submit" class = "button" value = "<?php if ($this->_tpl_vars['tournament']->get_id()): ?>Save<?php else: ?>Create<?php endif; ?>" />
</form>
<?php endif; ?>