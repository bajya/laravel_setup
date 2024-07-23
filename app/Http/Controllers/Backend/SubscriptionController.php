<?php
    
namespace App\Http\Controllers\Backend;
    
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Subscription;
use App\BusRuleRef;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use URL;
use Illuminate\Support\Arr;
use App\Country;   
use App\EmailTemplate; 
use Mail;
use App\Category;
use App\City;
use App\Region;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public $subscription;
    public $columns;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->subscription = new Subscription;
        $this->columns = [
            "select", "name","description","price", "created_at", "status","action",
        ];
       
    }

    public function index(Request $request) {
        $gender_type = $request->type ?? null;

        return view('backend.subscriptions.index',compact('gender_type'));
    }

    public function subscriptionsAjax(Request $request) {
    
        if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->search ??  '';
        }

        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->subscription->fetchSubscription($request, $this->columns);
        $total = $records->count();
        if (isset($request->start)) {
            $subscriptions = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $subscriptions = $records->offset($request->start)->limit($total)->get();
        }
        $result = [];
        foreach ($subscriptions as $subscription) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="subscription_id[]" value="' . $subscription->id . '"><i class="input-helper"></i></label></div>';
            $data['name'] = !empty($subscription->name) ?  ucfirst($subscription->name) : '-' ;
            $data['price'] = $subscription->price ?? '-' ;
           $data['description'] = (ucfirst($subscription->description) != null) ? \Str::limit(ucfirst($subscription->description), 50, '...') : '-';
           
            $data['activate'] = '<div class="bt-switch"><div><input type="checkbox"' . ($subscription->status == 'active' ? ' checked' : '') . ' data-id="' . $subscription->id . '" data-code="' . $subscription->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusSubscriptions"></div></div>';
            $data['created_at'] =  date('d-m-Y', strtotime($subscription->created_at));
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('editSubscriptions', ['id' => $subscription->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('viewSubscriptions', ['id' => $subscription->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';


           // $action .= '<a href="javascript:;" class="toolTip deleteUsers" data-toggle="tooltip" data-id="' . $user->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            $action.="</div>";
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
    public function create() {
      
        $type = 'add';
        $url = route('addSubscriptions');
		$subscription =null;
       
        return view('backend.subscriptions.create', compact('type', 'url','subscription'));
    }


    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'name' => 'required',
            'price' => 'required',
        
        ]);
        $attr = [
            'name' => 'Subscription Name',
            'price' => 'Subscription Price',
        
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('createSubscriptions')->withInput($request->all())->withErrors($validate);
        } else {
            try {
               
                $subscription = new Subscription;
                $subscription->name = $request->name;
                $subscription->price = $request->price;
                $subscription->type = $request->type;
                $subscription->description = $request->description;
                $subscription->created_at = date('Y-m-d H:i:s');
                $subscription->updated_at = date('Y-m-d H:i:s');

                // dd($user);
                if ($subscription->save()) {
                   
                    $request->session()->flash('success', 'Subscription added successfully');
                    return redirect()->route('subscriptions');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('subscriptions');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('subscriptions');
            }

        }
    }

    public function show(Request $request, $id = null) {
        $type = 'View';
        if (isset($id) && $id != null) {
            $subscription = Subscription::where('id', $id)->first();
            if(isset($subscription) && $subscription->marital_status=='inrelationship')
            {
                $subscription->marital_status ='In Relationship';
            }
            if (isset($subscription->id)) {
                return view('backend.subscriptions.view', compact('subscription', 'type'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('subscriptions');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('subscriptions');
        }
    }

    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            $subscription = Subscription::where('id', $id)->first();
            
            if (isset($subscription->id)) {
            
                $type = 'edit';
                $url = route('updateSubscriptions', ['id' => $subscription->id]);
                // $subscriptioncategorylist = Category::where("status","active")->get(['id',"name"]);
               
                return view('backend.subscriptions.create', compact('subscription', 'type', 'url'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('subscriptions');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('subscriptions');
        }
    }

    public function update(Request $request, $id = null) {
        if (isset($id) && $id != null) {

            $subscription = Subscription::where('id', $id)->first();
            $checkedMail = Subscription::where("id","!=",$id)->first();

            if (isset($subscription->id)) {
                $validate = Validator($request->all(),  [
                    'name' => 'required',
                   'price' => 'required',
                ]);
                $attr = [
                    'name' => 'Subscription Name',
                    'price' => 'Subscription Price',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editSubscriptions', ['id' => $subscription->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                       
                        $subscription->name = $request->name;
                        $subscription->price = $request->price;
                        $subscription->type = $request->type;
                        $subscription->description = $request->description;
                      //  $subscription->gift_category_id = $request->gift_category_id;    
                       
                        if ($subscription->save()) {
                           
                            $request->session()->flash('success', 'Subscription updated successfully');
                            return redirect()->route('subscriptions');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('editSubscriptions', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('editSubscriptions', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('editSubscriptions', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('editSubscriptions', ['id' => $id]);
        }

    }
    // activate/deactivate user
    public function updateStatus(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $subscription = Subscription::find($request->statusid);
            if (isset($subscription->id)) {
                $subscription->status = $request->status;
                if ($subscription->save()) {
                    $request->session()->flash('success', 'Subscription updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update Subscription. Please try again later.');
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->back();
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->back();
        }

    }

    // activate/deactivate user
    public function updateStatusAjax(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $subscription = Subscription::find($request->statusid);

            if (isset($subscription->id)) {
                $subscription->status = $request->status;
                if ($subscription->save()) {
                    echo json_encode(['status' => 1, 'message' => 'subscription status updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update subscription. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid subscription']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid subscription']);
        }
    }
    public function destroy(Request $request) {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $subscription = Subscription::find($request->deleteid);
            if (isset($subscription->id)) {
                $subscription->status = 'delete';
                if ($subscription->save()) {
                    DB::table('model_has_roles')->where('model_id',$subscription->id)->delete();
                    // Category::where("gift_category_id",$subscription->id)->update(['status'=>'delete']);
                    echo json_encode(["status" => 1, 'ids' => json_encode($request->deleteid), 'message' => 'Subscriptions deleted successfully.']);
                } else {
                    echo json_encode(["status" => 0, 'message' => 'Not all subscription were deleted. Please try again later.']);
                }
            } else {
                echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }
    public function bulkdelete(Request $request) {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $deleteid = explode(',', $request->deleteid);
            $ids = count($deleteid);
            $count = 0;
            foreach ($deleteid as $id) {
                $subscription = Subscription::find($id);

                if (isset($subscription->id)) {
                    $subscription->status = 'delete';
                    if ($subscription->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Subscriptions deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all Subscriptions were deleted. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }
    public function bulkchangeStatus(Request $request) {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $subscription = Subscription::find($id);

                if (isset($subscription->id)) {
                    if ($subscription->status == 'active') {
                        $subscription->status = 'inactive';
                    } elseif ($subscription->status == 'inactive') {
                        $subscription->status = 'active';
                    }

                    if ($subscription->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Subscriptions status updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all Subscriptions were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

 

}
