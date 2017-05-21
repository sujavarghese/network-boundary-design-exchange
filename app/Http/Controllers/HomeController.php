<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Boundary;
use App\User;
use App\UsersLog;


class HomeController extends Controller
{

    public $user_count = 0;
    public $ada_count = 0;
    public $sam_count = 0;
    public $dashboard_data = array();
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function get_users()
    {
        return User::count();
    }
    public function get_ada_count()
    {
        return Boundary::where('boundary_type', '=', 'ADA')->count();
    }
    public function get_sam_count()
    {
        return Boundary::where('boundary_type', '=', 'SAM')->count();
    }
    public function get_user_activity_log()
    {
        $log_table = UsersLog::all();
        $valid_upload_count = 0;
        $valid_validate_count = 0;
        $valid_load_count = 0;
        foreach ($log_table as $log_obj) {
            if ($log_obj['upload_status'] == "PASS") {
                $valid_upload_count = $valid_upload_count + 1;
            }
            if ($log_obj['validation_status'] == "VALIDATED") {
                $valid_validate_count = $valid_validate_count + 1;
            }
            if ($log_obj['load_status'] == "PASS") {
                $valid_load_count = $valid_load_count + 1;
            }
        }
        return array(
            array(
                "value"=> $valid_upload_count,
                "color"=> "gray"
            ),
            array(
                "value"=> $valid_validate_count,
                "color"=> "blue"
            ),
            array(
                "value"=> $valid_load_count,
                "color"=> "aqua"
            ),
        );
    }
    public function get_dashboard_details()
    {
        $this->dashboard_data['user_count'] = $this->get_users();
        $this->dashboard_data['ada_count'] = $this->get_ada_count();
        $this->dashboard_data['sam_count'] = $this->get_sam_count();
        $this->dashboard_data['load_stat'] = $this->get_user_activity_log();
    }

    public function index()
    {
        $this->get_dashboard_details();
        return view('home')->with(
            'dashboard_data', $this->dashboard_data
        );
    }
}
