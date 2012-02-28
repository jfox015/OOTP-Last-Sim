
<div id="content">
	<div id="main">
		<h1>Last Sim -> Boxscores</h1>
		<div style="width:100%;text-align:right;">
		<p>
		<?php
			if (isset($teams) && sizeof($teams) > 0) {
			echo form_open();
			echo form_label("Select Team:", "team_id");
			echo '<select id="team_id" name="team_id">'."\n";
			$fnd=0;
			$minTm=0;
			foreach($teams as $row) {
				$tid=$row['team_id'];
				if ($minTm==0) {$minTm=$tid;}
				$tname=$row['name']." ".$row['nickname'];
				$tname=str_replace(".","",$tname);
				echo '  <option value="'.$tid.'">'.$tname.'</option>'."\n";
				if ($fnd==0) {$team_id=$minTm;}
			}
			echo '</select>'."\n";
			echo form_button("submitBtn","Go",' id="submitBtn"');
			echo form_close();
		}
		?>
		</div>
		<div class="2col">
			<div class="column">
			<h3>Last Sim Results</h3>
	<?php
	$tothr=0;
	$text = "";
	$gid = 0;
	$report_path = $settings['ootp.asset_url'];
	if (isset($boxscores) && is_array($boxscores) && sizeof($boxscores) > 0) {
		foreach($boxscores as $grow) {
			$inntxt="<td class='hc' width=18>&#160;</td>";
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
				$inntxt.="<td class='hc' width=15>&#160;</td>";
				$ainn.="<td class='ic' width=15>&#160;</td>";
				$hinn.="<td class='ic' width=15>&#160;</td>";
			}
			$inntxt.="<td class='hc' width=15>R</td><td class='hc' width=15>H</td><td class='hc' width=15>E</td>";
			$ainn.="<td class='icgb' width=15>".$grow['runs0']."</td><td class='ic' width=15>".$grow['hits0']."</td><td class='ic' width=15>".$grow['errors0']."</td>";
			$hinn.="<td class='icgb' width=15>".$grow['runs1']."</td><td class='ic' width=15>".$grow['hits1']."</td><td class='ic' width=15>".$grow['errors1']."</td>";
			
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
			unset($gdate);
			$gDate=Date("M j, Y",strtotime($grow['date']));
			$text.=" <table cellspacing=0 cellpadding=0 style='border:0px;width:400px;margin:10px;'>\n";
			$text.="  <tr>\n";
			$text.="   <td class='hl'>\n";
			$text.="    ".$gDate;
			$text.=": <a href='".$report_path."box_scores/game_box_".$gid.".html'>Box Score</a>";
			$text.=" | <a href='".$report_path."game_logs/log_".$gid.".html'>Game Log</a>\n";
			if ($gamecast_links) {
				$text.=" | ".anchor('gamecast/index/'.$gid,'Replay')."\n";
				}
			//$text.=" | ".anchor('lastsim/matchups/'.$hid.'/'.$aid,'Season Series')."\n";
			$text.="   </td>\n";
			$text.="  </tr>\n";
			$text.="  <tr>\n";
			$text.="   <td>\n";
			$text.="    <table cellpadding=0 cellspacing=0 style='border:1px black solid;width:400px;margin-top:2px;margin-left:0px;'>\n";
			$text.="     <tr>\n";
			$text.="      <td style='padding:1px;width:44px;border-right:1px solid #999999;'>\n";
			$text.="       <img src='".$report_path."images/".str_replace(".png","_40.png",$teams[$aid]['logo_file'])."' width=40 height=40><br>\n";
			$text.="       <img src='".$report_path."images/".str_replace(".png","_40.png",$teams[$hid]['logo_file'])."' width=40 height=40>\n";
			$text.="      </td>\n";
			$text.="      <td valign='top' style='padding:0px;margin:0px'>\n";
			$text.="       <table cellspacing=0 cellpadding=1 style='width:356px;margin:0px;border:0px'>\n";
			$text.="        <tr>".$inntxt."</tr>\n";
			$text.="        <tr><td class='gl'><a href='".$report_path."/teams/team_".$aid.".html'>".$teams[$aid]['name']."</a></td>";
			$text.=$ainn."</tr>\n";
			$text.="        <tr><td class='gl'><a href='".$report_path."/teams/team_".$hid.".html'>".$teams[$hid]['name']."</a></td>";
			$text.=$hinn."</tr>\n";
			$text.="        <tr><td colspan=16 class='gl' style='padding:6px 4px 4px 4px;'>".$ptxt."<br />".$hrtxt."</td></tr>\n";
			$text.="       </table>\n";
			$text.="      </td>\n";
			$text.="     </tr>\n";
			$text.="    </table>\n";
			$text.="   </td>\n";
			$text.="  </tr>\n";
			$text.=" </table>\n";
		} // END for
	} else {
		$text = "<p><b>No recent games were found.</p>\n";
	} // END if
	print($text);
	unset($text);
	?>
			</div>
			<div class="column">
			<h3>Upcoming Schedule</h3>
		<?php
		if (isset($upcoming) && is_array($upcoming) && sizeof($upcoming) > 0) {
			foreach($upcoming as $row) {
			unset($date);
			$date =	strtotime($row['date']);
			$text ="<table cellspacing=0 cellpadding=0 style='border:0px;width:400px;margin:10px;'>\n";
			$text.=" <tr>\n";
			$text.="  <td class='hl'>\n";
			$text.="   ".date("M j, Y", $date)."\n";
			$text.="  </td>\n";
			$text.=" </tr>\n";
			$text.=" <tr>\n";
			$text.="  <td>\n";
			$text.="    <table cellpadding=0 cellspacing=0 style='border:1px black solid;width:400px;margin-top:2px;margin-left:0px;'>\n";
			$text.="     <tr>\n";
			$text.="      <td style='padding:1px;width:44px;border-right:1px solid #999999;'>\n";
			$text.="       <img src='".$report_path."images/".str_replace(".png","_40.png",$teams[$aid]['logo_file'])."' width=40 height=40><br>\n";
			$text.="       <img src='".$report_path."images/".str_replace(".png","_40.png",$teams[$hid]['logo_file'])."' width=40 height=40>\n";
			$text.="      </td>\n";
			$text.="        <td valign='top' style='padding:0px;margin:0px'>\n";
			$text.="       <table cellspacing=0 cellpadding=1 style='width:356px;margin:0px;border:0px'>\n";
			$text.="        <tr><td class='hl' colspan=2>".$date("g:i a",$date)."</td></tr>\n";
			$text.="        <tr><td class='gl' width=175><a href='".$report_path."/teams/team_".$aid.".html'>".$teams[$aid]['name']."</a></td>\n";
			$text.="            <td class='gl' width=175>".$team_scores[$aid]['w']."-".$team_scores[$aid]['l'].", On Road: ".$team_scores[$aid]['rw']."-".$team_scores[$aid]['rl']."</td></tr>\n";
			$text.="        <tr><td class='gl' width=175><a href='".$report_path."/teams/team_".$hid.".html'>".$teams[$hid]['name']."</a></td>\n";
			$text.="            <td class='gl' width=175>".$team_scores[$hid]['w']."-".$team_scores[$hid]['l'].", At Home: ".$team_scores[$hid]['hw']."-".$team_scores[$hid]['hl']."</td></tr>\n";
			$text.="        <tr><td class='gl' colspan=2 style='padding:6px 4px 4px 4px;'><a href='./matchups.php?team_id1=".$hid."&team_id2=".$aid."'>Season Series</a>:<br />".$teams[$aid]['name']." ".max(0,$team_scores[$aid]['wVs'][$hid])." vs. ".max(0,$team_scores[$hid]['wVs'][$aid])." ".$teams[$hid]['name']."</td></tr>\n";
			$text.="       </table>\n";
			$text.="      </td>\n";
			$text.="     </tr>\n";
			$text.="    </table>\n";
			$text.="   </td>\n";
			$text.="  </tr>\n";
			$text.=" </table>\n";
			} // END for
		} else {
			$text = "<p><b>No upcoming games were found.</p>\n";
		} // END if // END if
		print($text);
		unset($text);
		?>	
			</div> <!-- column -->
		</div> <!-- 2col -->
	</div> <!-- main -->
</div> <!-- content -->
<script type="text/javascript">
    head.ready(function() {
        $('#submitBtn').click(function(e) {
            e.preventDefault();
            document.location.href="<?php echo(site_url()); ?>/lastsim/boxscores/"+$('#team_id').val();
        });
    });
</script>