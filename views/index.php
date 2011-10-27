
<div id="content">
	<div id="main">
	<h1>Last Sim -> Boxscores</h1>
	<p>
	<?php if (isset($teams) && sizeof($teams) > 0) {
		$text="<div class='textbox'>\n";
		$text.=" <table cellpadding=2 cellspacing=0 border=0>\n";
		$text.="  <tr class='title'><td colspan=3>Team Results</td></tr>\n";
		$text.="  <tr>\n";
		$text.="   <td>\n";
		$text.="<form method='get' action='' class='inline'>\n";
		$text.=' <label for="team_id">Select Team:</label>';
		$text.=" <select id='team_id' name='team_id'>\n";
		$fnd=0;
		$minTm=0;
		foreach($teams as $row) {
			$tid=$row['team_id'];
			if ($minTm==0) {$minTm=$tid;}
			$tname=$row['name']." ".$row['nickname'];
			$tname=str_replace(".","",$tname);
			$text.='  <option value="'.$tid.'">'.$tname.'</option>\n';
			if ($fnd==0) {$team_id=$minTm;}
		}
		$text.=" </select>\n";
		$text.="    <input type='button' id='submitBtn' value='Go' style='float:left;' />\n";
		$text.="   </td>\n";
		$text.="   <td>&nbsp;</td>\n";
		$text.="   <td width='400px'>&nbsp;</td>\n";
		$text.="  </tr>\n";
                $text.="  </table>\n";
		$text.="</form>\n";
                $text.="</div>\n";
		print($text);
	}
	?>
	</div>
</div>
<script type="text/javascript">
head.ready(function() {
    $('#submitBtn').click(function(e) {
        e.preventDefault();
        document.location.href="/bonfire/lastsim/boxscores/league_date/2011_05_01/team_id/"+$('#team_id').val();
    });
});
</script>