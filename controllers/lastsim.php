<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	Module:: Last sim
 *	Displays functions that deal with the most recent sim data.
 *
 */
class LastSim extends Front_Controller {

	var $teams = array();
	/**
	 *	__construct.
	 *	Builds a new instance of the LastSim module.
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('LastSim_model', 'sim_model');
		$this->load->model('ootp_web_toolkit/Teams_model', 'teams_model');
		$this->load->model('ootp_web_toolkit/Leagues_model', 'leagues_model');
	}
	/**
	 *	INDEX.
	 *	Draws a select box to choose a team to view boxscores.
	 *
	 */
	public function index() {
		redirect('/lastsim/boxscores/');
	}
	/**
	 *	BOXSCORES.
	 *	Retrieves and displays boxscores for a particular team for a period specified 
	 *	int he main league sim settings.
	 *	@param	$league_id	int 	League ID
	 *	@param	$team_id	int 	Team ID
	 *
	 */
	public function boxscores() {

        $settings = $this->settings_lib->find_all();
        $team_id = $this->uri->segment(3);
		$league_id = $this->uri->segment(4);
		if (!isset($league_id) || empty($league_id) || $league_id == -1) {
			$league_id = $settings['ootp.league_id'];
		}
		if (isset($team_id) && !empty($team_id) && $team_id !== NULL) {
			$league = $this->leagues_model->find($league_id);
            if (isset($league) && $league->league_id != NULL) {
				$this->sim_model->init($settings['ootp.calc_length'],$settings['ootp.auto_sim_length'],(isset($settings['ootp.sim_length']) && !empty($settings['ootp.sim_length']) ? $settings['ootp.sim_length'] : false));
                // ASSURE PATH COMPLIANCE TO OOPT VERSION
                $this->load->helper('open_sports_toolkit/general');
                $settings = get_asset_path($settings);

                $teams = $this->teams_model->get_teams_array($league_id);

                $data = array();
				$data['boxscores'] = $this->sim_model->get_box_scores($league->current_date,$team_id, $settings, $league_id);
				$data['gamecast_links'] = in_array('gamecast',module_list(true));
				$data['settings'] = $settings;
				$data['teams'] = $teams;
				Template::set('boxscores',$this->load->view('lastsim/loop_boxscores',$data,true));
				unset($data);
				
				$data = array();
                $data['settings'] = $settings;
                $data['teams'] = $teams;
                $data['team_scores'] = $this->sim_model->get_situational_scoring($team_id,$league->league_id);
				$data['upcoming'] = $this->sim_model->get_upcoming_games($league->current_date,$team_id, $settings);
				Template::set('upcoming',$this->load->view('lastsim/loop_upcoming',$data,true));
				
				Template::set('scripts',$this->load->view('lastsim/boxscores_js',null,true));
			}	
		}
		if (!function_exists('form_open')) {
            $this->load->helper('form');
        }

		Template::render();
	}
}