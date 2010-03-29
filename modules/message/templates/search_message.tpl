{if !$result}
{literal}
    <style type = "text/css">
	    #json, #users {
		    display: none;
		}
		
		#users {
		    position: absolute; 
			border: 1px solid #000000; 
			background-color: #FFFFFF;
		}
		
		.user_list {
		    width: 200px;
		}
		
		.user_list_item {
		    cursor: pointer;
		}
		
		.user_list_item:hover {
		    color: #FFFFFF;
		    background-color: #000000;
		}
		
	</style>
    
    <script type = "text/javascript">
	    $(function() {
		    $("#init_date").datepicker();
		    $("#last_date").datepicker();
	    });

	    function refreshList(list) {
		    list.text('');
			list.hide();
		}
		
        function get_users(field) {
		    //Block, which contains list of matched users...
		    ulist = $('#users');
			name_prefix = field.value;
			//If more, than one user in list...
			if (name_prefix.indexOf(',') > -1) {
			    name_prefix = name_prefix.split(',');
				//get last name_prefix in list...
			    name_prefix = name_prefix[name_prefix.length - 1];
			}
			//Minimal length required...
			if (name_prefix.length < 3) {
			    refreshList(ulist);
			    return false;
			}
			//Block, which contains JSON code...
		    var json = $('#json');
			//Get users list...
	        json.load(
		        'message.php?action=get_players', 
		        {name_prefix: name_prefix},
				function () {
				    refreshList(ulist);
				    var users = eval('r = ' + $('#json').html());
					//array of users: [{id: id, name: name}, ..]
					users = users.users;
					//if no user returned...
					if (!users.length) {
			            return false;
			        }
			        for (i in users) {
					    user = $('<div class = "user_list_item">' + users[i].name + '</div>');
					    //If click item...
						user.click(function() {
						    //if more than one items, replace last...
						    if (field.value.indexOf(',') > -1) {
							    val = field.value.split(',');
								val[val.length - 1] = $(this).html();
							}
							else {
							    val = $(this).html();
							}
						    field.value = (field.value.indexOf(',') > -1)? val.join(',') : val;
							ulist.hide();
							field.focus();
						});
			            ulist.append(user);
			        }
					ulist.show();
				}
			);
		}
		
		function get_results() {
		    $('#search_result').load(
		        'message.php?action=search_message', 
		        {
				    form: 1,
					box: $('select[name=box]').val(),
					goal: $('select[name=goal]').val(),
					users: $('input:text[name=users]').val(),
					init_date: $('input:text[name=init_date]').val(),
					last_date: $('input:text[name=last_date]').val(),
					text: $('input:text[name=text]').val(),
					all_w: $('input:checkbox[name=all_w].checked').val()
				},
				function () {
				}
			);
		}
    </script>
{/literal}
<form action="" method = "post" onsubmit = "javascript: get_results(); return false;">
    <table>
        <tr>
            <td align="right">
                <strong>Message box:</strong>
            </td>
            <td>
                <select name = "box" onchange = "javascript: get_results();">
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
                <select name = "goal" onchange = "javascript: get_results();">
                    <option value = "0">Topic</option>
                    <option value = "1">Message Body</option>
                    <option value = "2">Both: topic and message body</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">
                <strong>Filters:</strong>
            </td>
            <td>
                &nbsp;
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                <table>
                    <tr>
                        <td align="right">
                            <strong>Was sent to users:</strong>
                            <br />
                            <span style = "font-size: 10pt">Write usernames, parting by comma</span>
                        </td>
                        <td>
                            <div>
                                <input type = "text" name = "users" value = "" onkeyup = "javascript: get_users(this);" onchange = "javascript: get_results();" class = "user_list" />
                                <div id = "users" class = "user_list">
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
                            <input id = "init_date" type = "text" name = "init_date" value = "{$init_date}" onchange = "javascript: get_results();" />
                            and 
                            <input id = "last_date" type = "text" name = "last_date" value = "{$last_date}" onchange = "javascript: get_results();" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="right">
                <strong>Goal phrase:</strong>
            </td>
            <td>
                <input type = "text" name = "text" value = "" onchange = "javascript: get_results();" />
                <input type = "checkbox" value = "1" name = "all_w" /> Search all words
            </td>
        </tr>
    </table>
    <input type = "submit" value = "Search" />
</form>
<div id = "json"></div>
<div id = "search_result" style = "width: 100%"></div>
{else}
<h2>Search Results</h2>
{foreach from=$results item="result"}
    {$result->get_topic()} with id = {$result->get_id()}<br />
{foreachelse}
    Nothing's found...
{/foreach}
{/if}