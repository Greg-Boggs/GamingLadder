{if $table && $table->get_tournament_id()}
    {assign var="winner" value=$table->get_winner()}
    <table class = "stroke" width = "100%">
		<tr>
		    {if $tournament->get_type()}
			    {assign var="td_count" value=$table->get_knock_out_stage_count()}
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
		    <tr>
		        <td {if $smarty.section.td.index==0}style = "border-left: 1px solid #000000;"{/if}>
			        <table cellpadding = "0" cellspacing = "0">
				        <tr>
					        {section name="td" start=0 loop=$td_count}
						        <td class = "stage">
				                    {assign var="stage_pairs" value=$table->get_situation($smarty.section.td.index+1)}
									{assign var="lindex" value=$smarty.section.td.index+1}
				                    <div class = "stroke_col">
									    {if !$winner || $lindex != $td_count}
										    <strong>Stage&nbsp;{$smarty.section.td.index+1}</strong>
										{/if}
				                            {foreach name="pairs" from=$stage_pairs[0] item="row"}
							                    {assign var="tindex" value=$smarty.foreach.pairs.index+1}
					                            {assign var="p1" value=$table->get_participant_by_id($row->get_first_participant())}
						                        {assign var="p2" value=$table->get_participant_by_id($row->get_second_participant())}
											    {if !$maxh}
											        {assign var="maxh" value=$stage_pairs[1]*45}
											    {/if}
							                    {if $stage_pairs[1]}
											        {assign var="h" value=$maxh/$stage_pairs[1]}
											    {else}
											        {assign var="h" value=$maxh}
											    {/if}
												{if $lindex > 1 }
												    {assign var = "t" value = "5"}
												{else}
												    {assign var = "t" value = "0"}
												{/if}
						                        {if $p2}
							                        <div class = "pair" style = "height: {$h+$t+5}px;">
						                                <div class = "participant_info" style = "height: {$h+$t}px;">
													        <div class = "info">
						                                        <span><a href = "profile.php?name={$p1->get_name()}">{$p1->get_name()}</a></span>
														        <img src = "images/competitors.png" alt = "v/s" />
														        <span><a href = "profile.php?name={$p2->get_name()}">{$p2->get_name()}</a></span>
														    </div>
						                                </div>
									                    <div class = "line">
									                        <div class = "score">
										                        {$row->get_first_result()}:{$row->get_second_result()}
										                    </div>
									                    </div>
								                    </div>
												    {assign var="free" value=0}
						                       {else}
											       {if !$winner || $lindex != $td_count}
										               {assign var="free" value=$p1->get_name()}
												   {else}
												       <div class = "participant_info" style = "height: {$h+$t}px;">
										                   <div class = "info">
						                                       <span><strong><a href = "profile.php?name={$p1->get_name()}">{$p1->get_name()}</a></strong></span> is winner!
						                                   </div>
										              </div>
											       {/if}
						                       {/if}
					                       {/foreach}
									       <div class = "free">
						                       <span>{if $free}<a href = "profile.php?name={$free}">{$free}</a> is free{else}&nbsp;{/if}</span>
									       </div>
				                   </div>
				               </td>
			               {/section}
		               </tr>
			       </table>
		       </td>
		   </tr>
		   <script type = "text/javascript">
		       {literal}
			       $('.line').each(function(index, elem){
			           $(elem).css({top: -$(elem).prev().height()/2 - $(elem).height() - 5});
			       });
				   $('.participant_info .info, .stroke_col').each(function(index, elem){
			           $(elem).css({top: $(elem).parent().height()/2 - $(elem).height()/2});
			       });
			   {/literal}
		   </script>
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