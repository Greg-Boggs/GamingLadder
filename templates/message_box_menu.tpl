<div class = "message_box_menu block">
    <div {if $selected == 0}class = "selected_item"{/if}>
	    <a href = "message.php?action=create_message">Compose message</a>
	</div>
	<div {if $selected == 1}class = "selected_item"{/if}>
        <a href = "message.php?action=show_message_box">Message box</a>
	</div>
	<div {if $selected == 2}class = "selected_item"{/if}>
	    <a href = "message.php?action=search_message">Search</a>
	</div>
</div>