			<?php
			if (isset($upcoming) && is_array($upcoming) && sizeof($upcoming) > 0) {
				foreach($upcoming as $row) {
					$gid = $row['game_id'];
					$hid=$row['home_team'];
					$hname=$teams[$hid]['name'];
					$aid=$row['away_team'];
					$aname=$teams[$aid]['name'];
					?>
					<table cellspacing="0" cellpadding="0" style="border:0px;width:400px;margin:10px;">
					<tr>
						<td class="hl"><?php echo(date("M j, Y", strtotime($row['date']))." ".format_time($row['time']));?></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" style="border:1px black solid;width:400px;margin-top:2px;margin-left:0px;">
						<tr>
							<td style="padding:1px;width:44px;border-right:1px solid #999999;">
							<img src="<?php echo($settings['ootp.team_logo_url'].str_replace(".png","_40.png",$teams[$aid]['logo_file']));?>" width="40" height="40"><br />
							<img src="<?php echo($settings['ootp.team_logo_url'].str_replace(".png","_40.png",$teams[$hid]['logo_file']));?>" width="40" height="40"><br />
							</td>
							
							<td valign="top" style="padding:0px;margin:0px">
							<table cellspacing"0" cellpadding="1" style="width:356px;margin:0px;border:0px">
							<tr>
								<td class="hl" colspan="2"><?php echo(format_time($row['time'])); ?></td>
							</tr>
							<tr>
								<td class='gl'><a href="<?php echo($settings['ootp.asset_url'].'teams/team_'.$aid); ?>.html"><?php echo($teams[$aid]['name']); ?></a></td>
								<?php
								if (isset($team_scores) && sizeof($team_scores)> 0 && isset($team_scores[$aid])) { ?>
								<td class="gl" width="175"><?php echo($team_scores[$aid]['w']."-".$team_scores[$aid]['l']); ?>, On Road: <?php echo($team_scores[$aid]['rw']."-".$team_scores[$aid]['rl']); ?></td>
								<?php } ?>
							</tr>
							<tr>
								<td class='gl'><a href="<?php echo($settings['ootp.asset_url'].'teams/team_'.$hid); ?>.html"><?php echo($teams[$hid]['name']); ?></a></td>
								<?php
								if (isset($team_scores) && sizeof($team_scores)> 0 && isset($team_scores[$hid])) { ?>
								<td class="gl" width="175"><?php echo($team_scores[$hid]['w']."-".$team_scores[$hid]['l']); ?>, On Road: <?php echo($team_scores[$hid]['rw']."-".$team_scores[$hid]['rl']); ?></td>
								<?php } ?>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
				<?php
				} // END foreach
			} else {
				echo('<p><b>No upcoming games were found.</b></p>'."\n");
			} // END if // END if
			?>	