		<?php
		if (!isset($type) || empty($type)): $type = "TOP_PLAYERS"; endif;
		switch($type) :
			case "MVP":
				if (!isset($series_name) || empty($series_name)): $series_name = ""; endif; 
				$title = $series_name.' '.lang('tp_title_mvp');
				break;
			case "SPOTLIGHT":
				$title = lang('tp_title_spotlight_on');
				break;
            case "TOP_PLAYERS":
			default;
				$title = lang('tp_title_top_players');
				switch ($player_type) :
					case TYPE_OFFENSE:
						$title .= 'Batters';
						break;
					case TYPE_SPECIALTY:
						$title .= 'Pitchers';
                        break;
					case TYPE_DEFENSE:
						$title .= 'Defenders';
                        break;
				endswitch;
		endswitch;
		if (isset($performers) && is_array($performers) && count($performers)) :
        ?>
		<div class="mod-container mod-scoreboard">
            <div class="mod-header<?php echo (($type != "TOP_PLAYERS") ? ' mod-mvp' : ''); ?>"><h4><?php echo $title; ?></h4></div>
                <div class="mod-content">

                    <ul class="top-performer <?php echo (($type != "TOP_PLAYERS") ? 'spotlight' : ''); ?>">
                    <?php
                        $rownum = 1;
                        foreach($performers as $row) :
                            $tid = $row['team_id'];
                            if (file_exists($settings['osp.players_img_path'].'player_'.$row['player_id'].'.png')) {
                                $player_img = $settings['osp.players_img_url'].'player_'.$row['player_id'].'.png';
                            } 
							else 
							{
                                $player_img = $settings['osp.asset_url'].'images/default_player_photo.png';
                            }
                            ?>
                            <li class="<?php echo($rownum==1 ? 'first':'alt');?> leader">
                            <?php
							$img_width = 35;
							$img_height = 48;
							if ($type != "TOP_PLAYERS") :
								$img_width = 65;
								$img_height = 100;
							endif;
							?>
								<!-- player pic -->
							<a href="<?php echo($settings['osp.asset_url']);?>players/player_<?php echo($row['player_id']);?>.html"><img
                                src="<?php echo($player_img);?>" border="0" width="<?php echo $img_width; ?>" height="<?php echo $img_height; ?>" alt="" title="" /></a>
                            <ul class="top-performers">
								
								<!-- player name -->
								<li><?php echo($row['player_name']);?></strong></a>
                                    &nbsp;&nbsp;<a href="<?php echo($settings['osp.asset_url']);?>teams/team_<?php echo($tid);?>.html"><?php echo($teams[$tid]['abbr']); ?></a></li>
                                
								<?php if (isset($mvp_summary) && !empty($mvp_summary)) : ?>
									<!-- player description -->
								<li id="leader-desc"><?php echo($mvp_summary); ?></li>
								<?php endif; ?>
								
									<!-- stats -->
								<li id="leader-1-stats">
                                    <?php
                                    $count = 0;
                                    foreach($stats_class as $field) :
										if ($field != 'PID' && $field != 'TID' && $field != 'TRO' && $field != 'TRP' && $field != 'PN' && $field != 'TN' && $field != 'ROLE') :
											$value = '';
											$label = '';
                                            if (isset($stats_list['general'][$field]['field']))
											{
                                                $value = $row[$stats_list['general'][$field]['field']];
												$label = lang("acyn_".$stats_list['general'][$field]['lang']);
											}
											else if (isset($stats_list[$player_type][$field]['field']))
											{
                                                $value = $row[$stats_list[$player_type][$field]['field']];
												$label = lang("acyn_".$stats_list[$player_type][$field]['lang']);
											}
											if ($value > 0) :
                                                if ($count > 0) : echo(", "); endif;
                                                echo($value.' '.$label);
											    $count++;
                                            endif;
										endif;
									endforeach;
                                    ?>
                                </li>
                            </ul>
                        </li>
						<?php
						$rownum++;
					endforeach;
					?>
					</ul>
                </div>
                <br clear="all" />
            </div>
        <br clear="all" />
        <?php
        endif;
        ?>