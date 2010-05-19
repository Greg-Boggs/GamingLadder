Game is reported!
{if $winner}
    Winner is <a href = "profile.php?name={$winner->get_name()}">{$winner->get_name()}</a>!
	<script type="text/javascript">
	    window.location.reload();
	</script>
{/if}