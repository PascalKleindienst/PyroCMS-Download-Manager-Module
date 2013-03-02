<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Download Manager Admin Controller
 *
 * @package		PyroCMS
 * @subpackage	Modules
 * @author		Pascal Kleindienst
 * @copyright	Copyright (c) 2013, Pascal Kleindienst
 * @link		http://www.pascalkleindienst.de
 * @license		LGPLv3
 */
class Admin extends Admin_Controller 
{
	// the section
    protected $section = "download_manager";
	
	/**
	 * @var array $validation rules - Some validation rules
	 * @access protected
	 */
	protected $validation_rules = array(	
		'download_name' => array(
			'field' => 'download_name',
			'label' => 'lang:dm_admin.name_label',
			'rules' => 'trim|htmlspecialchars|required|max_length[50]'
		),
		'download_slug' => array(
			'field' => 'download_slug',
			'label' => 'lang:dm_admin.slug_label',
			'rules' => 'trim|htmlspecialchars|required|max_length[50]'
		),
		'download_downloads' => array(
			'field' => 'download_downloads',
			'label' => 'lang:dm_admin.downloads_label',
			'rules' => 'trim|integer|greater_then[-1]'
		),
		'download_status' => array(
			'field' => 'download_status',
			'label' => 'lang:dm_admin.status_label',
			'rules' => 'trim|required|integer'
		),
		'download_login' => array(
			'field' => 'download_login',
			'label' => 'lang:dm_admin.login_label',
			'rules' => 'trim|required|integer'
		),
		'download_type' => array(
			'field' => 'download_type',
			'label' => 'lang:dm_admin.type_label',
			'rules' => 'trim|required|integer'
		),
		'download_file_file' => array(
			'field' => 'download_file',
			'label' => 'lang:dm_admin.file_label',
			'rules' => 'trim'
		),
		'download_file_url' => array(
			'field' => 'download_file',
			'label' => 'lang:dm_admin.file_label',
			'rules' => 'trim|prep_url|max_length[200]'
		),
	);

	/**
	 * Load all needed languages/model/libraries
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->language('download_manager');
		$this->load->model('download_manager_m');
		$this->load->libraries(array('files/files', 'form_validation'));
	}
	
	/**
	 * Overview list
	 * @access public
	 */
 	public function index()
    {          
 		$this->data['downloads'] = $this->download_manager_m->get_all();
		
        $this->template
			->title($this->module_details['name'])
			->enable_parser(true)
			->build('admin/index', $this->data);
	}
	
	/**
	 * Create a new download entry
	 * @access public
	 */
	public function create()
	{
		$this->method = 'create';
		$this->form_validation->set_rules($this->validation_rules);	
		
		// form was send and passed validation
		if($this->form_validation->run()) 
		{	
			// Upload a file if needed, else just grab the passed url
			if(!empty($_FILES) && $_FILES['download_file_file']['size'] > 0)
			{
				$upload = Files::upload(
					$this->download_manager_m->get_folder(),
					$_FILES['download_file_file']['name'], 
					'download_file_file'
				);
				
				if($upload['status'])
					$file = $upload['data']['id'];
			} 
			else 
			{
				$file = $this->input->post('download_file_url');
			}
			
			// setup post data, and insert the entry
			$data = array(
				'name' 		=> $this->input->post('download_name'),
				'slug' 		=> $this->input->post('download_slug'),
				'downloads' => $this->input->post('download_downloads'),
				'type' 		=> $this->input->post('download_type'),
				'status' 	=> $this->input->post('download_status'),
				'login' 	=> $this->input->post('download_login'),
				'file'		=> $file,
			);
			
			$insert_id = $this->download_manager_m->insert($data);
			
			// success message and redirection	
			if($insert_id) 
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('dm_admin.add_success'), $this->input->post('download_name')));
				
				$this->input->post('btnAction') == 'save_exit' ? 
					redirect('admin/download_manager') : 
					redirect('admin/download_manager/edit/' . $insert_id);
			}
		}
		
		// default file value
		$this->data['file'] = array(
			'name' 		=> null,
			'slug' 		=> null,
			'type' 		=> null,
			'status' 	=> null,
			'login' 	=> null,
			'file' 		=> null,
			'downloads' => 0
			
		);
		$this->data['file'] = (object)$this->data['file'];
		
		$this->template
			->title($this->module_details['name'])
			->append_js('module::download_manager.js', TRUE)
			->build('admin/form',$this->data);
	}
	
	/**
	 * Edit a download entry
	 * @access public
	 * @param int $id
	 */
	public function edit($id = 0)
	{
		$this->method = 'edit';
		
		// get the file
		$this->data['file'] = $this->download_manager_m->get( $id);
		
		// on submit
		if(!empty($_POST)) {
			$this->form_validation->set_rules($this->validation_rules);	
			
			// validation passed
			if($this->form_validation->run()) 
			{	
				// determin the download file
				if(!empty($_FILES) && $_FILES['download_file_file']['size'] > 0 )
				{
					$upload = Files::upload(
						$this->download_manager_m->get_folder(),
						$_FILES['download_file_file']['name'], 
						'download_file_file'
					);
					
					if($upload['status'])
						$file = $upload['data']['id'];
						
				} 
				else if($this->input->post('download_file_url') != '')
				{
					if($this->data['file']->type == Download_Manager_m::$TYPE['LOCAL'])
						Files::delete_file($this->data['file']->file->id);
						
					$file = $this->input->post('download_file_url');
				} 
				else
				{
					$file = $this->input->post('old_download_file');
				}
				
				// update data
				$data = array(
					'name' 		=> $this->input->post('download_name'),
					'slug' 		=> $this->input->post('download_slug'),
					'downloads' => $this->input->post('download_downloads'),
					'type' 		=> $this->input->post('download_type'),
					'status' 	=> $this->input->post('download_status'),
					'login' 	=> $this->input->post('download_login'),
					'file'		=> $file,
				);
				
				$update_id = $this->download_manager_m->update($id,$data);
				
				//send success message
				if($update_id) 
				{
					$this->session->set_flashdata('success', sprintf($this->lang->line('dm_admin.edit_success'), $this->input->post('name')));
					
					if( $this->input->post('btnAction') == 'save_exit') 
						redirect('admin/download_manager');
					else 
						redirect('admin/download_manager/edit/' . $id);
				}
			}
		}		
		
		$this->template
			->title($this->module_details['name'])
			->enable_parser(true)
			->append_js('module::download_manager.js', TRUE)
			->build('admin/form', $this->data);
	}
	
	/**
	 * Delete a download entry
	 * @access public
	 * @param int $id
	 */
	public function delete(int $id)
	{
		if(!isset($id)) 
		{ 
			$this->session->set_flashdata('error', lang('dm_admin.delete_fail') );
			redirect('admin/download_manager'); 
		} else {
			$this->session->set_flashdata('success', lang('dm_admin.delete_success') );
			$this->download_manager_m->delete($id); 
			redirect('admin/download_manager');
		}
	}
}