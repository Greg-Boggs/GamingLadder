{if $table && $table->get_tournament_id()}
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
			    {assign var="participants" value=$table->calculate_places()}
			    <th style = "width: 3%;">
		            &nbsp;
		        </th>
			    <th>
			        &nbsp;
			    </th>
			    {section name="key" loop=$participants.count step=1}
				    <th valign = "bottom">
					    {$smarty.section.key.index+1}
					</th>
				{/section}
				<th style = "width: 3%;" valign = "bottom">
				    Total
				</th>
				<th style = "width: 5%;">
				    Berger Coeff.
				</th>
				<th style = "width: 5%;" valign = "bottom">
				    Place
				</th>
		    {/if}
		</tr>
		{if !$tournament->get_type()}
	        {section name="key1" loop=$participants.count}
			    {assign var="count" value=$participants.count[key1].count}
				{assign var="uid1" value=$participants.count[key1].uid}
			    {assign var="participant" value=$table->get_participant_by_id($uid1)}
		        <tr {cycle name="lines" values="class='selected',"}>
                    <td style = "border-left: 1px solid #000000;">
				        {$smarty.section.key1.rownum}
				    </td>
				    <td>
				        <a href = "profile.php?name={$participant->get_name()}">{$participant->get_name()}</a>
				    </td>
				    {section name="key2" loop=$participants.count}
					    {assign var="count" value=$participants.count[key2].count}
				        {assign var="uid2" value=$participants.count[key2].uid}
				        <td>
					        {if $uid2 == $uid1}
							    <img src = "images/square.png" alt = "-" />
							{else}
							    {assign var="the_row" value=$table->get_the_row($uid1, $uid2)}
								{if !$the_row->get_first_result() && !$the_row->get_second_result()}
								    &nbsp;
								{else}
								    <div class = "score">
							            {if $the_row->get_first_participant() == $uid1}
									        {$the_row->get_first_result()}
									    {else}
									        {$the_row->get_second_result()}
									    {/if}
									</div>
									<div class = "number_of_games_subtitle">
									    game&nbsp;{$the_row->get_played_games()}&nbsp;of&nbsp;{$tournament->get_games_to_play()}
									</div>
								{/if}
							{/if}
					    </td>
				    {/section}
					<td>
					    <div class = "score">
						    {$participants.total[$uid1]}
						</div>
					</td>
					<td>
					    <div class = "score">
						    {$participants.bc[$uid1]}
						</div>
					</td>
					<td>
					    <div class = "score">
					        {if !$winner || $winner->get_player_id() != -1}{$participants.place[$uid1]}{else}-{/if}
						</div>
					</td>
				</tr>
		    {/section}
		{else}
		    {section name="td" start=0 loop=$td_count}
		        <td {if $smarty.section.td.index==0}style = "border-left: 1px solid #000000;"{/if}>
				    {assign var="stage_pairs" value=$table->get_situation($smarty.section.td.index+1)}
				    {if $smarty.section.td.index!=$td_count}
					    <table>
					        {foreach from=$stage_pairs item="row"}
							    {assign var="p1" value=$table->get_participant_by_id($row->get_first_participant())}
								{assign var="p2" value=$table->get_participant_by_id($row->get_second_participant())}
						        <tr>
								    <td>    
							            {$p1->get_name()}
								    </td>
									<td>
								        <img src = "images/competitors.png" alt = "v/s" />
									</td>
									<td>
								        {$p2->get_name()}
									</td>
									<td style = "text-align: center;">
								        <img src = "images/forward.png" alt = "=>" />
									</td>
									<td>
									    {if $row->get_game_dt()!='0000-00-00 00:00:00'}
									        {if $row->get_first_result()}
										        {$p1->get_name()}
										    {else}
										        {$p2->get_name()}
										    {/if}
									    {else}
									        <strong>?</strong>
									    {/if}
									<td>
							    </tr>
						    {/foreach}
						</table>
					{/if}
				</td>
				{/section}
		{/if}
	</table>
	{if $winner && $winner->get_player_id() > 0}
	    <div style = "padding-top: 10px; border-top: 1px solid #000000;">
	        Tournament is finished. <a href = "profile.php?name={$winner->get_name()}">{$winner->get_name()}</a> is winner.
		</div>
	{else}
	    {if $winner && $winner->get_player_id() == -1}
	        <div style = "padding-top: 10px; border-top: 1px solid #000000;">
	            Tournament is finished. Tie.
		    </div>
		{/if}
	{/if}
{else}
    Tournament is not played yet!
{/if}