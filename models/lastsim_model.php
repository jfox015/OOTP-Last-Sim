<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class LastSim_model extends MY_Model {

	protected $table        = 'games';
	protected $tables       = array();
	protected $key          = 'game_id';
	protected $soft_deletes = false;
	protected $date_format  = 'datetime';
	protected $set_created  = false;
	protected $set_modified = false;

    protected $simLen = 1;
	/**
	 *	C'TOR
	 *	Creates a new instance of LastSim_model
	 */
	public function __construct() {
		parent::__construct();
	}
	/**
	 *	INIT.
	 *	Initalizes the model for queries.
	 *
	 */
	public function init($calcLength = 0, $autoSimLength = '', $simLen = 7) {
		if ($autoSimLength==1) {
			$this->simLen=(($calcLength != 0) ? $calcLength : $simLen);
		} else {
			$this->simLen=$simLen;
		}
	}
	/**
	 *	GET BOX SCORES.
	 *	Fetch Box scores for the given period.
	 *
	 */
	public function get_box_scores($lgdate = false, $team_id = false) {
		if ($lgdate === false) return false;
		$oldprefix = $this->db->dbprefix;
        $this->db->dbprefix = '';
        $boxscores = array();
		$this->db->select('game_id,played,home_team,away_team,games.date,innings,runs0,runs1,hits0,hits1,errors0,errors1,winning_pitcher,losing_pitcher,save_pitcher')
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
				array_push($boxscores,$row);
			} // END foreach
		} // END if
		$query->free_result();
        $this->db->dbprefix = $oldprefix;
        return $boxscores;
	}
	/**
	 *	GET INNING SCORES.
	 *	Fetch inning scores for the given period.
	 *
	 */
	protected function getInningScores($game_id = false, $innings = false) {
		if ($game_id === false) return false;
		$inningscores = array();
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
		return $inningscores;
	}
	/**
	 *	GET PITCHER INFO.
	 *	Fetch  pitcher information based on winner, loser and save stats.
	 *
	 */
	protected function getPitcherInfo($winner = false, $loser = false, $save = false) {
		$pitcherStats = array();
		$query = $this->db->select('player_id,first_name,last_name')
				 ->where('player_id',$winner)
				 ->or_where("player_id",$loser);
		if ($save !== false && $save > 0) {
			$this->db->or_where("player_id",$save);
		}
		$query = $this->db->get('players');
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
		return $pitcherStats;
	}
	/**
	 *	GET HITTER INFO.
	 *	Fetch  hitter information bfor hitters with HRs.
	 *
	 */
	protected function getHitterInfo($game_id = false) {
		if ($game_id === false) return false;
		$batterStats = array();
		$query = $this->db->select('players.player_id,players.team_id,players_game_batting.hr,players.first_name,players.last_name')
				 ->join('players_game_batting','players.player_id = players_game_batting.player_id','right outer')
				 ->where('game_id',$game_id)
				 ->get('players');
		if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				array_push($batterStats,$row);
			} // END foreach
		} // END if
		$query->free_result();
		return $batterStats;
	}
	
	
}