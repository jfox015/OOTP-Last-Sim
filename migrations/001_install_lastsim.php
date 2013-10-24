<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_lastsim extends Migration {
	
	public function up() 
	{
		$prefix = $this->db->dbprefix;

        if ($this->db->table_exists('navigation')) 
		{
			$query = $this->db->query("SELECT nav_group_id FROM {$prefix}navigation_group where title = 'header_nav'");
			if ($query->num_rows() > 0) 
			{
				$row = $query->row();
				$nav_group_id = $row->nav_group_id;
				$data = array('nav_id'=>0,
					  'title'=>'Last Sim',
					  'url'=>'/lastsim',
					  'nav_group_id'=>$nav_group_id,
					  'position'=>1,
					  'parent_id'=>0,
					  'has_kids'=>0);
				$this->db->insert("{$prefix}navigation",$data);
			}
			$query->free_result();
		}
    }
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
		$prefix = $this->db->dbprefix;
		//delete the nav item
		$this->db->query("DELETE FROM {$prefix}navigation WHERE (title = 'Last Sim')");

    }
	
	//--------------------------------------------------------------------
	
}