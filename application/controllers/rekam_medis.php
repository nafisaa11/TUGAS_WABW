<?php
class Rekam_medis extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('RekamMedis_model'); // Nama model harus sama persis
        $this->load->model('Login_model');
        $this->load->library('IdGenerator');
        $this->load->library('form_validation'); // Load form validation library
        $this->load->library('session');
        $this->load->library('pagination');
    }

    public function index()
    {

        // Set form validation rules
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        // Check if form validation passes
        if ($this->form_validation->run() == FALSE) {
            // Reload login page if validation fails
            $data['judul'] = 'Halaman Login';
            $this->load->view('template/header', $data);
            $this->load->view('Rekam_medis/index');
            $this->load->view('template/footer');
        } else {
            // Retrieve input data
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            // Check login credentials using the model
            $user = $this->Login_model->login($username, $password);

            if ($user) {
                // Set session data for successful login
                $this->session->set_userdata('username', $username);
                redirect('Rekam_medis/main'); // Redirect to the main page if login is successful
            } else {
                // Set flash message for invalid login and reload login page
                $this->session->set_flashdata('error', 'Username atau password tidak sesuai');
                redirect('Rekam_medis/index');
            }
        }
    }

    public function logout()
    {
        // Hapus semua data sesi
        $this->session->unset_userdata('user_data');  // Hapus data user
        $this->session->sess_destroy();               // Hapus semua sesi

        // Redirect ke halaman login atau halaman utama
        redirect('Rekam_medis/index');
    }

    // DAFTAR REKAM MEDIS
    public function main()
    {
        $data['judul'] = 'Data Pasien';

        // Get search keyword
        if ($this->input->post('submit')) {
            $data['keyword'] = $this->input->post('keyword');
            // Reset start data if searching
            $this->session->set_userdata('keyword', $data['keyword']);
            redirect('Rekam_medis/main'); // Reload the page to reset pagination
        } else {
            $data['keyword'] = $this->session->userdata('keyword');
        }


        // Load konfigurasi custom
        $this->config->load('custom');

        $primaryColor = $this->config->item('colors')['Main8'];
        $secondaryColor = $this->config->item('colors')['Main9'];
        $thirdColor = $this->config->item('colors')['Main4'];
        $textColor = $this->config->item('colors')['White'];


        // Config Pagination
        $config['base_url'] = site_url('Rekam_medis/main');
        $config['per_page'] = 5;
        $data['start'] = $this->uri->segment(3, 0);

        // Gunakan warna dari konfigurasi custom
        $config['full_tag_open'] = '<ul class="flex space-x-2">';
        $config['full_tag_close'] = '</ul>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['num_links'] = 1;

        // Gunakan warna primary untuk button di pagination
        $config['attributes'] = [
            'class' => "btn p-regular py-2 px-4 rounded",
            'style' => "background-color: $primaryColor; color: $textColor;",
            'data-hover-color' => $thirdColor,
        ];

        // Halaman aktif menggunakan warna secondary
        $config['cur_tag_open'] = '<li><a class="btn p-semibold text-white py-2 px-4 rounded" style="background-color: ' . $secondaryColor . '; "hover:  ' . $thirdColor . ';">';

        $config['cur_tag_close'] = '</a></li>';

        // Tombol next dan prev
        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        // Tombol pertama dan terakhir
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        if ($data['keyword']) {
            // Get data with search keyword
            $config['total_rows'] = $this->RekamMedis_model->countPasien($data['keyword']);
            $data['pasien'] = $this->RekamMedis_model->getPasien($config['per_page'], $data['start'], $data['keyword']);
        } else {
            // Get all data without search keyword
            $config['total_rows'] = $this->RekamMedis_model->countAllPasien();
            $data['pasien'] = $this->RekamMedis_model->getPasien($config['per_page'], $data['start']);
        }


        if (!$this->input->post('submit')) {
            // Hapus session jika tidak ada pencarian baru
            $this->session->unset_userdata('keyword');
        }


        $this->pagination->initialize($config);

        // Load views
        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/main', $data);
        $this->load->view('template/footer');
    }

    public function tambahDokter()
    {
        $data['judul'] = 'Halaman Tambah Dokter';

        $id_dokter = $this->uri->segment(3);
        $data['ID_Dokter'] = $id_dokter;

        if ($this->input->post()) {
            $this->RekamMedis_model->tambahDataDokter(); // Menambahkan data dokter
            $this->session->set_flashdata('message', 'Data dokter berhasil ditambahkan.'); // Mengirimkan pesan sukses
            redirect('rekam_medis/mainDokter'); // Redirect setelah menambahkan data
        }

        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/TambahDokter', $data);
        $this->load->view('template/footer');
    }


    public function tambahPasien()
    {
        $data['judul'] = 'Halaman Tambah Pasien';

        if ($this->input->post()) {
            $this->RekamMedis_model->tambahDataPasien();
            redirect('rekam_medis/main');
        }

        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/TambahPasien', $data);
        $this->load->view('template/footer');
    }

    public function tambahRekamMedis()
    {
        $data['judul'] = 'Halaman Tambah Rekam Medis';

        // Retrieve ID_Pasien from URL or POST data
        $id_pasien = $this->uri->segment(3) ?: $this->input->post('id_pasien');
        if (!$id_pasien) {
            // Handle case if ID_Pasien is missing
            show_error('ID Pasien tidak ditemukan.', 400);
        }

        $data['ID_Pasien'] = $id_pasien;

        // If form is submitted
        if ($this->input->post()) {
            $this->RekamMedis_model->tambahDataRekamMedis($id_pasien, $this->input->post());

            //alert('Data rekam medis berhasil ditambahkan.');
            $this->session->set_flashdata('success', 'Data rekam medis berhasil ditambahkan.');


            redirect('Rekam_medis/detail/' . $id_pasien);
        }

        // Load view for input form
        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/inputRekamMedis', $data);
        $this->load->view('template/footer');
    }





    public function edit($NO_RekamMedis)
    {
        $data['judul'] = 'Halaman Edit Rekam Medis';
        // Ambil data rekam medis berdasarkan NO_RekamMedis
        $data['rekam_medis'] = $this->RekamMedis_model->getRekamMedisById($NO_RekamMedis);

        // Jika form disubmit
        if ($this->input->post()) {
            // Update data rekam medis
            $this->RekamMedis_model->editDataRekamMedis($NO_RekamMedis);

            $this->session->set_flashdata('success', 'Data rekam medis berhasil diubah.');

            redirect('rekam_medis/detail/' . $data['rekam_medis']['ID_Pasien']); // Redirect ke detail pasien
        }

        // Load view untuk form edit
        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/edit', $data);
        $this->load->view('template/footer');
    }


    public function detail($id_pasien = null)
    {
        if (!$id_pasien) {
            show_error('ID pasien tidak valid.');
            return;
        }


        $data['judul'] = 'Halaman Detail Rekam Medis';
        $data['pasien'] = $this->RekamMedis_model->getDetailPasien($id_pasien);
        $data['rekam_medis'] = $this->RekamMedis_model->getRekamMedisByPasien($id_pasien);

        // Load views
        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/detail', $data);
        $this->load->view('template/footer');
    }


    public function mainDokter()
    {
        $data['judul'] = 'Data Dokter';

        // Ambil keyword pencarian
        if ($this->input->post('submit')) {
            $data['keyword'] = $this->input->post('keyword');
            $this->session->set_userdata('keyword_dokter', $data['keyword']);
            redirect('Rekam_medis/mainDokter');
        } else {
            $data['keyword'] = $this->session->userdata('keyword_dokter');
        }

        // Load konfigurasi custom
        $this->config->load('custom');
        $primaryColor = $this->config->item('colors')['Main8'];
        $secondaryColor = $this->config->item('colors')['Main9'];
        $thirdColor = $this->config->item('colors')['Main4'];
        $textColor = $this->config->item('colors')['White'];

        // Konfigurasi Pagination
        $config['base_url'] = site_url('Rekam_medis/mainDokter');
        $config['per_page'] = 5;
        $data['start'] = $this->uri->segment(3, 0);

        // Styling pagination
        $config['full_tag_open'] = '<ul class="flex space-x-2">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['num_links'] = 2;

        $config['attributes'] = [
            'class' => "btn p-regular py-2 px-4 rounded",
            'style' => "background-color: $primaryColor; color: $textColor;",
            'data-hover-color' => $thirdColor,
        ];

        $config['cur_tag_open'] = '<li><a class="btn p-semibold text-white py-2 px-4 rounded" style="background-color: ' . $secondaryColor . ';">';
        $config['cur_tag_close'] = '</a></li>';

        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        if ($data['keyword']) {
            // Ambil data dengan keyword pencarian
            $config['total_rows'] = $this->RekamMedis_model->countDokter($data['keyword']);
            $data['dokter'] = $this->RekamMedis_model->getDokter($config['per_page'], $data['start'], $data['keyword']);
        } else {
            // Ambil semua data tanpa keyword
            $config['total_rows'] = $this->RekamMedis_model->countAllDokter();
            $data['dokter'] = $this->RekamMedis_model->getDokter($config['per_page'], $data['start']);
        }

        if (!$this->input->post('submit')) {
            // Hapus session jika tidak ada pencarian baru
            $this->session->unset_userdata('keyword_dokter');
        }

        $this->pagination->initialize($config);

        // Load views
        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/mainDokter', $data);
        $this->load->view('template/footer');
    }

    public function editDokter($id_dokter)
    {
        $data['judul'] = 'Edit Data Dokter';
        // Ambil data dokter berdasarkan ID_Dokter
        $data['dokter'] = $this->RekamMedis_model->getDokterById($id_dokter);
    
        // Jika form disubmit
        if ($this->input->post()) {
            // Update doctor data
            $this->RekamMedis_model->editDataDokter($id_dokter);
    
            // Set success flash data
            $this->session->set_flashdata('message', 'Data dokter berhasil diubah.');
    
            // Redirect to the mainDokter page
            redirect('rekam_medis/mainDokter');
        }
    
        // Load view untuk form edit
        $this->load->view('template/header', $data);
        $this->load->view('Rekam_medis/editDokter', $data);
        $this->load->view('template/footer');
    }
    


    public function hapusDokter($id_dokter)
    {
        // Panggil fungsi deleteDokter dari model
        $result = $this->RekamMedis_model->deleteDokter($id_dokter);

        if ($result) {
            $this->session->set_flashdata('message', 'Data dokter berhasil dihapus.');
        } else {
            // Jika gagal dihapus, tampilkan pesan gagal
            $this->session->set_flashdata('message', 'Data dokter gagal dihapus.');
        }

        // Redirect kembali ke halaman daftar dokter
        redirect('Rekam_medis/mainDokter');
    }



}




