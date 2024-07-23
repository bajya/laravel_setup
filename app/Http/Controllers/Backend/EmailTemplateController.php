<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EmailTemplate;

use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class EmailTemplateController extends Controller {
	public $emailtemplate;
	public $columns;

	public function __construct() {
		$this->emailtemplate = new EmailTemplate;
		$this->columns = [
			"select","sno", "name","subject","description","footer","activate","created_at", "action",
		];
		$this->middleware('permission:emailtemplate-list|emailtemplate-edit', ['only' => ['index','update']]);
        $this->middleware('permission:emailtemplate-edit', ['only' => ['edit','update']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {

		return view('backend.emailtemplates.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function emailtemplateAjax(Request $request) {
		
		
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->searchval;
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->emailtemplate->fetchEmailTemplate($request, $this->columns);
		$total = $records->get();

		if (isset($request->start)) {
			$emailtemplate = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$emailtemplate = $records->offset($request->start)->limit(count($total))->get();
		}
		// echo $total;
		$result = [];
		$i = 1;
		foreach ($emailtemplate as $list) {
			$data = [];
			// $data['sno'] = $i++;
			$data['name'] = $list->name;
			$data['subject'] = $list->subject;
			$data['description'] =  $list->description  ?? '';
			$data['footer'] ="test"; $list->footer;

			 	$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="email_id[]" value="' . $list->id . '"><i class="input-helper"></i></label></div>';
			
			$data['name'] = $list->name;
			$data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($list->status == 'active' ? ' checked' : '') . ' data-id="' . $list->id . '" data-code="' . $list->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusEmailTemplate"></div></div>';

			$data['created_at'] =  date('d-m-Y', strtotime($list->created_at));

			$action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

			if (Helper::checkAccess(route('editemailtemplate'))) {
				$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editemailtemplate', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
			}

			 if (Helper::checkAccess(route('vieweditemailtemplate'))) {
			 	$action .= '<a href="' . route('vieweditemailtemplate', ['id' => $list->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
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
		if (isset($id) && $id != null) {
			$emailtemplate = $this->emailtemplate::where('id', $id)->first();
			if (isset($emailtemplate->id)) {
			
				return view('backend.emailtemplates.view', compact('emailtemplate'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('emailtemplate');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('emailtemplate');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$emailtemplate = $this->emailtemplate::where('id', $id)->first();
			if (isset($emailtemplate->id)) {
			
				$type = 'Edit';
				return view('backend.emailtemplates.create', compact('emailtemplate', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('emailtemplate');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('emailtemplate');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id = null) {
		
		if (isset($id) && $id != null) {
			$emailtemplate = $this->emailtemplate::where('id', $id)->first();
			if (isset($emailtemplate->id)) {
				try {
					$emailtemplate->name = $request->name;
					$emailtemplate->subject = $request->subject;
					//$emailtemplate->footer = $request->footer;
					$emailtemplate->description = $request->description;
					$emailtemplate->save();
					
					$request->session()->flash('success', 'Page updated successfully');
					return redirect()->route('emailtemplate');

				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('emailtemplate');
				}

			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('emailtemplate');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('emailtemplate');
		}

	}
	/**
	 * Remove the specified FQA from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		if (isset($request->id) && $request->id != null) {
			$emailtemplate = $this->emailtemplate::find($request->id);

			if (isset($emailtemplate->id)) {
				$emailtemplate->status = 'delete';
				if ($faq->save()) {
					echo json_encode(["status" => 1, 'message' => 'EmailTemplate deleted successfully.']);
				} else {
					echo json_encode(["status" => 0, 'message' => 'Some error occurred while deleting the FAQ']);
				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
			}

		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

		public  function bulkchangeStatus(Request $request){
		  if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $countries = EmailTemplate::find($id);

                if (isset($countries->id)) {
                    if ($countries->status == 'active') {
                        $countries->status = 'inactive';
                    } elseif ($countries->status == 'inactive') {
                        $countries->status = 'active';
                    }
                    if ($countries->save()) {
                    	$count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Email Template updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all country were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
	}


    	// activate/deactivate 
    public function updateStatusAjax(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $countries = EmailTemplate::find($request->statusid);

            if (isset($countries->id)) {
                $countries->status = $request->status;
                if ($countries->save()) {
                	
                    echo json_encode(['status' => 1, 'message' => 'Email template updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to email template. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid data']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid data']);
        }
    }
	
}
