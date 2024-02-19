<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admins_upload extends CI_Controller
{

    public function index()
    {
        $this->load->view('upload_admin');
    }

    public function do_upload_profile($id = 0)
    {

        $admins = $this->MCore->get_data('admins', 'id = ' . $id)->row_array();
        if ($admins['profile_url'] != '') {
            $path = $admins['profile_url'];
            if (isset($path)) {
                unlink($path);
            }
        }
        $new_name = $this->session->userdata('id') . '_' . time();

        $config['upload_path']          = './uploads/admins';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['max_size']             = 5000;
        $config['file_name']            = $new_name;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("fileFoto")) {
            $data = array('upload_data' => $this->upload->data());

            $image = $data['upload_data']['file_name'];
            //Compress Image
            $config['image_library'] = 'gd2';
            $config['source_image'] = './uploads/admins/' . $image;
            $config['create_thumb'] = FALSE;
            $config['maintain_ratio'] = FALSE;
            $config['quality'] = '50%';
            // $config['width'] = 500;
            // $config['height'] = 500;
            $config['new_image'] = './uploads/admins/' . $image;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();

            $sql = $this->MCore->save_data('admins', ['profile_url' => $image], true, array('id' => $id));

            $arr['status'] = $sql;


            if ($sql) {
                $arr['message'] = 'Foto berhasil diperbarui';
            } else {
                $arr['message'] = 'Foto gagal diperbarui';
            }
        } else {
            $arr['status'] = 0;
            $arr['message'] = $this->upload->display_errors();
        }
        echo json_encode($arr);
    }
}
