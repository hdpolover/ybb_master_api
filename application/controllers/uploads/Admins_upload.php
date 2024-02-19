<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admins_upload extends CI_Controller
{

    public function index()
    {
        $this->load->view('upload_admin');
    }

    public function do_upload_profile($id = 1)
    {

        // $admins = $this->mCore->get_data('admins', 'id = ' . $id)->row_array();
        // if ($admins['profile_url'] != '') {
        //     $path = $admins['profile_url'];
        //     if (isset($path)) {
        //         unlink($path);
        //     }
        // }
        $new_name = $id . '_' . time();

        $config['upload_path'] = './uploads/admins';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 5000;
        $config['file_name'] = $new_name;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload("fileFoto")) {

            //Get uploaded file information
            $upload_data = $this->upload->data();
            $fileName = $upload_data['file_name'];

            //File path at local server
            $source = './uploads/admins/' . $fileName;

            //Load codeigniter FTP class
            $this->load->library('ftp');

            //FTP configuration
            $ftp_config['hostname'] = 'ftp.ybbfoundation.com';
            $ftp_config['username'] = 'storage_user@ybbfoundation.com';
            $ftp_config['password'] = 'Ns@%L(y_iSU9';
            $ftp_config['port'] = 21;
            $ftp_config['debug'] = TRUE;

            //Connect to the remote server
            $this->ftp->connect($ftp_config);

            //File upload path of remote server
            $destination = 'YBB/uploads/admins/' . $fileName;

            //Upload file to the remote server
            $this->ftp->upload($source, $destination);

            //Close FTP connection
            $this->ftp->close();

            //Delete file from local server
            @unlink($source);

            $sql = $this->mCore->save_data('admins', ['profile_url' => config_item('dir_upload').'admins/'.$fileName], true, array('id' => $id));
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
