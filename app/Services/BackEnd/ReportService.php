<?php
namespace App\Services\BackEnd;
use DB;
use Session;
use Lang;
use Image;
use App\Http\Helper;

class ReportService{

	public static function getIncomeExpenseHead(){
		$result = DB::table('income_expense_head')
				  ->orderBy('id','DESC')
				  ->get();
		return $result;
	}
	public static function searchDetailsIncomeReport($data=null){
		$result = DB::table('income_history as ih')
				  ->select(['ih.comments',
				  			'ih.amount',
				  			'ih.transection_date',
				  			'admins.name',
				  			])
				  ->join('admins', 'admins.id', '=', 'ih.fk_created_by')
				  ->whereBetween('ih.transection_date', array($data['from'], $data['to']))
				  ->where('ih.fk_income_expense_head_id',$data['incomeExpenseHead'])
				  ->orderBy('ih.id','DESC')
				  ->get();
		return $result;
	}
	
	public static function searchDetailsExpenseReport($data=null){
		$result = DB::table('expense_history as eh')
				  ->select(['eh.comments',
				  			'eh.amount',
				  			'eh.transection_date',
				  			'admins.name',
				  			])
				  ->join('admins', 'admins.id', '=', 'eh.fk_created_by')
				  ->whereBetween('eh.transection_date', array($data['from'], $data['to']))
				  ->where('eh.fk_income_expense_head_id',$data['incomeExpenseHead'])
				  ->orderBy('eh.id','DESC')
				  ->get();
		return $result;
	}

	public static function searchSummaryIncomeReport($data=null){
		$result = DB::table('income_history as ih')
				  ->select('ieh.id','ieh.title_lng1',DB::raw('sum(ih.amount) as income'))
				  ->join('income_expense_head as ieh','ieh.id','=','ih.fk_income_expense_head_id')
				  ->whereBetween('ih.transection_date', array($data['from'], $data['to']))
				  ->groupBy('ieh.id')
				  ->get();
				return Helper::arrayObjectToArray($result);
		
	}
	public static function searchSummaryExpenseReport($data=null){
		$result = DB::table('expense_history as eh')
				  ->select('ieh.id','ieh.title_lng1',DB::raw('sum(eh.amount) as expense'))
				  ->join('income_expense_head as ieh','ieh.id','=','eh.fk_income_expense_head_id')
				  ->whereBetween('eh.transection_date', array($data['from'], $data['to']))
				  ->groupBy('ieh.id')
				  ->get();
		return Helper::arrayObjectToArray($result);
	}

	public static function getShipmentDetails($data=null){
		$result= DB::table('orders')
					->select([
							'order_wise_shipping.address',
							'order_wise_shipping.shipping_date',
				  			'users.full_name',
				  			'users.email',
				  			'cities.city_name_lng1',
				  			'orders.invoice_no',
				  			'orders.total_amount',
				  			'orders.id',
				  			])
				    ->leftjoin('order_wise_shipping', 'orders.id', '=', 'order_wise_shipping.fk_order_id')
				    ->leftjoin('users', 'users.id', '=', 'orders.fk_user_id')
				    ->leftjoin('cities', 'cities.id', '=', 'order_wise_shipping.fk_city_id')
				    ->whereBetween('order_wise_shipping.shipping_date', array($data['from'], $data['to']))
				    ->where('orders.status', 3)
				    ->get();
				return $result;

	}


//=====================@@Order Details Ajax @@==================

	public static function orderDetailsReportAjax($data = null){
 		$result = DB::table('orders')
			->select(
					'users.full_name as user_name',
					'users.mobile_no',
					'users.email',
 					'orders.invoice_no',
 					'orders.total_amount',
 					'orders.order_date',
 					'orders.discount',
 					'orders.id as order_id',
 					'order_wise_shipping.shipping_date',
 					'order_wise_shipping.delivery_date'
					)
			->join ('users','users.id','=','orders.fk_user_id')
			->leftjoin('order_wise_shipping','order_wise_shipping.fk_order_id','=','orders.id')
			->whereBetween('orders.order_date',array($data['start_date'], $data['end_date']))
			->where('orders.status',2)
			->get();
 		return $result;
	}

}
