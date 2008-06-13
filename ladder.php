<?php
require('variables.php');
require('variablesdb.php');
require('top.php');
require('ladder_cookie.inc.php');


?>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
	$('#waiting').load('ladderdata.php', function(){init_sort()});
}
);
function init_sort()
    {
        $("#ladder")
    	.tablesorter({widthFixed: true, widgets: ['zebra']}) 
    	.tablesorterPager({container: $("#pager")});
    }
</script>
<p><p><p>
<div id="waiting"><p align="center">
<img src="graphics/baby.gif" /><br/>Please wait while the ladder loads...</p>

</div>
<?php
require('bottom.php');
?>
