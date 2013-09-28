<?php if(isset($popup_template)) { echo($popup_template); } ?>

<div class="container-fluid">
    <div class="row-fluid">
		<div class="span12">
			<div class="row-fluid">
				<div class="span7">
					<h1><?php echo lang('ls_htlm_title'); ?></h1>
				</div>
				<div class="span5" style="text-align:right;">
					<?php
					if (isset($teams) && sizeof($teams) > 0) {
						echo form_open();
						echo form_label("Select Team:", "team_id");
						echo '<select id="team_id" name="team_id">' . "\n";
						$fnd = 0;
						$minTm = 0;
						foreach ($teams as $row) {
							$tid = $row['team_id'];
							if ($minTm == 0) {
								$minTm = $tid;
							}
							$tname = $row['name'] . " " . $row['nickname'];
							$tname = str_replace(".", "", $tname);
							echo '  <option value="' . $tid . '">' . $tname . '</option>' . "\n";
							if ($fnd == 0) {
								$team_id = $minTm;
							}
						}
						echo '</select>' . "\n";
						echo form_button("submitBtn", "Go", ' id="submitBtn"');
						echo form_close();
					}
					?>
				</div>
				<!-- 2col -->
			</div>
			<div class="row-fluid">
				<div class="span6">
					<h3><?php echo lang('ls_last_sim_results'); ?></h3>
					<?php if (isset($boxscores)) {
					echo($boxscores);
				}
					?>
				</div>
				<div class="span6">
					<h3><?php echo lang('ls_upcoming_schedule'); ?></h3>
					<?php if (isset($upcoming)) {
					echo($upcoming);
				}
					?>
					<div class="clear" style="clear:both;"></div>
				</div>
				<!-- 2col -->
			</div>
			<!-- row-fluid -->
		</div>
        <!-- row-fluid -->
    </div>
    <!-- container-fluid -->
</div>