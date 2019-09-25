<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Periode Penilaian
 *
 * Updated  2018, 10 June 22:59
 *
 * @author  Indra Prasetya <indraprasetya154@gmail.com>
 *
 */

class Periodepenilaian extends MX_Controller
{

	function __construct()
	{

		parent::__construct();

		$this->system 	= $this->config->item('system');
		$this->prefix 	= $this->db->dbprefix;
		$this->table 	= $this->prefix . "_periode_penilaian";
		$this->pkey 	= "periode_id";
		$this->join 	= $this->prefix ."_sys_user";
		$this->fkey 	= "user_id";
		$this->module 	= "periodepenilaian";
	}

	public function index()
	{

		$this->page();
	}

	public function page()
	{

		//auth
		$this->auth->authorize($this->system['userData'], $this->module, 'list');
		$this->urlpattern->setQueryString();

		$data 	= array();
		$alert	= "";

		$msg 	= $this->session->flashdata('message');

		//alert
		if ($msg != '') {

			$alert 	= "
						<div class='alert alert-success'>
						    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
						    <strong>SUCCESS !</strong> " . $msg . ".
						</div>
					";

			$this->session->set_flashdata('message', '');
		}

		//paggination
		$pagging_uri = 3;

		if ($this->uri->segment($pagging_uri)) {
			$start = $this->uri->segment($pagging_uri);
		} else {
			$start = 0;
		}

		$limit 			= 20;
		$join 			= array($this->join, $this->join .'.'. $this->fkey .' = '. $this->table .'.'. $this->fkey, 'left');
		// $join2 			= array($this->join2, $this->join2 .'.'. $this->fkey2 .' = '. $this->table .'.'. $this->fkey2, 'left');
		$data['datas']	= $this->_dataModel->get_data($this->table, '', $limit, $start, array('periode_id', 'DESC'), $join);
		// echo $this->db->last_query(); die;

		$config['base_url'] 	= site_url('periodepenilaian/page/');
		$config['total_rows'] 	= $this->_dataModel->table_record_count;
		$config['per_page'] 	= $limit;
		$config['uri_segment'] 	= $pagging_uri;

		$this->pagination->initialize($config);

		$data['page_links']		= $this->pagination->create_links();
		$data['total'] 			= $this->_dataModel->table_record_count;
		$data['number'] 		= $start;
		$data['alert'] 			= $alert;
		$data['key'] 			= '';
		$data['param'] 			= '';

		$this->template->set('title', 'Periode Penilaian');
		$this->template->set('menu',  'periodepenilaian');
		$this->template->load('root', 'list', $data);
	}

	public function search()
	{

		//auth
		$this->auth->authorize($this->system['userData'], $this->module, 'list');
		$this->urlpattern->setQueryString();

		$data 	= array();
		$key 	= $this->input->get('key');
		$param 	= $this->input->get('param');

		$search = array($param => $key);

		//paggination
		$pagging_uri = 3;

		if ($this->uri->segment($pagging_uri)) {
			$start = $this->uri->segment($pagging_uri);
		} else {
			$start = 0;
		}

		$limit 			= 20;
		$join 			= array($this->join, $this->join .'.'. $this->fkey .' = '. $this->table .'.'. $this->fkey, 'left');
		// $join2 			= array($this->join2, $this->join2 .'.'. $this->fkey2 .' = '. $this->table .'.'. $this->fkey2, 'left');
		$data['datas']	= $this->_dataModel->get_search($this->table, $search, $limit, $start, array('periode_id', 'DESC'), '', $join);

		$config['base_url'] 	= site_url('periodepenilaian/search/');
		$config['suffix'] 		= "?key=" . $key . "&param=" . $param;
		$config['first_url'] 	= $config['base_url'] . $config['suffix'];
		$config['total_rows'] 	= $this->_dataModel->table_record_count;
		$config['per_page'] 	= $limit;
		$config['uri_segment'] 	= $pagging_uri;

		$this->pagination->initialize($config);

		$msg 	= "Hasil pencarian untuk ' <i>" . $key . "</i> '";
		$alert 	= "
					<div class='alert alert-success'>
					    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
					    <strong>SUCCESS !</strong> " . $msg . ".
					</div>
				";

		$data['page_links']		= $this->pagination->create_links();
		$data['total'] 			= $this->_dataModel->table_record_count;
		$data['number'] 		= $start;
		$data['alert'] 			= $alert;
		$data['key'] 			= $key;
		$data['param'] 			= $param;

		$this->template->set('title', 'Periode Penilaian');
		$this->template->set('menu',  'periodepenilaian');
		$this->template->load('root', 'list', $data);
	}

