<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analytics;
use DB;
use Response;

class AnalyticsController extends Controller
{
    //
    public function report_daily(Request $request)
    {
    	try
    	{

	    	$date_from 	= $request->date_from;
	    	$date_to 	= $request->date_to;
	    	$country 	= strtolower($request->country);
	    	$referer 	= strtolower($request->referer);

	    	// $analytics 	= Analytics::select(array(DB::raw('COUNT(id) as count'), 'date'))
	    	// 				->whereBetween('date', [$date_from, $date_to])
	    	// 				->groupBy('date')
	    	// 				->get();

	    	$analytics_qry 	= Analytics::select(array(DB::raw('COUNT(id) as count'), 'date'));

			if((isset($request->date_from)) && ($request->date_from != null))
			{
		    	$date_from 		= $request->date_from;

		    	if((!isset($request->date_to)) || ($request->date_to == null) || (trim($request->date_to) == ''))
		    	{
		    		$date_to = date('Y-m-d');
		    	}
		    	else
		    	{
		    		$date_to = $request->date_to;
		    	}

				$analytics_qry 	= $analytics_qry->whereBetween('date', [$date_from, $date_to]);
			}
			else
			{
				if((isset($request->date_to)) && ($request->date_to != null) && (trim($request->date_to) != ''))
		    	{
					$data 						= array();
					$data['success'] 			= false;
					$data['error'] 				= 'Can not pass date_to without date_from';
					return Response::json($data, 400);
		    	}
			}

			if((isset($request->country)) && ($request->country != null))
			{
				$country     	= strtolower($request->country);
				$analytics_qry 	= $analytics_qry->where('country', $country);
			}

			if((isset($request->referer)) && ($request->referer != null))
			{
				$referer 		= strtolower($request->referer);
				$analytics_qry 	= $analytics_qry->where('referer', $referer);
			}

			$analytics 	   		=	$analytics_qry->groupBy('date')->get();

			$report 			= array();

			foreach($analytics as $row)
			{
				$report[] = array('date'=>$row['date'], 'count'=>$row['count']);
			}

			$data 						= array();
			$data['success'] 			= true;
			$data['data']['report'] 	= $report;

			return Response::json($data);

		}
		catch(\Exception $e) 
		{
			$data 						= array();
			$data['success'] 			= false;
			$data['error'] 				= 'Internal server error: '.$e->getMessage();
			return Response::json($data, 500);

		}

    }

    public function report_monthly(Request $request)
    {
    	try
    	{

			$analytics_qry = Analytics::selectRaw('year(date) year, monthname(date) month, count(id) count');

			if((isset($request->date_from)) && ($request->date_from != null))
			{
		    	$date_from 		= $request->date_from;

		    	if((!isset($request->date_to)) || ($request->date_to == null) || (trim($request->date_to) == ''))
		    	{
		    		$date_to = date('Y-m-d');
		    	}
		    	else
		    	{
		    		$date_to = $request->date_to;
		    	}

				$analytics_qry 	= $analytics_qry->whereBetween('date', [$date_from, $date_to]);
			}
			else
			{
				if((isset($request->date_to)) && ($request->date_to != null) && (trim($request->date_to) != ''))
		    	{
					$data 						= array();
					$data['success'] 			= false;
					$data['error'] 				= 'Can not pass date_to without date_from';
					return Response::json($data, 400);
		    	}
			}

			if((isset($request->country)) && ($request->country != null))
			{
				$country     	= strtolower($request->country);
				$analytics_qry 	= $analytics_qry->where('country', $country);
			}

			if((isset($request->referer)) && ($request->referer != null))
			{
				$referer 		= strtolower($request->referer);
				$analytics_qry 	= $analytics_qry->where('referer', $referer);
			}

			$analytics 	   =	$analytics_qry->groupBy('year', 'month')
			                	->orderBy('year', 'desc')
			                	->get();

			$report 	= array();

			$yearArray 	= array(); 

			foreach($analytics as $row)
			{
				$yearArray[$row['year']][$row['month']] = array('count'=>$row['count']);
			}

			$report[] = $yearArray;

			$data 						= array();
			$data['success'] 			= true;
			$data['data']['report'] 	= $report;

			return Response::json($data);

		}
		catch(\Exception $e) 
		{
			$data 						= array();
			$data['success'] 			= false;
			$data['error'] 				= 'Internal server error: '.$e->getMessage();
			return Response::json($data, 500);

		}

    }

}
