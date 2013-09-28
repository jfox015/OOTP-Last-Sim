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
		$this->load->model('open_sports_toolkit/Teams_model', 'teams_model');
		$this->load->model('open_sports_toolkit/Leagues_model', 'leagues_model');
		
		$this->lang->load('lastsim');
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
			$league_id = $settings['osp.league_id'];
		}
		$teams = $this->teams_model->get_teams_array($league_id);
		Template::set('teams',$teams);
		Assets::add_js($this->load->view('lastsim/boxscores_js',null,true),'inline');
		if (isset($team_id) && !empty($team_id) && $team_id !== NULL) {
			$league = $this->leagues_model->find($league_id);
            if (isset($league) && $league->league_id != NULL) {
				$this->sim_model->init($settings['osp.calc_length'],$settings['osp.auto_sim_length'],(isset($settings['osp.sim_length']) && !empty($settings['osp.sim_length']) ? $settings['osp.sim_length'] : false));
                $this->load->helper('open_sports_toolkit/general');
                // ASSURE PATH COMPLIANCE TO OOPT VERSION
                $settings = get_asset_path($settings);

                if (in_array('players',module_list(true)))
				{
					modules::run('players/player_link_init');
					$this->load->helper('players/players');
				}
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
			}	
		}
		if (!function_exists('form_open')) {
            $this->load->helper('form');
        }
		Assets::add_module_css('lastsim','box_styles.css');
                
		Template::render();
	}
}