	public function add()
	{
		//auth
		$this->auth->authorize($this->system['userData'], $this->module, 'add');
		$this->urlpattern->resetQueryString();

		$data 	= array();

		if ($this->input->post('submit')) {

			$nama_periode = $this->input->post('nama_periode');
			$tanggal_mulai = $this->input->post('tanggal_mulai');
			$tanggal_selesai = $this->input->post('tanggal_selesai');
			$date			= date('Y-m-d H:i:s');

			$value 	= array(
				'nama_periode'	 		=> $nama_periode,
				'tanggal_mulai'	 		=> $tanggal_mulai,
				'tanggal_selesai'		=> $tanggal_selesai,
				'user_id'				=> $this->auth->user()['id'],
				'created_at' => $date,
				'updated_at' => $date
			);

			$res 	= $this->_dataModel->insert($this->table, $value);

			if ($res) {

				$msg = "Periode Penilaian berhasil ditambahkan";

				$this->session->set_flashdata('message', $msg);

				// redirect($this->urlpattern->getRedirect());
				redirect(site_url('periodepenilaian'));
			}
		}

		$this->template->set('title', 'Tambah Periode Penilaian');
		$this->template->set('menu',  'periodepenilaian');
		$this->template->load('root', 'add', $data);
	}

	public function edit($id)
	{

		//auth
		$this->auth->authorize($this->system['userData'], $this->module, 'edit');
		$this->urlpattern->resetQueryString();

		$data 	= array();
		$detail = $this->_dataModel->getDetail($this->table, $this->pkey, $id);

		//check data
		if (!$detail) {
			echo "
				<script type='text/javascript'>
					alert('Data yang dimaksud tidak sersedia');
					document.location = '" . $this->urlpattern->getRedirect() . "';
				</script>
			";

			exit;
		}

		if ($this->input->post('submit')) {

			$nama_periode = $this->input->post('nama_periode');
			$tanggal_mulai = $this->input->post('tanggal_mulai');
			$tanggal_selesai = $this->input->post('tanggal_selesai');
			$date			= date('Y-m-d H:i:s');

			$value 	= array(
				'nama_periode'	 		=> $nama_periode,
				'tanggal_mulai'	 		=> $tanggal_mulai,
				'tanggal_selesai'		=> $tanggal_selesai,
				'user_id'				=> $this->auth->user()['id'],
				'updated_at' => $date
			);

			$res 	= $this->_dataModel->update($this->table, $this->pkey, $id, $value);

			if ($res) {

				$msg = "Periode Penilaian berhasil diubah";

				$this->session->set_flashdata('message', $msg);

				// redirect($this->urlpattern->getRedirect());
				redirect(site_url('periodepenilaian'));
			}
		}

		$data['data'] 		= $detail;

		$this->template->set('title', 'Ubah Periode Penilaian');
		$this->template->set('menu',  'periodepenilaian');
		$this->template->load('root', 'edit', $data);
	}

	public function delete($id)
	{

		//auth
		$this->auth->authorize($this->system['userData'], $this->module, 'delete');
		$this->urlpattern->resetQueryString();

		$detail = $this->_dataModel->getDetail($this->table, $this->pkey, $id);

		//check data
		if (!$detail) {
			echo "
				<script type='text/javascript'>
					alert('Data yang dimaksud tidak sersedia');
					document.location = '" . $this->urlpattern->getRedirect() . "';
				</script>
			";

			exit;
		}

		$res 	= $this->_dataModel->delete($this->table, $this->pkey, $id);

		if ($res) {

			$msg = "Periode Penilaian berhasil dihapus";

			$this->session->set_flashdata('message', $msg);

			// redirect($this->urlpattern->getRedirect());
			redirect(site_url('periodepenilaian'));
		}
	}
}