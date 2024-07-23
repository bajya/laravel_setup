<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Newsletter;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class NewsletterController extends Controller {
	public $newsletter;
	public $columns;

	public function __construct() {
		$this->newsletter = new Newsletter;
		$this->columns = [
			"sno", "email", "action",
		];

		//$this->middleware('permission:newsletter-list|newsletter-edit', ['only' => ['index','update']]);
        //$this->middleware('permission:newsletter-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('backend.newsletter.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function newsletterAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->newsletter->fetchNewsletter($request, $this->columns);
		$total = $records->get();
		if (isset($request->start)) {
			$newsletter = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$newsletter = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($newsletter as $list) {
			$data = [];
			$data['sno'] = $i++;
			$data['email'] = $list->email;
			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));
			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

			if (Helper::checkAccess(route('viewNewsletter'))) {
				$action .= '<a href="' . route('viewNewsletter', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
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
			$newsletter = Newsletter::where('id', $id)->first();
			if (isset($newsletter->id)) {
				return view('backend.newsletter.view', compact('newsletter', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('newsletter');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('newsletter');
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
