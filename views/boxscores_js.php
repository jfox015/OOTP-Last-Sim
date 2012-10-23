<script type="text/javascript">
    head.ready(function() {
        
		<?php if (isset($gamecast_links) && $gamecast_links) { ?>
		// Gamecast JS code
		var gamerunning = null;
		$('a[rel=replay]').click(function(e) {
			e.preventDefault();
			var proceed = true,
			args = this.id.split("_"), // index 0 = box count id, 1 = gamecast game id
			gc_link = '<?php echo site_url() ?>/gamecast/index/'+args[2];
			if (gamerunning) {
				proceed = confirm("There is already a game replay running. If you choose to continue, the previous game will be stopped and a new replay will be started. Do you wish to proceed?");
			}
			if (proceed) {
				if (gamerunning) {
				if (args[0] == "1") { // INLINE
						$('#ifrm_'+gamerunning).remove();
						$('#box_'+gamerunning).css('display','block');
						$('#gc_'+gamerunning).css('display','none');
						gamerunning = null;
					}
					$('#gc_'+args[1]).append('<iframe id="ifrm_'+args[1]+'" style="width:470px;height:218px;border=none;" scrolling="no" />');
					$('#ifrm_'+args[1]).attr('src', gc_link+'/2'); 
					$('#box_'+args[1]).css('display','none');
					$('#gc_'+args[1]).css('display','block');
				} else {
					var gcWin = window.open(gc_link+'/1','gamecast'+args[2],'width=465,height=550,scrolling=no,menu=no,status=yes,top=50,left=50,location=no,resize=yes');
				}
				gamerunning = args[2];
			}
		});
        <?php } ?>
		$('#submitBtn').click(function(e) {
            e.preventDefault();
            document.location.href="<?php echo(site_url()); ?>/lastsim/boxscores/"+$('#team_id').val();
        });
    });
</script>