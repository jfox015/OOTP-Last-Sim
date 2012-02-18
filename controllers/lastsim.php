<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	Module:: Last sim
 *	Displays functions that deal with the most recent sim data
 *
 */
class LastSim extends Front_Controller {

	var $teams = array();
	/**
	 *	C'TOR.
	 *	Builds a new instance of LastSim.
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('LastSim_model', 'sim_model');
		$this->load->model('league_manager/Teams_model', 'teams_model');
		$this->load->model('league_manager/Leagues_model', 'leagues_model');
	}
	/**
	 *	INDEX.
	 *	Draws a select box to choose a team to view boxscores.
	 *
	 */
	public function index() {
		$league_id = $this->uri->segment(4);
		Template::set('teams',$this->teams_model->get_teams_array($league_id));
		Template::render();
	}
	/**
	 *	BOXSCORES.
	 *	Retirves and displays boxscores for a particular team for a given period.
	 *
	 */
	public function boxscores() {
        $league_id = $this->uri->segment(4);
		$team_id = $this->uri->segment(5);
		if (isset($team_id) || $team_id !== NULL) {
			$league = $this->leagues_model->find($league_id);
			if (isset($league) && $league->league_id != NULL) {
				$sim_config = read_config('league_manager');
				$this->sim_model->init($sim_config['auto_sim_length'],$sim_config['calc_length'],$sim_config['sim_length']);
				Template::set('boxscores',$this->sim_model->get_box_scores($league->current_date,$team_id));
			}	
		}
		Template::set('teams',$this->teams_model->get_teams_array($league_id));
		Template::set_view('last_sim/index');
		Template::render();
	}
}