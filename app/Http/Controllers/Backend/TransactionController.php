<?php

namespace App\Http\Controllers\Backend;
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
// use App\ContestJoin;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Image;
use URL; 
use Auth; 
use File; 
use Illuminate\Support\Arr;

class TransactionController extends Controller {
	public $transaction;
	public $columns;

	public function __construct() {
		$this->transaction = new Transaction;
		$this->columns = [
			"select", "s_no","title","transaction_no", "payment_type","amount", "status", "action"
		];
	
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$count = Transaction::where('status', '!=', 'delete')->count();
		return view('backend.transactions.index', compact('count'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function transactionAjax(Request $request) {
		if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
		
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->transaction->fetchTransactions($request, $this->columns);
		$count = $records->get();
		if (isset($request->start)) {
			$list = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$list = $records->offset($request->start)->limit(count($count))->get();
		}

		$result = [];
		
		$total = count($count);
		// die();
		$i = 1;
		foreach ($list as $cat) { 
			$data = [];
			$data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="transaction_id[]" value="' . $cat->id . '"><i class="input-helper"></i></label></div>';
			$data['sno'] = $request->start + $i++;
			$data['user_id'] = '<a href="' . route('users') . '">' . $cat->getUser->name . '</a>';
			$data['title'] = $cat->title ?? 'N/A';
            $data['transaction_no'] = $cat->transaction_no;
            $data['payment_type'] =  $cat->payment_type; 
            $data['amount'] =  $cat->amount;
			$data['status'] = ucfirst(config('constants.STATUS.' . $cat->status));
			$data['created_at'] =  date('d-m-Y', strtotime($cat->created_at));
			$action = '';
			
			$action .= '<a href="' . route('viewTransaction', ['id' => $cat->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
			
			$data['action'] = $action;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
		]);
		echo $data;

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	// public function create() { 
	// 	$type = 'add';
	// 	$url = route('addContestParticipant');
	// 	$contestparticipant = new ContestParticipant;
	// 	return view('backend.contestparticipants.create', compact('type', 'url', 'contestparticipant'));
	// }

	/**
	 * check for unique ContestParticipant
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	// public function checkContestParticipant(Request $request, $id = null) {
	// 	if (isset($request->contestparticipant_name)) {
	// 		$check = ContestParticipant::where('name', $request->contestparticipant_name);
	// 		if (isset($id) && $id != null) {
	// 			$check = $check->where('id', '!=', $id);
	// 		}
	// 		$check = $check->where('status', '!=', 'delete')->count();
	// 		if ($check > 0) {
	// 			return "false";
	// 		} else {
	// 			return "true";
	// 		}
	// 	} else {
	// 		return "true";
	// 	}
	// }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	// public function store(Request $request) {
	// 	$validate = Validator($request->all(), [
	// 		'contestparticipant_name' => 'required',
	// 		'location' => 'required',
			
	// 	]);
	// 	$attr = [
	// 		'contestparticipant_name' => 'Contest Participant Name',
	// 		'location' => 'Location',
		
	// 	];
	// 	$validate->setAttributeNames($attr);
	// 	if ($validate->fails()) {
	// 		return redirect()->route('createContestParticipant')->withInput($request->all())->withErrors($validate);
	// 	} else {
	// 		try {
	// 			$contestparticipant = new ContestParticipant();
			
	// 			$contestparticipant->name = $request->post('contestparticipant_name');
	// 			$contestparticipant->start_date = $request->post('start_date');
    //             $contestparticipant->end_date = $request->post('end_date');
    //             $contestparticipant->location = $request->post('location');
	// 			$contestparticipant->status = trim($request->post('status'));
	// 			$contestparticipant->created_at = date('Y-m-d H:i:s');
	// 			if ($contestparticipant->save()) {
					
	// 				$request->session()->flash('success', 'Contest Participant added successfully');
	// 				return redirect()->route('contestparticipants');
	// 			} else {
	// 				$request->session()->flash('error', 'Something went wrong. Please try again later.');
	// 				return redirect()->route('contestparticipants');
	// 			}
	// 		} catch (Exception $e) {
	// 			$request->session()->flash('error', 'Something went wrong. Please try again later.');
	// 			return redirect()->route('contestparticipants');
	// 		}
	// 	}
	// }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$type = 'Show';
			$transaction = Transaction::where('id', $id)->first();
			if (isset($transaction->id)) {
				return view('backend.transactions.view', compact('transaction', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('transactions');
			}
			
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('transactions');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	// public function edit(Request $request, $id = null) {
	// 	if (isset($id) && $id != null) {
    //         $contestparticipant = ContestParticipant::where('id', $id)->first();
    //         if (isset($contestparticipant->id)) {
    //             $type = 'edit';
    //             $url = route('updateContestParticipante', ['id' => $contestparticipant->id]);
    //             return view('backend.contestparticipants.create', compact('contestparticipant', 'type', 'url'));
    //         } else {
    //             $request->session()->flash('error', 'Invalid Data');
    //             return redirect()->route('contestparticipants');
    //         }
    //     } else {
    //         $request->session()->flash('error', 'Invalid Data');
    //         return redirect()->route('contestparticipants');
    //     }
	// }



	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	// public function update(Request $request, $id = null) {
	// 	if (isset($id) && $id != null) {
	// 		$contestparticipant = ContestParticipant::where('id', $id)->first();
	// 		if (isset($contestparticipant->id)) {
	// 			$validate = Validator($request->all(), [
	// 				'contestparticipant_name' => 'required',
	// 				'location' => 'required',
	// 			]);
	// 			$attr = [
	// 				'contestparticipant_name' => 'Contest Participant Name',
	// 				'location' => 'Location',
	// 			];
	// 			$validate->setAttributeNames($attr);
	// 			if ($validate->fails()) {
	// 				return redirect()->route('createContestParticipant')->withInput($request->all())->withErrors($validate);
	// 			} else {
	// 				try {
	// 					$contestparticipant->name = $request->post('contestparticipant_name');
	// 					$contestparticipant->start_date = $request->post('start_date');
    //                     $contestparticipant->end_date = $request->post('end_date');
    //                     $contestparticipant->location = $request->post('location');
	// 					$contestparticipant->status = trim($request->post('status'));

	// 					if ($contestparticipant->save()) {

							
	// 						$request->session()->flash('success', 'Contest Participant updated successfully');
	// 						return redirect()->route('contestparticipants');
	// 					} else {
	// 						$request->session()->flash('error', 'Something went wrong. Please try again later.');
	// 						return redirect()->route('contestparticipants');
	// 					}
	// 				} catch (Exception $e) {
	// 					$request->session()->flash('error', 'Something went wrong. Please try again later.');
	// 					return redirect()->route('contestparticipants');
	// 				}
	// 			}
	// 		} else {
	// 			$request->session()->flash('error', 'Invalid Data');
	// 			return redirect()->route('contestparticipants');
	// 		}
	// 	} else {
	// 		$request->session()->flash('error', 'Invalid Data');
	// 		return redirect()->route('contestparticipants');
	// 	}
	// }

	// activate/deactivate contestparticipants
	// public function updateStatus(Request $request) {

	// 	if (isset($request->statusid) && $request->statusid != null) {
	// 		$contestparticipant = ContestParticipant::find($request->statusid);

	// 		if (isset($contestparticipant->id)) {
	// 			$contestparticipant->status = $request->status;
	// 			if ($contestparticipant->save()) {
	// 				$request->session()->flash('success', 'Contest Participant updated successfully.');
	// 				return redirect()->back();
	// 			} else {
	// 				$request->session()->flash('error', 'Unable to update Contest Participantn. Please try again later.');
	// 				return redirect()->back();
	// 			}
	// 		} else {
	// 			$request->session()->flash('error', 'Invalid Data');
	// 			return redirect()->back();
	// 		}
	// 	} else {
	// 		$request->session()->flash('error', 'Invalid Data');
	// 		return redirect()->back();
	// 	}

	// }
  
	// activate/deactivate Contest Participant
	// public function updateStatusAjax(Request $request) {

	// 	if (isset($request->statusid) && $request->statusid != null) {
	// 		$contestparticipant = ContestParticipant::find($request->statusid);

	// 		if (isset($contestparticipant->id)) {
	// 			$contestparticipant->status = $request->status;
	// 			if ($contestparticipant->save()) {
	// 				echo json_encode(['status' => 1, 'message' => 'Contest Participant updated successfully.']);
	// 			} else {
	// 				echo json_encode(['status' => 0, 'message' => 'Unable to update Contest Participant. Please try again later.']);
	// 			}
	// 		} else {
	// 			echo json_encode(['status' => 0, 'message' => 'Invalid Contest Participant']);
	// 		}
	// 	} else {
	// 		echo json_encode(['status' => 0, 'message' => 'Invalid Contest Participant']);
	// 	}

	// }

	// public function deleteItems($root, $level) {
	// 	$child = $root->childCat;
	// 	foreach ($child as $ch) {
	// 		$ch->status = 'delete';
	// 		$ch->save();
	// 		$this->deleteItems($ch, ++$level);
	// 	}
	// 	$root = $child;
	// 	return true;
	// }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	// public function destroy(Request $request) {
	// 	if (isset($request->deleteid) && $request->deleteid != null) {
	// 		$recommendedContestParticipant = ContestParticipant::find($request->deleteid);
	// 		if (isset($recommendedContestParticipant->id)) {
	// 			$recommendedContestParticipant->status = 'delete';
	// 			if ($recommendedContestParticipant->save()) {
	// 				echo json_encode(['status' => 1, 'message' => 'Contest Participant deleted successfully.']);
	// 			} else {
	// 				echo json_encode(['status' => 0, 'message' => 'Unable to delete Contest Participant. Please try again later.']);
	// 			}
	// 		} else {
	// 			echo json_encode(['status' => 0, 'message' => 'Invalid Contest Participant']);
	// 		}
	// 	} else {
	// 		echo json_encode(['status' => 0, 'message' => 'Invalid Contest Participant']);
	// 	}
	// }

	
	/**
	 * Remove multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	// public function bulkdelete(Request $request) {
	// 	if (isset($request->deleteid) && $request->deleteid != null) {
	// 		$deleteid = explode(',', $request->deleteid);
	// 		$ids = count($deleteid);
	// 		$count = 0;
	// 		foreach ($deleteid as $id) {
	// 			$recommendedContestParticipant = ContestParticipant::find($id);
	// 			if (isset($recommendedContestParticipant->id)) {
	// 				$recommendedContestParticipant->status = 'delete';
	// 				if ($recommendedContestParticipant->save()) {
	// 					$count++;
	// 				}
	// 			}
	// 		}
	// 		if ($count == $ids) {
	// 			echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Contest Participant deleted successfully.']);
	// 		} else {
	// 			echo json_encode(["status" => 0, 'message' => 'Not all Contest Participant  were deleted. Please try again later.']);
	// 		}
	// 	} else {
	// 		echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
	// 	}
	// }
	/**
	 * activate/deactivate multiple resource from storage.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	// public function bulkchangeStatus(Request $request) {

	// 	if (isset($request->ids) && $request->ids != null) {
	// 		$ids = count($request->ids);
	// 		$count = 0;
	// 		foreach ($request->ids as $id) {
	// 			$recommendedContestParticipant = ContestParticipant::find($id);
	// 			if (isset($recommendedContestParticipant->id)) {
	// 				if ($recommendedContestParticipant->status == 'active') {
	// 					$recommendedContestParticipant->status = 'inactive';
	// 				} elseif ($recommendedContestParticipant->status == 'inactive') {
	// 					$recommendedContestParticipant->status = 'active';
	// 				}
	// 				if ($recommendedContestParticipant->save()) {
	// 					$count++;
	// 				}
	// 			}
	// 		}
	// 		if ($count == $ids) {
	// 			echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Contest Participant updated successfully.']);
	// 		} else {
	// 			echo json_encode(["status" => 0, 'message' => 'Not all Contest Participant were updated. Please try again later.']);
	// 		}
	// 	} else {
	// 		echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
	// 	}
	// }

}
