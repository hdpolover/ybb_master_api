<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Forbidden extends CI_Controller
{
    
    function index(){
        show_error('Forbidden!');
    }

}
