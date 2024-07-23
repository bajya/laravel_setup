<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ContactUs;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class ContactUsController extends Controller {
	public $contactus;
	public $columns;

	public function __construct() {
		$this->contactus = new ContactUs;
		$this->columns = [
			"sno", "email", "action",
		];

		//$this->middleware('permission:contactus-list|contactus-edit', ['only' => ['index','update']]);
        //$this->middleware('permission:contactus-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('backend.contactus.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function contactusAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->contactus->fetchContactUs($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$contactus = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$contactus = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($contactus as $list) {
			$data = [];
			$data['sno'] = $i++;
			$data['name'] = ucfirst($list->name);
			$data['state'] = ucfirst($list->state);
			$data['city'] = ucfirst($list->city);
			$data['email'] = $list->email;
			$data['mobile'] = $list->country_code.' '.$list->mobile;
			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));
			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

			if (Helper::checkAccess(route('viewContactUs'))) {
				$action .= '<a href="' . route('viewContactUs', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			}
			$action.="</div>";
			$data['action'] = $action;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => count($total),
			'recordsFiltered' => count($total),
		]);
		echo $data;

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		$type = 'Show';
		if (isset($id) && $id != null) {
			$contactus = ContactUs::where('id', $id)->first();
			if (isset($contactus->id)) {
				return view('backend.contactus.view', compact('contactus', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('contactus');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('contactus');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id = null) {
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id = null) {

	}
	/**
	 * Remove the specified FQA from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		
	}

}
