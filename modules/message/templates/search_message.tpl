{if !$result}
{literal}
    <script type = "text/javascript">
	    $(function() {
		    $("#init_date").datepicker();
		    $("#last_date").datepicker();
			$('input').change(function() {
		        getResults();
		    });
		    $('select').change(function() {
		        getResults();
		    });
	    });
		
        function getUsers(field, dest) {
		    autoComplete(
			    field.value, 
				'message.php?action=get_players', 
				dest, 
				field
			);
		}
		
		function getResults(current_page) {
		    $('#search_result').load(
		        'message.php?action=search_message' + '&amp;p_c_p=' + ((current_page)? current_page : 0), 
		        {
				    form: 1,
					box: $('select[name=box]').val(),
					goal: $('select[name=goal]').val(),
					fromwhere: $('select[name=fromwhere]').val(),
					status: $('select[name=status]').val(),
					signature: $('select[name=signature]').val(),
					users: $('input:text[name=users]').val(),
					init_date: $('input:text[name=init_date]').val(),
					last_date: $('input:text[name=last_date]').val(),
					text: $('input:text[name=text]').val(),
					{/literal}
					    {if $user->get_is_admin()}
						    player: $('input:text[name=player]').val(),
							hide_deleted: $('input:checkbox[name=hide_deleted]').val(),
						{/if}
					{literal}
					all_w: $('input:checkbox[name=all_w]').val()
				},
				function () {
				}
			);
		}
    </script>
{/literal}
<div class = "column" style = "margin-left: 5%;">
    {html_entity->message_box_menu selected=2}
</div>
<div class = "column" style = "width: auto;">
    <form action="" method = "post" id = "search_form" onsubmit = "javascript: getResults(); return false;">
        <div class = "block_search_message">
            {if $user->get_is_admin()}
                <div class = "wrapper">
				    <div>
                        <strong>Player</strong>
					</div>
                    <div class = "block_select_user">
                        <input type = "text" name = "player" value = "" onkeyup = "javascript: getUsers(this, $('#players'));" class = "value_list" />
                        <div id = "players" class = "value_list">
                        </div>
                    </div>
				</div>
            {/if}
            <div class = "wrapper">
			    <div>
                    <strong>Message box:</strong>
				</div>
				<div>
                    <select name = "box">
                        <option value = "0">Inbox</option>
                        <option value = "1">Outbox</option>
                        <option value = "2">Both: inbox and outbox</option>
                    </select>
				</div>
			</div>
            <div class = "wrapper">
		        <div>
                    <strong>Search in</strong>
				</div>
				<div>
                    <select name = "goal">
                        <option value = "0">Topic</option>
                        <option value = "1">Message Body</option>
                        <option value = "2">Both: topic and message body</option>
                    </select>
				</div>
			</div>
            <div class = "wrapper">
		        <div>
                    <strong>Message status:</strong>
				</div>
				<div>
				    <select name = "status">
                        <option value = "0">Read</option>
                        <option value = "1">Unread</option>
                        <option value = "2" selected="selected">Both: read and unread</option>
                    </select>
				</div>
			</div>
            <div class = "wrapper">
		        <div>
                    <strong>Message signature:</strong>
				</div>
				<div>
				    <select name = "signature">
                        <option value = "u">From user</option>
                        <option value = "a">From admin</option>
                        <option value = "s">System</option>
					    <option value = "w">Warning</option>
					    <option value = "0">Either</option>
                    </select>
				</div>
			</div>
            <div class = "wrapper">
		        <div>
                    <strong>Was</strong>&nbsp;
					<select name = "fromwhere">
					    <option value = "0">Recieved from</option>
						<option value = "1">Sent to</option>
						<option value = "2">Sent to or Recieved from</option>
					</select>
					&nbsp;<strong>the next users:</strong>
				</div>
                <div class = "block_select_user">
				    <input type = "text" name = "users" value = "" onkeyup = "javascript: getUsers(this, $('#users2'));" class = "value_list" />
                    <div id = "users2" class = "value_list">
					</div>
                </div>
            </div>
            <div class = "wrapper">
		        <div>
                    <strong>Sent date:</strong>
				</div>
				<div>
				    Between 
                    <input id = "init_date" type = "text" name = "init_date" value = "{$init_date}" />
                    and 
                    <input id = "last_date" type = "text" name = "last_date" value = "{$last_date}" />
				</div>
			</div>
            <div class = "wrapper">
		        <div>
                    <strong>Goal phrase:</strong>
				</div>
				<div>
				    <input type = "text" name = "text" value = "" />
                    <input type = "checkbox" value = "0" name = "all_w" onchange = "javascript: this.value = (this.value == '1')? 0 : 1;" /> Full match
				</div>
			</div>
            {if $user->get_is_admin()}
                <div class = "wrapper">
				    <div>
                        <strong>Hide deleted:</strong>
					</div>
					<div>
					    <input type = "checkbox" value = "1" name = "hide_deleted" checked = "checked" onchange = "javascript: this.value = (this.value == '1')? 0 : 1;" />
					</div>
				</div>
            {/if}
		</div>
		<div style = "clear: both;">
        </div>
        <input type = "submit" value = "Search" />
    </form>
    <div id = "search_result" style = "width: 100%">
	</div>
</div>
{else}
    <h2 style = "border: 0;">Search Results</h2>
    {if $results}
        <div style = "width: 80%;">
            {application->load_module module_name='topic' module_action='thread' param=$results}
        </div>
    {else}
        Nothing's found...
    {/if}
{/if}
<div style = "clear: both;">
</div>