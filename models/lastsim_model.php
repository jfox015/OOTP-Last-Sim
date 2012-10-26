<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	LAST SIM MODEL CLASS.
 *
 *	@author			Jeff Fox <jfox015 (at) gmail (dot) com>
 *  @copyright   	(c)2011-12 Jeff Fox/Aeolian Digital Studios
 *	@version		0.1
 *
*/

class LastSim_model extends MY_Model {

	protected $table        = 'games';
	protected $tables       = array();
	protected $key          = 'game_id';
	protected $soft_deletes = false;
	protected $date_format  = 'datetime';
	protected $set_created  = false;
	protected $set_modified = false;
	
    protected $simLen = 7;
	
	protected $dbprefix = '';
    protected $use_prefix = false;


	//--------------------------------------------------------------------
	
	/**
	 *	C'TOR
	 *	Creates a new instance of LastSim_model
	 */
	public function __construct() {
		parent::__construct();
		// Since this model doesn't extend the base model in the open sports toolkit, we do this manually
        $this->dbprefix = $this->db->dbprefix;
        $this->use_prefix = ($this->settings_lib->item('osp.use_db_prefix') == 1) ? true : false;
    }
	
	/*----------------------------------------------------------------------
	/	PUBLIC FUNCTIONS
	/---------------------------------------------------------------------*/
	
	//--------------------------------------------------------------------
	
