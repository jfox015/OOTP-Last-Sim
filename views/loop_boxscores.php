		<?php
		$tothr=0;
		$text = "";
		$gid = 0;
		$boxCount = 0;
		$report_path = $settings['ootp.asset_url'];
		if (isset($boxscores) && is_array($boxscores) && sizeof($boxscores) > 0) {
			foreach($boxscores as $grow) {
				$inntxt='<td class="hc" width="18">&#160;</td>';
				$ainn="";
				$hinn="";
				$inncnt=0;
				$gid = $grow['game_id'];
				$hid=$grow['home_team'];
				$hname=$teams[$hid]['name'];
				$aid=$grow['away_team'];
				$aname=$teams[$aid]['name'];
				if (isset($grow['inningScores']) && sizeof($grow['inningScores']) > 0 ) {
					foreach($grow['inningScores'] as $row) {
						if ($row['team']==0)  {
							$inntxt.="<td class='hc' width=15>".$row['inning']."</td>";
							$ainn.="<td class='ic' width=15>".$row['score']."</td>";
							$inncnt++;
						} else {
							if (($grow['runs1']>$grow['runs0']) && ($row['inning']>8) && ($row['inning']==$grow['innings']) && ($row['score']==0)) {$row['score']="X";}
							$hinn.="<td class='ic' width=15>".$row['score']."</td>";
						}
					}
				}
				for ($i=0;$i<(12-$inncnt);$i++) {
					$inntxt	.=	'<td class="hc" width="15">&#160;</td>'."\n";
					$ainn	.=	'<td class="ic" width="15">&#160;</td>'."\n";
					$hinn	.=	'<td class="ic" width="15">&#160;</td>'."\n";
				}
				$inntxt	.=	"<td class='hc' width=15>R</td><td class='hc' width=15>H</td><td class='hc' width=15>E</td>";
				$ainn	.=	"<td class='icgb' width=15>".$grow['runs0']."</td><td class='ic' width=15>".$grow['hits0']."</td><td class='ic' width=15>".$grow['errors0']."</td>";
				$hinn	.=	"<td class='icgb' width=15>".$grow['runs1']."</td><td class='ic' width=15>".$grow['hits1']."</td><td class='ic' width=15>".$grow['errors1']."</td>";
				
				// PITCHERS LINES
				$ptxt="";
				if (isset($grow['pitcherInfo']) && sizeof($grow['pitcherInfo']) > 0 ) {
					$pitcher = array();
					foreach($grow['pitcherInfo'] as $type =>$row) {
						$fi=$row['first_name'];
						$fi=$fi[0];
						$pitcher[$type]='<a href="'.$report_path.'players/player_'.$row['player_id'].'.html">'.$fi.'. '.$row['last_name'].'</a>';
					}
					$ptxt="W: ".$pitcher['wp']." L: ".$pitcher['lp'];
					if (isset($pitcher['sv']) && !empty($pitcher['sv'])) {$ptxt.=" S: ".$pitcher['sv'];}
				}
			
				// HR HITTERS LINKS
				$hrdata=array();
				$hrtxt="";
				$tothr=0;
				if (isset($grow['batterInfo']) && sizeof($grow['batterInfo']) > 0 ) {
					foreach($grow['batterInfo'] as $row) {
						$hrcnt=$row['hr'];
						$tid=$row['team_id'];
						if (!isset($hrdata[$tid])) { $hrdata[$tid] = ""; }
											if ($hrcnt>0) {
							$fi=$row['first_name'];
							$fi=$fi[0];
							$hrdata[$tid].=" <a href='".$report_path."players/player_".$row['player_id'].".html'>".$fi.". ".$row['last_name']."</a>";
							if ($hrcnt>1) {$hrdata[$tid].=" (".$hrcnt.")";}
							$hrdata[$tid].=",";
							$tothr++;
						}
					}
					if ($hrdata[$aid]!="") {$hrdata[$aid]=$teams[$aid]['abbr'].":".substr_replace($hrdata[$aid],"",-1);}
					if ($hrdata[$hid]!="") {$hrdata[$hid]=$teams[$hid]['abbr'].":".substr_replace($hrdata[$hid],"",-1);}
					if ($tothr==0) {
						$hrtxt="&nbsp;";
					} else {
						$hrtxt="HR - ".$hrdata[$aid]." ".$hrdata[$hid];
					}
				}
				// GAME NOTES
				if (isset($grow['notes']) && !empty($grow['notes']))
				{
					$gnote=$grow['notes'];
					$gnote=trim($gnote,", ");
					$gnote="<br />Notes: ".$gnote;
				}
				unset($gdate);
				
				/*---------------------------------
				/	BEGIN BOX HTML OUTPUT
				/--------------------------------*/
				$gDate=Date("M j, Y",strtotime($grow['date']));
				?>
				<div id="box_<?php echo($boxCount); ?>" style="display:block;">
				<table cellspacing="0" cellpadding="0" style="border:0px;width:400px;margin:10px;">
					<!-- MATCHUP anD LINKS -->
				<tr>
					<td class="hl"><?php echo($gDate.': <a href="'.$settings['ootp.asset_url'].'box_scores/game_box_'.$gid.'.html">Box Score</a>'); ?>
					<a href="<?php echo($settings['ootp.asset_url']. 'game_logs/log_'.$gid); ?>.html">Game Log</a>
					<?php 
					if ($gamecast_links) {
						echo(' | Replay: '.anchor('#','Inline',array('id'=>'1_'.$boxCount.'_'.$gid,'rel'=>'replay'))." | ".anchor('#','New Window',array('id'=>'2_'.$boxCount.'_'.$gid,'rel'=>'replay')));
					}
					?>
					</td>
				</tr>
					<!-- BEGIN SCORE OUTPUT -->
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" style="border:1px black solid;width:400px;margin-top:2px;margin-left:0px;">
					<tr>
						<td style="padding:1px;width:44px;border-right:1px solid #999999;">
						<img src="<?php echo($settings['ootp.team_logo_path'].str_replace(".png","_40.png",$teams[$aid]['logo_file']));?>" width="40" height="40"><br />
						<img src="<?php echo($settings['ootp.team_logo_path'].str_replace(".png","_40.png",$teams[$hid]['logo_file']));?>" width="40" height="40"><br />
						</td>
						<td valign="top" style="padding:0px;margin:0px">
						<table cellspacing="0" cellpadding="1" style="width:356px;margin:0px;border:0px">
						<tr><?php echo($inntxt); ?></tr>
						<tr>
							<td class='gl'><a href="<?php echo($settings['ootp.asset_url'].'teams/team_'.$aid); ?>.html"><?php echo($teams[$aid]['name']); ?></a></td>
							<?php echo($ainn); ?>
						</tr>
						<tr>
							<td class='gl'><a href="<?php echo($settings['ootp.asset_url'].'teams/team_'.$hid); ?>.html"><?php echo($teams[$hid]['name']); ?></a></td>
							<?php echo($hinn); ?>
						</tr>
						<tr>
							<td colspan="16" class="gl" style="padding:6px 4px 4px 4px;"><?php echo($ptxt."<br />".$hrtxt); ?></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</div>
				<?php 
				if ($gamecast_links) {
					$text.='<div id="gc_'.$boxCount.'" style="display:none;">'."\n</div>\n";
				}
				$boxCount++;
			} // END foreach
		} else {
			echo('<p><b>No recent games were found.</b></p>');
		}
		?>