{literal}
    <script type = "text/javascript">
	    //<![CDATA[
		    function save_filter() {
		        $.ajax({
			        url: 'tournament.php?action=create_filter',
				    data: {tablefield: $('#tablefield').val(), value: $('#value').val(), operator: $('#operator').val(), form: 1},
				    success: function() {
					    get_filters();	
					}
				})
		    }
		//]]>
	</script>
{/literal}
<form action = "" method = "post" onsubmit = "javascript: save_filter(); return false;">
    <div class = "wrapper">
        <div>
	        <strong>Field:</strong>&nbsp;{html_options name=tablefield id=tablefield options=$fields}&nbsp;
			{html_options name=operator id=operator options=$operators}&nbsp;
			<strong>Value:</strong>&nbsp;<input type = "text" name = "value" id = "value" />&nbsp;
			<input type = "submit" value = "Save" style = "border: 1px solid #000000;" />
			<br />
			(You can use asterisk * in value field.)
        </div>
    </div>
</form>