<div class="container-fluid">
    <div id="row-fluid">
        <div class="span12">
            <h1>Last Sim -> Boxscores</h1>

            <div style="width:100%;text-align:right;">
                <p>
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
        </div>
        <div class="row-fluid">
            <div class="span6">
                <h3>Last Sim Results</h3>
                <?php if (isset($boxscores)) {
                echo($boxscores);
            }
                ?>
            </div>
            <div class="span6">
                <h3>Upcoming Schedule</h3>
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
    <!-- container-fluid -->
<?php
if (isset($scripts)) {
    echo($scripts);
}
?>