{if $created}
    Action successfully done! Wait, you will be automatically redirected...
    Click <a href = 'tournament.php?action=list_tournaments'>here</a> to redirect manualy.
    {html_entity->redirect url='tournament.php?action=list_tournaments'}
{else}
{literal}
    <script type = "text/javascript">
	    function get_filters() {
		    $('#loader').show();
			var l = $('#loader');
		    $('#filters').load(
			    'tournament.php?action=list_filters',
				{tid: {/literal}{if $tournament->get_id()}{$tournament->get_id()}{else}0{/if}{literal}},
				function() {
				    $('#filters').show();
					$('#filters').prepend(l);
					$('#loader').hide();
				}
			);
		}
	    $(function() {
	        $("#date_signup_start").datepicker();
		    $("#date_signup_end").datepicker();
		    $("#date_play_start").datepicker();
		    $("#date_play_end").datepicker();
			{/literal}{if $form}get_filters();{/if}{literal}
	    });
	</script>
{/literal}
{if $errors}<div class = "error"><strong>{$errors.spam}</strong></div>{/if}
<form action="" method = "post">
{if !$tournament->get_id()}
    <div class = "wrapper">
	    <div>
		    <strong>Type:</strong>
		</div>
		<div class = "block">
		    <input type = "radio" name = "type" value = "1" {if !$tournament->get_type()}checked = "checked"{/if} /> Circular
			&nbsp;
			<input type = "radio" name = "type" value = "2" {if $tournament->get_type()}checked = "checked"{/if} /> Knock out
		</div>
    </div>
    <div class = "wrapper">
	    <div>
		    <strong>Name:</strong>
		</div>
		<div class = "block">
		    <input type = "text" name = "name" value = "{$tournament->get_name()}" />
			    {if $errors}<div class = "error">{$errors.name}</div>{/if}
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Winner title:</strong>
		</div>
		<div class = "block">
		    <input type = "text" name = "winner_title" value = "{$winner_title}" />
		</div>
    </div>
{/if}
	<div class = "wrapper">
	    <div>
		    <strong>Information:</strong>
		</div>
		<div class = "block">
		    <textarea name = "information">{$tournament->get_information()}</textarea>
			{if $errors}<div class = "error">{$errors.information}</div>{/if}
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Rules:</strong>
		</div>
		<div class = "block">
		    <textarea name = "rules">{$tournament->get_rules()}</textarea>
		</div>
    </div>
{if !$tournament->get_id()}
	<div class = "wrapper">
	    <div>
		    <strong>Signup dates:</strong>
		</div>
		<div class = "block">
		    Start:&nbsp;<input type = "text" name = "date_signup_start" id = "date_signup_start" value = "{$tournament->get_date("sign_up_starts", "/")}" />
			End:&nbsp;<input type = "text" name = "date_signup_end" id = "date_signup_end" value = "{$tournament->get_date("sign_up_ends", "/")}" />
			{if $errors}<div class = "error">{$errors.date_signup_start}</div>{/if}
			{if $errors}<div class = "error">{$errors.date_signup_end}</div>{/if}
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Play dates:</strong>
		</div>
		<div class = "block">
		    Start:&nbsp;<input type = "text" name = "date_play_start" id = "date_play_start" value = "{$tournament->get_date("play_starts", "/")}" />
			End:&nbsp;<input type = "text" name = "date_play_end" id = "date_play_end" value = "{$tournament->get_date("play_ends", "/")}" />
			{if $errors}<div class = "error">{$errors.date_play_start}</div>{/if}
			{if $errors}<div class = "error">{$errors.date_play_end}</div>{/if}
		</div>
    </div>
	<div class = "wrapper">
	    <div>
		    <strong>Number of participants:</strong>
		</div>
		<div class = "block">
		    Min.:&nbsp;<input type = "text" name = "min_participants" value = "{$tournament->get_min_participants()}" />
			Max.:&nbsp;<input type = "text" name = "max_participants" value = "{$tournament->get_max_participants()}" />
			{if $errors}<div class = "error">{$errors.min_participants}</div>{/if}
			{if $errors}<div class = "error">{$errors.max_participants}</div>{/if}
		</div>
    </div>
{/if}
	<div class = "wrapper">
	    <div>
		    <strong>Number of games to play:</strong>
		</div>
		<div class = "block">
		    <input type = "text" name = "games_to_play" value = "{if $tournament->get_games_to_play()}{$tournament->get_games_to_play()}{else}1{/if}" />
			{if $errors}<div class = "error">{$errors.games_to_play}</div>{/if}
		</div>
    </div>
    <div class = "list">
        <div class = "list_header" onclick = "javascript: get_filters();">
	        <strong>Apply filter</strong>
	    </div>
	    <div class = "list_content" id = "filters">
		    {html_entity->loader text="Load..."}
	    </div>
    </div>
	<div style = "clear: both; height: 10px;">
    </div>
    <input name = "form" type = "hidden" value = "1" />
	<input name = "tid" type = "hidden" value = "{$tournament->get_id()}" />
    <input type = "submit" class = "button" value = "{if $tournament->get_id()}Save{else}Create{/if}" />
</form>
{/if}