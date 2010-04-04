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
		        'message.php?action=search_message' + '&p_c_p=' + ((current_page)? current_page : 0), 
		        {
				    form: 1,
					box: $('select[name=box]').val(),
					goal: $('select[name=goal]').val(),
					fromwhere: $('select[name=fromwhere]').val(),
					users: $('input:text[name=users]').val(),
					init_date: $('input:text[name=init_date]').val(),
					last_date: $('input:text[name=last_date]').val(),
					text: $('input:text[name=text]').val(),
					{/literal}
					    {if $user->get_is_admin()}
						    player: $('input:text[name=player]').val(),
							deleted: $('input:checkbox[name=deleted]').val(),
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
<form action="" method = "post" onsubmit = "javascript: getResults(); return false;">
    <table>
        {if $user->get_is_admin()}
            <tr>
                <td align="right">
                    <strong>Select player</strong>
                    <br />
                    <span style = "font-size: 10pt">Start writing username</span>
                </td>
                <td valign="top">
                    <div>
                        <input type = "text" name = "player" value = "" onkeyup = "javascript: getUsers(this, $('#players'));" class = "value_list" />
                        <div id = "players" class = "value_list">
                        </div>
                    </div>
                </td>
            </tr>
        {/if}
        <tr>
            <td align="right">
                <strong>Message box:</strong>
            </td>
            <td>
                <select name = "box">
                    <option value = "0">Inbox</option>
                    <option value = "1">Outbox</option>
                    <option value = "2">Both: inbox and outbox</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">
                <strong>Search in:</strong>
            </td>
            <td>
                <select name = "goal">
                    <option value = "0">Topic</option>
                    <option value = "1">Message Body</option>
                    <option value = "2">Both: topic and message body</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">
                <strong>Was</strong>&nbsp;<select name = "fromwhere"><option value = "0">Recieved from</option><option value = "1">Sent to</option><option value = "2">Sent to or Recieved from</option></select>&nbsp;the next users:</strong>
                <br />
                <span style = "font-size: 10pt">Write usernames, parting by comma</span>
            </td>
            <td valign="top">
                <div>
                    <input type = "text" name = "users" value = "" onkeyup = "javascript: getUsers(this, $('#users2'));" class = "value_list" />
                    <div id = "users2" class = "value_list">
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td align="right">
                <strong>Sent date:</strong>
            </td>
            <td>
                Between 
                <input id = "init_date" type = "text" name = "init_date" value = "{$init_date}" />
                and 
                <input id = "last_date" type = "text" name = "last_date" value = "{$last_date}" />
            </td>
        </tr>
        <tr>
            <td align="right">
                <strong>Goal phrase:</strong>
            </td>
            <td>
                <input type = "text" name = "text" value = "" />
                <input type = "checkbox" value = "0" name = "all_w" onchange = "javascript: this.value = (this.value == '1')? 0 : 1;" /> Full match
            </td>
        {if $user->get_is_admin()}
            <tr>
                <td align="right">
                <strong>Show deleted:</strong>
            </td>
            <td>
                <input type = "checkbox" value = "0" name = "deleted" onchange = "javascript: this.value = (this.value == '1')? 0 : 1;" />
            </td>
            </tr>
        {/if}
        </tr>
    </table>
    <input type = "submit" value = "Search" />
</form>
<div id = "search_result" style = "width: 100%"></div>
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