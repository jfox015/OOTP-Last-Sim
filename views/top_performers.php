		<?php 
		switch ($player_type) {
			case 0:
				$type = 'Batters';
				$stats = array('AVG','R','H','HR','RBI','SB','OBP','SLG');
				break;
			case 1:
				$type = 'Pitchers';
				$stats = array('W','L','S','IP','ERA','K','WHIP','OAVG');
				break;
		}
		?>
        <div class="mod-container mod-scoreboard">
            <div class="mod-header"><h4>TOP <?php echo strtoupper($type); ?></h4></div>
                <div class="mod-content">

                    <ul class="top-performer">
                    <?php
                        $rownum = 1;
                        foreach($performers as $row) :
                            $cls = ($rownum-1) % 2 + 1;
                            $tid = $row['team_id'];

                            if (file_exists($settings['osp.players_img_path'].'player_'.$row['player_id'].'.png')) {
                                $player_img = $settings['osp.players_img_url'].'player_'.$row['player_id'].'.png';
                            } else {
                                $player_img = $settings['osp.asset_url'].'images/default_player_photo.png';
                            }

                            ?>
                            <li class="<?php echo($rownum==1 ? 'first':'alt');?> leader">
                            <a href="<?php echo($settings['osp.asset_url']);?>players/player_<?php echo($row['player_id']);?>.html"><img
                                src="<?php echo($player_img);?>" border="0" width="35" height="48" alt="" title="" /></a>
                            <ul class="top-performers">
                                <li><a href="<?php echo($settings['osp.asset_url']);?>players/player_<?php echo($row['player_id']);?>.html"><strong><?php echo($row['first_name'] . " " . $row['last_name']);?></strong></a>
                                    &nbsp;&nbsp;<a href="<?php echo($settings['osp.asset_url']);?>teams/team_<?php echo($tid);?>.html"><?php echo($teams[$tid]['abbr']); ?></a></li>
                                <li id="leader-1-stats">
                                    <?php
                                    $count = 0;
                                    foreach($stats as $stat):
                                        $val = $row[strtolower($stat)];
                                        if ($val > 0) {
											if ($player_type == 0)
											{
												switch ($stat)
												{
													case 'AVG':
														$val = strstr(sprintf("%.3f",$row['avg']),".");
														break;

													case 'OBP':
														$val = strstr(sprintf("%.3f",$row['obp']),".");
														break;

													case 'SLG':
														$val = strstr(sprintf("%.3f",$row['slg']),".");
														break;

													case 'OPS':
														$val = sprintf("%.3f", $row['obp'] + $row['slg']);
														if ($val < 1) {
															$val = strstr($val, ".");
														}
														break;
													default:
														break;
												}
											}
											else if ($player_type == 1)
											{
												switch ($stat)
												{
													case 'IP':
														$val = sprintf("%.1f", $row['ip']);
														break;
													case 'ERA':
														$val = sprintf("%.2f", $row['era']);
														break;
													case 'WHIP':
														$val = sprintf("%.2f", $row['whip']);
														break;
													case 'OAVG':
														$val = sprintf("%.3f", $row['oavg']);
														if ($val < 1) {
															$val = strstr($val, ".");
														}
														break;
													default:
														break;
												}
											}
											if ($count > 0) { echo(", "); }
											echo($val.' '.$stat);
											$count++;
										}
                                    endforeach;
                                    ?>
                                </li>
                            </ul>
                        </li>
                            <?php
                            $rownum++;
                        endforeach;
                        ?></ul>

                </div>
                <br clear="all" />
            </div>
        <br clear="all" />