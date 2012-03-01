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
		$this->load->model('league_manager/Teams_model', 'teams_model');
		$this->load->model('league_manager/Leagues_model', 'leagues_model');
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
				$this->sim_model->init($settings['ootp.auto_sim_length'],$settings['ootp.calc_length'],$settings['ootp.sim_length']);
				Template::set('boxscores',$this->sim_model->get_box_scores($league->current_date,$team_id));
				Template::set('upcoming',$this->sim_model->get_upcoming_games($league->current_date,$team_id));
				Template::set('team_scores',$this->sim_model->get_situational_scoring($team_id,$league->league_id));
				// TEST if Gamecast module is installed. Set link display var to return TRUWE or FALSE based on test
				$gamecast = false;
                $modules = module_list(true);
                foreach ($modules as $module) {
                    if ($module == 'gamecast') {
                        $gamecast = true;
                        break;
                    }
                }
                //$gamecast = @Modules::find('config/config.php','gamecast',modu) ? true : false)
                Template::set('gamecast_links',$gamecast);
			}	
		}
		if (!function_exists('form_open')) {
            $this->load->helper('form');
        }
        Template::set('settings',$settings);
		Template::set('teams',$this->teams_model->get_teams_array($league_id));
		Template::render();
	}
}