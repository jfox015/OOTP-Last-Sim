		<?php 
		switch ($player_type) {
			case 0:
				$type = 'Batters';
				$stats = array('AVG','R','H','HR','RBI','SB','OPS');
				break;
			case 1:
				$type = 'Pithers';
				$stats = array('W','L','S','IP','ERA','K','WHIP','OAVG');
				break;
		}
		?>
		<div class="tablebox">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr class="h1_l">
			<td><?php echo($type); ?></td>
		</tr>
		<tr>
			<td>
			<table class="sortable" width="100%">
			<thead>
			<tr class="hsn2">
				<td class=""hsn2_l">Player</td>
				<?php
				foreach($stats as $stat) {
					echo('\t\t<td>'.$stat.'</td>'."\n");
				}
				?>
			</tr>
			</thead>
			
			<tbody>
			<?php
			$rownum = 0;
			foreach($performers as $row) {
				$cls = $rownum % 2 + 1;
				$tid = $row['team_id'];
				?>
			<tr class="s<?php echo($cls); ?>">
				<td class="s<?php echo($cls);?>_l"><a href="<?php echo($settings['ootp.asset_url']);?>players/player_<?php echo($row['player_id']);?>.html"><?php echo($row['first_name'] . " " . $row['last_name']);?></a>, 
				<a href="<?php echo($settings['ootp.asset_url']);?>teams/team_<?php echo($tid);?>.html"><?php echo($teams[$tid]['abbr']); ?></a></td>
				<?php
				foreach($stats as $stat => $db) 
				{
					$val = $row[strtolower($stat)];
					if ($player_type == 0) 
					{
						switch ($stat) 
						{
							case 'AVG':
								$val = strstr(sprintf("%.3f",$row['avg']),".");
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
					echo('\t\t<td>'.$val.'</td>'."\n");
				}
				?>
			</tr>
			<?php
				$rownum++;
			}
			?>
			</table>
			</td>
		</tr>
		</table>
		</div>