{if $table}
    {assign var="winner" value=$table->get_winner()}
    <table class = "stroke" width = "100%">
		<tr>
		    {if $tournament->get_type()}
			    {assign var="td_count" value=$table->get_knock_out_stage_count()}
			    {section name="td" start=0 loop=$td_count}
				    <th>
					    Stage&nbsp;{$smarty.section.td.index+1}
					</th>
				{/section}
		    {else}
			    {assign var="participants" value=$table->get_ordered_participants()}
			    <th style = "width: 3%;">
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
		        <tr {cycle name="lines" values="class='selected',"}>
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
		    {section name="td" start=0 loop=$td_count}
		        <td>
				    {assign var="stage_pairs" value=$table->get_knock_out_stage_situation($smarty.section.td.index+1)}
				    {if $smarty.section.td.index!=$td_count}
					    {foreach from=$stage_pairs item="row"}
						    <div {if $row->get_game_dt() != '0000-00-00 00:00:00'}style="color: red;"{/if}>
							    {$table->get_participant_by_id($row->get_first_participant())->get_name()}
								&nbsp;v/s&nbsp;
								{$table->get_participant_by_id($row->get_second_participant())->get_name()}
							</div>
						{/foreach}
					{/if}
				</td>
				{/section}
		{/if}
	</table>
	{if $winner->get_player_id()}
	    Tournament is finished. {$winner->get_name()} is winner.
	{/if}
{else}
    Tournament is not played yet!
{/if}