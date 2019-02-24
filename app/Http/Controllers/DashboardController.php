<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Exports\SolidaridadEmployeeUsersExport;
use App\Exports\IndianUsersExport;
use App\Exports\NonIndianUsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\Session;
use App\SessionBooking;

class DashboardController extends Controller
{
	/**
     * Download the user details in XLSX format.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request) 
    {
    	ob_end_clean();
    	ob_start();

    	if ($request->users == self::ALL_USERS) 
    	{
    		return Excel::download(new UsersExport, 'all_users.xlsx');
    	}
    	else if($request->users == self::SOLIDARIDAD_USERS)
    	{
    		return Excel::download(new SolidaridadEmployeeUsersExport, 'solidaridad_users.xlsx');
    	} 
    	else if($request->users == self::INDIAN_USERS)
    	{
    		return Excel::download(new IndianUsersExport, 'indian_users.xlsx');
    	}
    	else if($request->users == self::NON_INDIAN_USERS)
    	{
    		return Excel::download(new NonIndianUsersExport, 'non_indian_users.xlsx');
    	}

        ob_flush();
    }

    /**
     * User analytics.
     *
     * @return \Illuminate\Http\Response
     */
    public function userAnalytics()
    {
    	$user = new User();
    	$result = array();

        $result['total_users'] = count($user->getAllUsersDetails());
        $result['total_indian_users'] = count($user->getIndianUsersDetails());
        $result['total_non_indian_users'] = count($user->getNonIndianUsersDetails());
        $result['total_solidaridad_employees'] = $user->countTotalSolidaridadEmplees();

        $this->setStatusCode(200);
        return $this->respondWithSuccess($result);
    }

    /**
     * User analytics.
     *
     * @return \Illuminate\Http\Response
     */
    public function sessionAnalytics()
    {
    	$session = new Session();
    	$booking = new SessionBooking();
    	$result = array();

    	$sessions = $session->getSessionByParallel();

    	if (count($sessions)) 
    	{

    		foreach ($sessions as $key => $value) 
	    	{
	    		$room = $session->getTotalSeats($value->id);
	    		
	    		$result[$key]['session_name'] = $value->session_name;
	    		$result[$key]['room_name'] = $value->room_name;
	    		$result[$key]['file_name'] = $value->file_name;
	    		$result[$key]['file_path'] = $value->file_path;
	    		$result[$key]['session_date'] = $value->session_date;
	    		$result[$key]['total_seats'] = $room->total_seats;
	    		$result[$key]['booked_seats'] = $booking->getBookingSeats($value->id);
	    	}
    	}


        $this->setStatusCode(200);
        return $this->respondWithSuccess($result);
    }
}
