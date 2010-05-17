{if $table}
    {assign var="winner" value=$table->get_winner()}
    <table class = "stroke" width = "100%">
	    {assign var="participants" value=$table->get_ordered_participants()}
		<tr>
		    {if $tournament->get_type()}
			    {assign var="td_count" value=$table->get_knock_out_stage_count()}
			    {section name="td" start=0 loop=$td_count}
				    <th>
					    Stage&nbsp;{$smarty.section.td.index+1}
					</th>
				{/section}
		    {else}
			    <th>
		            &nbsp;
		        </th>
			    <th>
			        &nbsp;
			    </th>
			    {foreach from=$participants key="key" item="smth"}
				    <th>
					    {$key+1}
					</th>
				{/foreach}
		    {/if}
		</tr>
		{if !$tournament->get_type()}
	        {foreach from=$participants key="key" item="participant"}
		        <tr>
                    <td>
				        {$key+1}
				    </td>
				    <td>
				        <a href = "profile.php?name={$participant->get_name()}">{$participant->get_name()}</a>
				    </td>
				    {foreach from=$participants item="p"}
				        <td>
					        {if $p->get_player_id() == $participant->get_player_id()}
							    -
							{else}
							    {assign var="the_row" value=$table->get_the_row($participant->get_player_id(), $p->get_player_id())}
								{if !$the_row->get_first_result() && !$the_row->get_second_result()}
								    &nbsp;
								{else}
							        {if $the_row->get_first_participant() == $participant->get_player_id()}
									    {$the_row->get_first_result()}
									{else}
									    {$the_row->get_second_result()}
									{/if}
								{/if}
							{/if}
					    </td>
				    {/foreach}
				</tr>
		    {/foreach}
		{else}
		    {section name="td" start=0 loop=$td_count+1}
		        <td>
				    {assign var="stage_pairs" value=$table->get_knock_out_stage_situation($smarty.section.td.index+1)}
				    {if $smarty.section.td.index!=$td_count}
					    {foreach from=$stage_pairs.rows item="row"}
						    {$row.first->get_name()}&nbsp;v/s&nbsp;{$row.second->get_name()}
						{/foreach}
					    {if $stage_pairs.free}
						    <br />
						    {$stage_pairs.free->get_name()}&nbsp;is free.
						{/if}
					{else}
					    {if $winner->get_id()}
						    {$winner->get_name()} is winner
						{/if}
					{/if}
				</td>
				{/section}
		{/if}
	</table>
	{if $winner->get_player_id()}
	    Tournament finished. {$winner->get_name()} is winner.
	{/if}
{else}
    Tournament is not played yet!
{/if}