		<?php
		$tothr=0;
		$text = "";
		$gid = 0;
		$boxCount = 0;
		$report_path = $settings['osp.asset_url'];
		if (isset($boxscores) && is_array($boxscores) && sizeof($boxscores) > 0) :
			foreach($boxscores as $grow) :			
				$gid = $grow['game_id'];
				$hid=$grow['home_team'];
				$hname=$teams[$hid]['name'];
				$aid=$grow['away_team'];
				$aname=$teams[$aid]['name'];
				
				/*---------------------------------
				/	BEGIN BOX HTML OUTPUT
				/--------------------------------*/
				?>
				<h4 class="gameDate"><?php echo(Date("M j, Y",strtotime($grow['date'])));?></h4>
				<div id="box_<?php echo($boxCount); ?>" class="mod-container mod-no-header-footer final-state mod-scorebox" style="display:block;">
					<span id="sort" class="sort" style="display: none;">0</span>
					<div class="mod-content">
						<div class="game-header">
							<div class="game-status"><p id="statusLine1">Final</p></div>
							<ul id="linescoreHeader" class="score header" style="display: block">
								<li class="finalScore">R</li>
								<li>H</li>
								<li>E</li>
							</ul>
						</div>
						<div class="team away">
							<div id="aWinner" class="winner-arrow" style="display:<?php if($grow['runs0'] > $grow['runs1']) { echo('block'); } else { echo('none'); } ?>"></div>
							<div class="logo-small" style="background: url(<?php echo($settings['osp.team_logo_url'].str_replace(".png","_40.png",$teams[$aid]['logo_file']));?>) top left no-repeat;"></div>
							<div class="team-capsule">
								<p id="aNameOffset" class="team-name" style="padding-top:6px;">
									<span id="aTeamName"><a href="<?php echo($settings['osp.asset_url'].'teams/team_'.$aid); ?>.html"><?php echo($teams[$aid]['name']); ?></a></span>
								</p>
								<p id="aRecord" class="record">&nbsp;</p>
							</div>

							<ul id="aScores" class="score" style="display:block">
								<li id="alshT" class="finalScore"><?php echo($grow['runs0']); ?></li>
								<li id="alshH"><?php echo($grow['hits0']); ?></li>
								<li id="alshE"><?php echo($grow['errors0']); ?></li>
							</ul>
							</div>
						<div class="team home">
							<div id="hWinner" class="winner-arrow" style="display:<?php if($grow['runs1'] > $grow['runs0']) { echo('block'); } else { echo('none'); } ?>"></div>
							<div class="logo-small" style="background: url(<?php echo($settings['osp.team_logo_url'].str_replace(".png","_40.png",$teams[$hid]['logo_file']));?>) top left no-repeat;"></div>
							<div class="team-capsule">
								<p id="hNameOffset" class="team-name" style="padding-top:6px;">
									<span id="hTeamName"><a href="<?php echo($settings['osp.asset_url'].'teams/team_'.$hid); ?>.html"><?php echo($teams[$hid]['name']); ?></a></span>
								</p>
								<p id="hRecord" class="record">&nbsp;</p>
							</div>

							<ul id="hScores" class="score" style="display:block">
								<li id="hlshT" class="finalScore"><?php echo($grow['runs1']); ?></li>
								<li id="hlshH"><?php echo($grow['hits1']); ?></li>
								<li id="hlshE"><?php echo($grow['errors1']); ?></li>
							</ul>
						</div>
						<div id="gameLinks" class="expand-gameLinks">
							<a href="<?php echo($settings['osp.asset_url'].'box_scores/game_box_'.$gid.'.html'); ?>">Box&nbsp;Score</a>
							&nbsp;&#187;&nbsp;  <a href="<?php echo($settings['osp.asset_url']. 'game_logs/log_'.$gid.'.html'); ?>">Game Log</a>
							<?php 
							if ($gamecast_links) {
								echo('&nbsp;&#187;&nbsp; Replay: '.anchor('#','Inline',array('id'=>'1_'.$boxCount.'_'.$gid,'rel'=>'replay'))." | ".anchor('#','New Window',array('id'=>'2_'.$boxCount.'_'.$gid,'rel'=>'replay')));
							}
							?>
							&nbsp;&#187;&nbsp; <?php echo anchor('/lastsim/matchup/'.$aid.'/'.$hid,'Matchup'); ?>
						</div>
						
						<?php
						if (isset($grow['inningScores']) && sizeof($grow['inningScores']) > 0 ) :
							$ainn="";
							$hinn="";
							$inncnt=0;
							$innRowTpl = '<td style="padding: 0px 2px 0px 2px;" id="lsh';
							$innRowSum = '<th class="featured" style="padding: 0px 2px 0px 2px;" id="lsh';
							$tInnTpl = '<td style="padding: 0px 2px 0px 2px;" id="';
							$tInnSum = '<td style="padding: 0px 2px 0px 2px;" class="featured" id="';
							?>
						<div class="border-top game-info-module clear" id="gameDetails">
							<table class="game-details" summary="Game Details">
						
							<?php
							$inntxt='';
							$ainn='';
							$hinn='';
							foreach($grow['inningScores'] as $row) :
								if ($row['team']==0)  :
									$inntxt.=$innRowTpl.($row['inning']-1).'">'.$row['inning'].'</td>';
									$ainn.=$tInnTpl.'als'.($row['inning']-1).'">'.$row['score'].'</td>';
									$inncnt++;
								else :
									if (($grow['runs1']>$grow['runs0']) && ($row['inning']>8) && ($row['inning']==$grow['innings']) && ($row['score']==0)) {$row['score']="X";}
									$hinn.=$tInnTpl.'hls'.($row['inning']-1).'">'.$row['score'].'</td>';
								endif;
							endforeach;
							for ($i=0;$i<(12-$inncnt);$i++) {
								$inntxt	.=	'<th style="width: 5px;">&nbsp;</th>'."\n";
								$ainn	.=	'<td>&nbsp;</td>'."\n";
								$hinn	.=	'<td>&nbsp;</td>'."\n";
							}
							$inntxt	.=	$innRowSum.'T">R</td>'."\n".$innRowSum.'H">H</td>'."\n".$innRowSum.'E">E</td>'."\n";
							$ainn	.=	$tInnSum.'T">'.$grow['runs0'].'</td>'."\n".$tInnSum.'H">'.$grow['hits0'].'</td>'."\n".$tInnSum.'E">'.$grow['errors0'].'</td>'."\n";
							$hinn	.=	$tInnSum.'T">'.$grow['runs1'].'</td>'."\n".$tInnSum.'H">'.$grow['hits1'].'</td>'."\n".$tInnSum.'E">'.$grow['errors1'].'</td>'."\n";
							?>

							<thead>
								<td style="width: 20%;">&nbsp;</td>
								<?php echo($inntxt); ?>
							</thead>
							<tbody>
							<tr>
								<td class="team"><a href="<?php echo($settings['osp.asset_url'].'teams/team_'.$aid.'.html'); ?>"><?php echo($teams[$aid]['abbr']); ?></a></td>
								<?php echo($ainn); ?>
							</tr>
							<tr>
								<td class="team"><a href="<?php echo($settings['osp.asset_url'].'teams/team_'.$hid.'.html'); ?>"><?php echo($teams[$hid]['abbr']); ?></a></td>
								<?php echo($hinn); ?>
							</tr>
							
							</tbody>
							</table>
						</div>
						<?php
						endif;
						?>
							
						<?php
						// PITCHERS LINES
						if (isset($grow['pitcherInfo']) && sizeof($grow['pitcherInfo']) > 0 ) : ?>
							<!-- PITCHER INFO -->
						<div class="clear border-top game-info-module" id="winningPitchers">
						<?php
							$pitcher = array();
							foreach($grow['pitcherInfo'] as $type =>$row) {
								$fi=$row['first_name'];
								$fi=$fi[0];
								$pitcher[$type]='<a href="'.$report_path.'players/player_'.$row['player_id'].'.html">'.$fi.'. '.$row['last_name'].'</a>';
							}
						?>
							<span id="winningPitcher"><strong>W:&nbsp;</strong> <?php echo($pitcher['wp']); ?>&nbsp;&nbsp; </span>
							<span id="losingPitcher"><strong>L:&nbsp;</strong>	<?php echo($pitcher['lp']); ?>&nbsp;&nbsp; </span>
							<span id="savingPitcher"><?php echo((isset($pitcher['sv']) && !empty($pitcher['sv'])) ? '<strong>SV:&nbsp;</strong>	'.$pitcher['sv'] : ''); ?></span>
						</div>
						<?php
						endif;
						
						$hrdata=array();
						$hHrtxt="";
                        $aHrtxt="";
						$tothr=0;
						if (isset($grow['batterInfo']) && sizeof($grow['batterInfo']) > 0) : ?>
							<!-- HOME RUN SUMMARY -->
						<div id="homeRuns" class="border-top game-info-module">
						<?php
							foreach($grow['batterInfo'] as $row) :
								$hrcnt=$row['hr'];
								$tid=$row['team_id'];
								if (!isset($hrdata[$tid])) { $hrdata[$tid] = ""; }
								if ($hrcnt>0) :
                                    if (!empty($hrdata[$tid])) { $hrdata[$tid] .= ","; }
                                    $fi=$row['first_name'];
									$fi=$fi[0];
									$hrdata[$tid].=' <a href="'.$report_path.'players/player_'.$row['player_id'].'.html">'.$fi.'. '.$row['last_name'].'</a>';
									if ($hrcnt>1) {$hrdata[$tid].="&nbsp;(".$hrcnt.")";}
									$tothr++;
								endif;
							endforeach;
                            echo("HR - ");
     						if ($hrdata[$aid]!="") :
                                echo('<span class="awayHomers"><strong>'.$teams[$aid]['abbr'].'</strong> :'.substr_replace($hrdata[$aid],"",-1).'</span>');
                            endif;
                            if ($hrdata[$hid]!="") :
                                echo('<span class="homeHomers"><strong>'.$teams[$hid]['abbr']."</strong> :".substr_replace($hrdata[$hid],"",-1).'</span>');
                            endif;
                            if (empty($hrdata[$hid]) && empty($hrdata[$aid])) {
                                echo("None");
                            }
						?>
						</div>
						<?php
						endif;
						if (isset($grow['notes']) && !empty($grow['notes'])) :
						?>
								<!-- NOTES -->
							<div id="gameNote" class="border-top game-info-module">
								<?php echo(trim($grow['notes'],", ")); ?>
							</div>
						<?php
						endif;
						?>
					</div>
				</div>
				<?php
				if ($gamecast_links) :
					$text.='<div id="gc_'.$boxCount.'" style="display:none;">'."\n</div>\n";
				endif;
				$boxCount++;				
			endforeach; // END foreach
		else :
			echo('<p><b>No recent games were found.</b></p>');
		endif;
		?>