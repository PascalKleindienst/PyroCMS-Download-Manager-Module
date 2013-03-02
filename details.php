<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Download Manager Module Details
 *
 * @package		PyroCMS
 * @subpackage	Modules
 * @author		Pascal Kleindienst
 * @copyright	Copyright (c) 2013, Pascal Kleindienst
 * @link		http://www.pascalkleindienst.de
 * @license		LGPLv3
 */
class Module_Download_Manager extends Module
{
	public $version = '0.1';
	
	/**
	 * Info about the module
	 * @access public
	 * @return array
	 */
	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Download Manager',
				'de' => 'Download Manager'
			),
			'description' => array(
				'en' => 'A simple download manager',
				'de' => 'Ein einfacher Download Manager'
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content',
			'sections' => array(
				'download_manager' => array(
					'name' => 	'dm_admin.title',
				    'uri' => 	'admin/download_manager',
				    'shortcuts' => array(
						'create' => array(
 							'name' => 'dm_admin.create_button',
							'uri' => 'admin/download_manager/create',
							'class' => 'add'
						)
					)	
				) 
			)
		);
	}
	
	/**
	 * Install routine
	 * @access public
	 * @return bool
	 */
	public function install()
	{
		$this->dbforge->drop_table('pk_download_manager');
		$this->dbforge->drop_table('pk_download_manager_settings');
		$this->db->delete('settings', array('module' => 'download_manager'));
		
		$download_manager = array(
			'id' => array(
				'type' => 'MEDIUMINT',
				'constraint' => 8,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
				'primary' => TRUE
			),
			'downloads' => array(
				'type' => 'MEDIUMINT',
				'constraint' => 8,
				'unsigned' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 50
			),
			'slug' => array(
				'type' => 'VARCHAR',
				'constraint' => 50
			),
			'status' => array(
				'type' => 'TINYINT',
				'constraint' => 1
			),
			'login' => array(
				'type' => 'TINYINT',
				'constraint' => 1
			),
			'type' => array(
				'type' => 'TINYINT',
				'constraint' => '1'
			),
			'file' => array(
				'type' => 'VARCHAR',
				'constraint' => 200
			)
		);
		
		$this->dbforge->add_field($download_manager);
		$this->dbforge->add_key('id',TRUE);
		$this->dbforge->create_table('pk_download_manager',TRUE);
		
		// settings
		$this->load->library('files/files');
		$folder = Files::create_folder(0, 'Download Manager');
		$settings = array(
			'folder' => array(
				'type' => 'TINYINT',
				'constraint' => 1
			)
		);
		
		$this->dbforge->add_field($settings);
		$this->dbforge->create_table('pk_download_manager_settings', TRUE);
		
		if($folder['status'])
			$this->db->insert('pk_download_manager_settings', array('folder' => $folder['data']['id']));
			
		return TRUE;
	}
	
	/**
	 * Uninstall routine
	 * @access public
	 * @return bool
	 */
	public function uninstall()
	{
		$this->dbforge->drop_table('pk_download_manager');
		$this->dbforge->drop_table('pk_download_manager_settings');
		return TRUE;
	}
	
	/**
	 * Upgrade routine
	 * @access public
	 * @param 
	 * @return bool
	 */
	public function upgrade($old_version)
	{
		// Upgrade Logic
		return TRUE;
	}
}