	/**
	 *	INIT.
	 *	Initalizes the model for queries.
	 *
	 */
	public function init($calcLength = 0, $autoSimLength = '', $simLen = false) {
		if ($autoSimLength==1) {
			$this->simLen =(($calcLength != 0) ? $calcLength : (($simLen !== false) ? $simLen : $this->simLen));
		} else {
			$this->simLen=$simLen;
		}
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET BOX SCORES.
	 *	Fetch Box scores for the given period.
	 *	@param	$lgdate		Date/time	The current league date
	 *	@param	$team_id	Int			The team to get the list of games for
	 *	@return				Array 		Array of games
	 *
	 */
	public function get_box_scores($lgdate = false, $team_id = false, $settings = false, $league_id = 100) {
		if ($lgdate === false) return false;
		$oldprefix = $this->db->dbprefix;
        $this->db->dbprefix = '';
        $boxscores = array();
		$this->db->select('game_id,played,home_team,away_team,games.date,games.time, innings,runs0,runs1,hits0,hits1,errors0,errors1,winning_pitcher,losing_pitcher,save_pitcher')
				 ->where('played',1)
				 ->where("DATEDIFF('".$lgdate."',games.date)<".$this->simLen);
                 //->limit(0,10);
		if ($team_id !== false) {
			$this->db->where("(home_team=".$team_id." OR away_team=".$team_id.")");
		}
		$query = $this->db->order_by('date,time','asc')
				 ->get($this->table);
		//print($this->db->last_query()."<br />");
        if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				$row['inningScores'] = $this->getInningScores($row['game_id'],$row['innings']);
				$row['pitcherInfo'] = $this->getPitcherInfo($row['winning_pitcher'],$row['losing_pitcher'],$row['save_pitcher']);
				$row['batterInfo'] = $this->getHitterInfo($row['game_id']);
				$row['notes'] = $this->get_notes_pitching($row['game_id'], $settings, $league_id, $this->get_notes_batting($row['game_id'], $settings, $league_id));
				array_push($boxscores,$row);
			} // END foreach
		} // END if
		$query->free_result();
        $this->db->dbprefix = $oldprefix;
        return $boxscores;
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET PLAYOFF BOX SCORES.
	 *	Fetch Box scores for the given period.
	 *	@param	$lgdate		Date/time	The current league date
	 *	@param	$team_id	Int			The team to get the list of games for
	 *	@return				Array 		Array of games
	 *
	 */
	public function get_playoff_box_scores($league_id = 100, $home_team_id = null, $away_team_id = null, $settings = false) {

		$oldprefix = $this->db->dbprefix;
        $this->db->dbprefix = '';
        $boxscores = array();
		$this->db->select('game_id,game_type,played,home_team,away_team,games.date,games.time, innings,runs0,runs1,hits0,hits1,errors0,errors1,winning_pitcher,losing_pitcher,save_pitcher')
				->where('game_type',3)
				->where('league_id',$league_id)
				->where('played',1)
				->where('((home_team='.$home_team_id.' AND away_team='.$away_team_id.') OR (home_team='.$away_team_id.' AND away_team='.$home_team_id.'))')
				->order_by('date,time','asc');
		$query = $this->db->get($this->table);
		//print($this->db->last_query()."<br />");
        if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				if ($row['game_type'] == 3) {
                    $row['inningScores'] = $this->getInningScores($row['game_id'],$row['innings']);
                    $row['pitcherInfo'] = $this->getPitcherInfo($row['winning_pitcher'],$row['losing_pitcher'],$row['save_pitcher']);
                    $row['batterInfo'] = $this->getHitterInfo($row['game_id']);
                    $row['notes'] = $this->get_notes_pitching($row['game_id'], $settings, $league_id, $this->get_notes_batting($row['game_id'], $settings, $league_id));
                    array_push($boxscores,$row);
                }
			} // END foreach
		} // END if
		$query->free_result();
        $this->db->dbprefix = $oldprefix;
        return $boxscores;
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET UPCOMING GAMES.
	 *	Retrieves a list of games that are coming up in the teams schedule.
	 *	@param	$lgdate		Date/time	The current league date
	 *	@param	$team_id	Int			The team to get the scedule for
	 *	@return				Array 		Array of games
	 *
	 */
	public function get_upcoming_games($lgdate = false, $team_id = false) {
		if ($lgdate === false) return false;
		$oldprefix = $this->db->dbprefix;
        $this->db->dbprefix = '';
        $upcoming = array();
		$this->db->select('game_id,home_team,away_team,date,time')
				 ->where('played',0)
				 ->where("DATEDIFF('".$lgdate."',games.date)<=0")
				 ->where("DATEDIFF('".$lgdate."',games.date)>-".($this->simLen-1));
                 //->limit(0,10);
		if ($team_id !== false) {
			$this->db->where("(home_team=".$team_id." OR away_team=".$team_id.")");
		}
		$this->db->order_by('date,time','asc');
		$query = $this->db->get($this->table);
		//print($this->db->last_query()."<br />");
        if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				array_push($upcoming,$row);
			} // END foreach
		} // END if
		$query->free_result();
        $this->db->dbprefix = $oldprefix;
        return $upcoming;
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET UPCOMING PLAYOFF GAMES.
	 *	Retrieves a list of playoff games that are coming up in the teams schedule.
	 *	@param	$lgdate		Date/time	The current league date
	 *	@param	$team_id	Int			The team to get the scedule for
	 *	@return				Array 		Array of games
	 *
	 */
	public function get_upcoming_playoff_games($league_id = 100, $home_team_id = null, $away_team_id = null) {

		$oldprefix = $this->db->dbprefix;
        $this->db->dbprefix = '';
        $upcoming = array();
		$this->db->select('game_id,home_team,away_team,date,time')
				->where('game_type',3)
				->where('league_id',$league_id)
				->where('played',0)
                ->where('((home_team='.$home_team_id.' AND away_team='.$away_team_id.') OR (home_team='.$away_team_id.' AND away_team='.$home_team_id.'))')
                ->order_by('date,time','asc');
		$query = $this->db->get($this->table);
		//print($this->db->last_query()."<br />");
        if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				array_push($upcoming,$row);
			} // END foreach
		} // END if
		$query->free_result();
        $this->db->dbprefix = $oldprefix;
        return $upcoming;
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET SITUATIONAL SCORING.
	 *	Retrieves a list of situatioanl records for the specified team.
	 *	@param	$team_id	Int			The team to get the scedule for
	 *	@param	$league_id	Int			The league ID
	 *	@return				Array 		Array of games
	 *
	 */
	public function get_situational_scoring($team_id = false, $league_id = 100, $team_scores = false) {

		if ($team_id === false) return false;
		
		$oldprefix = $this->db->dbprefix;
        $this->db->dbprefix = '';
        if ($team_scores === false) $team_scores = array();
		
		$this->db->select('game_id,home_team,away_team,runs0,runs1')
				 ->where('game_type',0)
				 ->where('league_id',$league_id)
				 ->where('played',1);
                 //->limit(0,10);
		if ($team_id !== false) {
			$this->db->where("(home_team=".$team_id." OR away_team=".$team_id.")");
		}
		$query = $this->db->get($this->table);
		//print($this->db->last_query()."<br />");
        if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				$hid=$row['home_team'];
				$aid=$row['away_team'];
				if ($row['runs0']>$row['runs1'])
				{
					$team_scores[$aid]['w']=(isset($team_scores[$aid]['w'])) ? $team_scores[$aid]['w']+1 : 1;
					$team_scores[$aid]['rw']=(isset($team_scores[$aid]['rw'])) ? $team_scores[$aid]['rw']+1 : 1;
					$team_scores[$hid]['l']=(isset($team_scores[$hid]['l'])) ? $team_scores[$hid]['l']+1 : 1;
					$team_scores[$hid]['hl']=(isset($team_scores[$hid]['hl'])) ? $team_scores[$hid]['hl']+1 : 1;
					$team_scores[$aid]['wVs'][$hid]=(isset($team_scores[$aid]['wVs'][$hid])) ? $team_scores[$aid]['wVs'][$hid]+1 : 1;
				}
				else
				{
					$team_scores[$aid]['l']=(isset($team_scores[$aid]['l'])) ? $team_scores[$aid]['l']+1 : 1;
					$team_scores[$aid]['rl']=(isset($team_scores[$aid]['rl'])) ? $team_scores[$aid]['rl']+1 : 1;
					$team_scores[$hid]['w']=(isset($team_scores[$hid]['w'])) ? $team_scores[$hid]['w']+1 : 1;
					$team_scores[$hid]['hw']=(isset($team_scores[$hid]['hw'])) ? $team_scores[$hid]['hw']+1 : 1;
					$team_scores[$hid]['wVs'][$aid]=(isset($team_scores[$hid]['wVs'][$aid])) ? $team_scores[$hid]['wVs'][$aid]+1 : 1;
				}
			}
		}
		$query->free_result();
        $this->db->dbprefix = $oldprefix;
        return $team_scores;
	}

	//--------------------------------------------------------------------
	
	/**
	 * GET NOTES BATTING.
	 *
	 * Queries for a set of top offensive performances from a specified list of games for a league..
	 *
	 * @param	int		$league_id			League Id, 100 if not specified
	 * @param	String	$gidList			Comma seperated list of game IDs
	 * @return	Array						Ammended games array
	 * @auuthor	Frank Esselink
	 */
    public function get_notes_batting($game_id = false, $settings = false, $league_id = 100, $notes = false)
	{
		
        if ($notes === false) { $notes = ''; }
		if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->select('players_game_batting.player_id,game_id,first_name,last_name,h,d,t,hr,rbi,sb')
            ->join('players', 'players.player_id = players_game_batting.player_id','left')
            ->where('(hr>2 OR h>5 OR rbi>7 or sb>3 OR ((h-d-t-hr)>0 AND d>0 AND t>0 AND hr>0))')
            ->where('players_game_batting.league_id', $league_id)
            ->where('players_game_batting.game_id', $game_id);
        $query = $this->db->get('players_game_batting');
        if ($query->num_rows() > 0) {
            
			$row = $query->row_array();
                
			$gid = $row['game_id'];
			$pid = $row['player_id'];
			$fi = $row['first_name'];
			$fi = $fi[0];
			$name = $fi . ". " . $row['last_name'];
			$h = $row['h'];
			$d = $row['d'];
			$t = $row['t'];
			$hr = $row['hr'];
			$s = $h - $d - $t - $hr;
			$rbi = $row['rbi'];
			$sb = $row['sb'];

			if ($hr > 2) {
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> belts $hr HR\'s';
			}
			if ($h > 5) {
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> smacks $h hits';
			}
			if ($rbi > 7) {
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> drives in $rbi';
			}
			if ($sb > 3) {
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> steals $sb bases';
			}
			if (($s > 0) && ($d > 0) && ($t > 0) && ($hr > 0)) {
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> hits for the cycle!';
			}
        }
        $query->free_result();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $notes;
	}

	//--------------------------------------------------------------------
	
	/**
	 * GET NOTES Pitching.
	 *
	 * Queries for a set of top pitching performances from a specified list of games for a league..
	 *
	 * @param	int		$league_id			League Id, 100 if not specified
	 * @param	String	$gidList			Comma seperated list of game IDs
	 * @return	Array						Ammended games array
	 * @auuthor	Frank Esselink
	 */
    public function get_notes_pitching($game_id = false, $settings = false, $league_id = 100)
	{
		
        $notes = '';
		if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->select('players_game_pitching_stats.player_id,game_id,first_name,last_name,k,(ip*3+ipf)/3 as ip,ha,cg,sho')
            ->join('players', 'players.player_id = players_game_pitching_stats.player_id','left')
            ->where('(k>14 OR ((ip*3+ipf)/3)>9 OR (ha=0 AND ((ip*3+ipf)/3)>7) OR (ha<3 AND cg=1 AND sho=1))')
            ->where('players_game_pitching_stats.league_id', $league_id)
            ->where('players_game_pitching_stats.game_id', $game_id);
        $query = $this->db->get('players_game_pitching_stats');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();

			$gid = $row['game_id'];
			$pid = $row['player_id'];
			$fi = $row['first_name'];
			$fi = $fi[0];
			$name = $fi . ". " . $row['last_name'];
			$k = $row['k'];
			$ip = $row['ip'];
			$ha = $row['ha'];
			$cg = $row['cg'];
			$sho = $row['sho'];
			if (floor($ip) == $ip) 
			{
				$dispIP = round($ip, 0);
			} else {
				$dispIP = round(floor($ip), 0) . " " . round((3 * ($ip - floor($ip))), 0) . "/3";
			}

			if ($k > 14) 
			{
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> strikes out '.$k;
			}
			if ($ip > 9) {
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> goes '.$dispIP.' innings';
			}
			if (($ha == 0) && ($ip > 7)) {
				if ($cg == 1) {
					if (!empty($notes)) { $notes .= ", "; }
					$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> pitches a no-hitter';
				}
				else {
					if (!empty($notes)) { $notes .= ", "; }
					$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> fails to allow a hit';
				}
			}
			if (($ha < 3) && ($cg == 1) && ($sho == 1) && ($ha != 0)) {
				if (!empty($notes)) { $notes .= ", "; }
				$notes .= '<a href="'.$settings['osp.asset_url'].'players/player_'.$pid.'.html">'.$name.'</a> pitches a '.$ha.'-hit shutout';
			}
        }
        $query->free_result();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $notes;
	}

	//--------------------------------------------------------------------
	
	/**
	 * GET TEAM SITUATIONAL RECORDS.
	 *
	 * Queries for the home and road w/l breakdown for the selected teams.
	 *
	 * @param	int		$league_id			League Id, 100 if not specified
	 * @param	Array	$teams				Array of team ifnormation
	 * @param	int		$home_team_id		Home team ID
	 * @param	int		$away_team_id		Away team ID
	 * @return	Array						Ammended teams array
	 * @auuthor	Frank Esselink
	 */
    public function get_team_situational_records($league_id = 100, $teams = false, $home_team_id = -1, $away_team_id = -1) 
	{
		
		if ($teams !== false && is_array($teams) && count($teams)) {
		
			if (!$this->use_prefix) $this->db->dbprefix = '';
			$this->db->select('game_id,home_team,away_team,runs0,runs1')
				->where('league_id', $league_id)
				->where('game_type', 0)
				->where('played', 1)
				->where('(home_team = '.$home_team_id.' OR home_team = '.$away_team_id.' OR away_team = '.$away_team_id.' OR away_team = '.$home_team_id.')');
			$query = $this->db->get('games');
			if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row) {
					$hid = $row['home_team'];
					$aid = $row['away_team'];
					if ($row['runs0'] > $row['runs1']) {
						$teams[$aid]['rw'] = isset($teams[$aid]['rw']) ? $teams[$aid]['rw'] + 1 : 1;
						$teams[$hid]['hl'] = isset($teams[$hid]['hl']) ? $teams[$hid]['hl'] + 1 : 1;
					}
					else {
						$teams[$aid]['rl'] = isset($teams[$aid]['rl']) ? $teams[$aid]['rl'] + 1 : 1;
						$teams[$hid]['hw'] = isset($teams[$hid]['hw']) ? $teams[$hid]['hw'] + 1 : 1;
					}
				}
			}
			$query->free_result();
			if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
		}
		return $teams;
	}

	//--------------------------------------------------------------------
	
	/**
	 * GET TOP BATTERS BY GAME LIST.
	 *
	 * Queries for top offensive performances for a specified list of game IDs. This i
	 * function calls to get_top_performances(0 to return the players and data.
	 *
	 * @param	Array	$gidList			Array of game IDs
	 * @param	int		$pcnt				Games played Count
	 * @return	Array						Array of players stats and meta data
	 *
	 * @see 	lastSim_model::get_top_performances()
	 */
    public function get_top_batters_by_gamelist($gidList = false, $pcnt = false)
	{
		$performers = array();
		if ($gidList !== false) 
		{
			$performers = $this->get_top_performances(0, RANGE_GAME_ID_LIST, $gidList, false, $pcnt);
		}
		return $performers;
	}

	//--------------------------------------------------------------------
	
	/**
	 * GET TOP PITCHERSS BY GAME LIST.
	 *
	 * Queries for top pitching performances for a specified list of game IDs. This i
	 * function calls to get_top_performances(0 to return the players and data.
	 *
	 * @param	Array	$gidList			Array of game IDs
	 * @param	int		$pcnt				Games played Count
	 * @return	Array						Array of players stats and meta data
	 *
	 * @see 	lastSim_model::get_top_performances()
	 */
   public function get_top_pitchers_by_gamelist($gidList = false, $pcnt = false)
	{
		$performers = array();
		if ($gidList !== false) 
		{
			$performers = $this->get_top_performances(1, RANGE_GAME_ID_LIST, $gidList, false, $pcnt);
		}
		return $performers;
	}
			
	/*----------------------------------------------------------------------
	/	PROTECTED FUNCTIONS
	/---------------------------------------------------------------------*/

	//--------------------------------------------------------------------
	
	/**
	 * GET TOP PERFORMERS.
	 *
	 * Queries for the top players for the given range. Each player gets a score and are ranked according
	 * to that score.
	 *
	 * @param	int			$player_type		0 for batter, 1 for pitcher
	 * @param	int			$rangeType			Range constant (RANGE_GAME_ID_LIST,RANGE_DATE_LIST,RANGE_TEAM_LIST,RANGE_PLAYER_LIST,RANGE_SEASON,RANGE_CAREER)
	 * @param	varchar		$rangeVal0			First range value
	 * @param	varchar		$rangeVal1			(OPTIONAL) second range var (an end date for example)
	 * @param	int			$pcnt				(OPTIONAL) Games played Count (default is 0)
	 * @param	int			$limit				(OPTIONAL) SQL Limit override (default is 5)
	 * @param	int			$offset				(OPTIONAL) SQL Record offset index override (default is 0)
	 * @return	Array							Array of players stats and meta data
	 * 
	 * @auuthor	Frank Esselink
	 * @auuthor	Jeff Fox
	 */
    protected function get_top_performances($player_type = 0, $rangeType = false, $rangeVal0 = false, $rangeVal1 = false, $pcnt = false, $limit = 5, $offset = 0)
	{
		
		$performers = array();
		if ($pcnt === false) $pcnt = 0;
		switch ($player_type) {
			case 0:
				$select = 'players.player_id,players.first_name,players.last_name,players.team_id,sum(h) as h,sum(hr) as hr,sum(rbi) as rbi,sum(r) as r,sum(sb) as sb,(sum(h)/sum(ab)) as avg,(sum(h)+sum(bb)+sum(hp))/(sum(ab)+sum(bb)+sum(hp)+sum(sf)) as obp,(sum(h)+sum(d)+2*sum(t)+3*sum(hr))/sum(ab) as slg,if(SUM(pa)<(2*'.$pcnt.'),-99,(0.47*(sum(h)-sum(d)-sum(t)-sum(hr)) + .78*sum(d) + 1.09*sum(t) + 1.4*sum(hr) + .33*(sum(bb)-sum(hp)) + .3*sum(sb) + .5*(-.52*sum(cs) - .26*(sum(ab)-sum(h)-sum(gdp)) - .72*sum(gdp)))) as lw';
				$table = 'players_game_batting';
				$order_by = 'lw';
				$test_field = 'ab';
				break;
			case 1:
				$select = 'game_id,players.player_id,players.first_name,players.last_name,players.team_id,((SUM(ip)*3+SUM(ipf))/3) as ip,sum(w) as w,sum(l) as l,sum(s) as s,sum(k) as k,9*sum(er)/((SUM(ip)*3+SUM(ipf))/3) as era,(sum(bb)+sum(ha)+sum(hp))/((SUM(ip)*3+SUM(ipf))/3) as whip,sum(ha)/sum(ab) as oavg,(((sum(ha)+sum(bb)+sum(hp))*(0.89*(1.255*(sum(ha)-sum(hra))+4*sum(hra))+0.56*(sum(bb)+sum(hp)-sum(iw))))/(sum(bf)*((SUM(ip)*3+SUM(ipf))/3)))*9*0.75 as erc,';
				$select .= 'if(SUM(ip)<('.$pcnt.'-1),-99,3*((SUM(ip)*3+SUM(ipf))/3)+4*sum(w)-4*sum(l)+5*sum(s)+sum(k)+.5*(-2*sum(ha)-2*sum(bb))) as score';
				$table = 'players_game_pitching_stats';
				$order_by = 'score';
				$test_field = 'ip';
				break;
		}
		if ($rangeType !== false && $rangeVal0 !== false) 
		{
			if (!$this->use_prefix) $this->db->dbprefix = '';
			$this->db->select($select, false)
					 ->join('players', 'players.player_id = '.$table.'.player_id','right outer');
			switch ($rangeType) {
				case RANGE_GAME_ID_LIST:
					$gidStr = '';
					if (is_array($rangeVal0) && count($rangeVal0)) 
					{
						foreach ($rangeVal0 as $id) 
						{
							if (!empty($gidStr)) { $gidStr .= ' OR '; }
							$gidStr .= 'game_id = '.$id;
						} // END foreach
					} // END if
					$this->db->where('('.$gidStr.')');
					break;
				case RANGE_DATE_LIST:
					$this->db->where("DATEDIFF('".$rangeVal0."',games.date)<=0")
							 ->where("DATEDIFF('".$rangeVal0."',games.date)>-".($this->simLen-1));
					break;
				case RANGE_TEAM_LIST:
					$this->db->where_in('players.team_id', $rangeVal0);
					break;
			} // END switch
			$this->db->where($table.".".$test_field.' > 0');
			$this->db->group_by('player_id')
					 ->order_by($order_by, 'desc');
			// LIMITS AND OFFSET
			if (isset($limit) && isset($offset))
			{
				if ($limit != -1 && $offset == 0)
				{
					$this->limit($limit);
				}
				else if ($limit != -1 && $offset > 0)
				{
					$this->db->limit($offset,$limit);
				} // END if
			} // END if
			
			$query = $this->db->get($table);
            //print($this->db->last_query()."<br />");
            if ($query->num_rows() > 0) {
				$performers = $query->result_array();
			} // END if
			$query->free_result();
			if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix; // END if
		} // END if $rangeType !== false
		return $performers;
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET INNING SCORES.
	 *	Fetch inning scores for the given period.
	 *
	 */
	protected function getInningScores($game_id = false, $innings = false) {
		if ($game_id === false) return false;
		$inningscores = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
			$query = $this->db->select('team,inning,score')
				 ->where('game_id',$game_id)
				 ->where("inning > ".($innings-12))
				 ->order_by('team,inning','asc')
				 ->get('games_score');
				 
		if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				array_push($inningscores,$row);
			} // END foreach
		} // END if
		$query->free_result();
		if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
		return $inningscores;
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET PITCHER INFO.
	 *	Fetch  pitcher information based on winner, loser and save stats.
	 *
	 */
	protected function getPitcherInfo($winner = false, $loser = false, $save = false) {
		$pitcherStats = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
			$query = $this->db->select('player_id,first_name,last_name')
				 ->where('player_id',$winner)
				 ->or_where("player_id",$loser);
		if ($save !== false && $save > 0) {
			$this->db->or_where("player_id",$save);
		}
		$query = $this->db->get('players');
        //print($this->db->last_query()."<br />");
        if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				if ($row['player_id'] == $winner) {
					$pitcherStats['wp'] = $row;
				} else if ($row['player_id'] == $loser) {
					$pitcherStats['lp'] = $row;
				} else if ($save !== false && $row['player_id'] == $save) {
					$pitcherStats['sv'] = $row;
				} 
			} // END foreach
		} // END if
		$query->free_result();
		if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
		return $pitcherStats;
	}

	//--------------------------------------------------------------------
	
	/**
	 *	GET HITTER INFO.
	 *	Fetch  hitter information bfor hitters with HRs.
	 *
	 */
	protected function getHitterInfo($game_id = false) {
		if ($game_id === false) return false;
		$batterStats = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
		$this->db->select('players.player_id,players.team_id,players_game_batting.hr,players.first_name,players.last_name')
				 ->join('players_game_batting','players.player_id = players_game_batting.player_id','right outer')
				 ->where('game_id',$game_id);
	    $query = $this->db->get('players');
        //print($this->db->last_query()."<br />");
        if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				array_push($batterStats,$row);
			} // END foreach
		} // END if
		$query->free_result();
		if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
		return $batterStats;
	}
}