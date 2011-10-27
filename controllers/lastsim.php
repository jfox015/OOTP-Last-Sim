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
		$this->getURIData();
		$this->load->model('ootp_dashboard/OOTPData_model','ootpdata_model');
		$this->teams = $this->ootpdata_model->getTeams($this->uriVars['league_id']);
		$this->load->model('lastsim/LastSim_model', 'lastsim_model');
	}
	/**
	 *	INDEX.
	 *	Draws a select box to choose a team to view boxscores.
	 *
	 */
	public function index() {
		Template::set('teams',$this->teams);
		Template::render();
	}
	/**
	 *	BOXSCORES.
	 *	Retirves and displays boxscores for a particular team for a given period.
	 *
	 */
	public function boxscores() {
        if (!isset($this->uriVars['team_id'])) {
			$this->index();
		} else {
			$this->config = read_config('last_sim');
			$this->lastsim_model->init($this->config['calcLength'],$this->config['autoSimLength'],$this->config['simLength']);

			$league_date = $this->ootpdata_model->getLeagueDate($this->uriVars['league_id']);
			Template::set('teams',$this->teams);
			Template::set('boxscores',$this->lastsim_model->getBoxScores($league_date,$this->uriVars['team_id']));
			Template::render();
		}
	}
	protected function getUriData() {
		parent::getUriData();
        $this->uriVars['league_id'] = 100;
	}
}