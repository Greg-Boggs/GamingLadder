{if $error}
    {literal}
	    {'error': '{/literal}{$error}{literal}'}
	{/literal}
{else}
    {literal}
        {'success': 1}
    {/literal}
{/if}