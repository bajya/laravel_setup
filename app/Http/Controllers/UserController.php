<?php
    
namespace App\Http\Controllers;
    
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\BusRuleRef;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use URL;
use Illuminate\Support\Arr;
use App\Country;   
use App\EmailTemplate; 
use Mail;
use App\Transaction;
use App\State;
use App\City;
use App\Region;
use App\BoxPurchase;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public $user;
    public $columns;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->user = new User;
       
        $this->columns = [
            "select", "name","last_name", "email","created_at", "activate", "action",
        ];
       
       /* $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);*/
    }

    public function index(Request $request) {
        // dd($request->all());
        // $gender_type = $request->type ?? null;

        return view('backend.users.index');
    }

    public function usersAjax(Request $request) {
    
        if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = $request->search ??  '';
        }

        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        // dd($request->all());

        $records = $this->user->fetchUsers($request, $this->columns);
        $total = $records->count();
        if (isset($request->start)) {
            $users = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $users = $records->offset($request->start)->limit($total)->get();
        }
        
        $result = [];
        foreach ($users as $user) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $user->id . '"><i class="input-helper"></i></label></div>';
            $data['name'] = !empty($user->name) ?  ucfirst($user->name) : '-' ;
            $data['last_name'] = !empty($user->last_name) ?  ucfirst($user->last_name) : '-' ;
            $data['mobile'] ="+". ucfirst($user->phone_code." ".$user->mobile);
            $data['email'] = $user->email;
            $data['address'] =  (ucfirst($user->address) != null) ? \Str::limit(ucfirst($user->address), 50, '...') : '-';
            $data['image'] = ($user->avatar != null) ? '<img src="'. $user->avatar.'" width="70%" />' : '-';
            $data['status'] = ucfirst(config('constants.STATUS.' . $user->status));
            $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->status == 'active' ? ' checked' : '') . ' data-id="' . $user->id . '" data-code="' . $user->name . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusUsers"></div></div>';
            $data['created_at'] =  date('d-m-Y', strtotime($user->created_at));
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('editUsers', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('viewUsers', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

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
        $url = route('addUsers');
        $roles = Role::pluck('name','name')->all();
       // $countrylist = Country::where("status","active")->get(['id',"name"]);
       
        $user = new User();
        // $user->gender = "Male";
        $user->nationality = '';
        return view('backend.users.create', compact('type', 'url', 'roles','user'));
    }

    public function checkUsers(Request $request, $id = null) {
        if (isset($request->user_email)) {
            $check = User::where('email', $request->user_email);
            if (isset($id) && $id != null) {
                $check = $check->where('id', '!=', $id);
            }
            $check = $check->where('status', '!=', 'delete')->count();
            if ($check > 0) {
                return "false";
            } else {
                return "true";
            }
        } else {
            if (isset($request->email)) {
                $check = User::where('email', $request->email);
                if (isset($id) && $id != null) {
                    $check = $check->where('id', '!=', $id);
                }
                $check = $check->where('status', '!=', 'delete')->count();
                if ($check > 0) {
                    return "false";
                } else {
                    return "true";
                }
            } else {
                return "true";
            }
        }
    }
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'name' => 'required',
            'user_image' => 'required|mimes:jpeg,png,jpg,gif,svg',
            'user_email' => 'required|email',
            'phone_number' => 'required|numeric',
            'phone_code' => 'required',

        ]);
        $attr = [
            'name' => 'First Name',
            'user_image' => 'Image',
            'user_email' => 'Email',
            'phone_number' => 'Phone no',
            'password' => 'Password',
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('createUsers')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $checkedPhone = User::where("mobile",$request->phone_number)->where("phone_code",$request->phone_code)->where("status","!=","delete")->first();
                if($checkedPhone)
                {
                    $request->session()->flash('error', 'Phone number already exists');
                    return redirect()->back();
                }
                $user = new User;
                $filename = "";
                if ($request->hasfile('user_image')) {
                    $file = $request->file('user_image');
                    $filename = time() . $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);
                    $filename = str_replace('.jpeg', '.jpg', $filename);
                    $file->move(public_path('img/avatars'), $filename);
                }
                if ($filename != "") {
                    $user->avatar = $filename;
                }
                $password = $this->generateReferralCode(1);
                $user->name = ucfirst($request->name);
                $user->email = $request->user_email;
                $user->mobile = $request->phone_number;
                $user->phone_code = $request->phone_code;
                // $user->contact_person_name = $request->contact_person_name;
                // $user->address = $request->address;
                $user->password = Hash::make($password);
                $user->is_admin = 'No';
                $user->created_at = date('Y-m-d H:i:s');
                $user->updated_at = date('Y-m-d H:i:s');

                // dd($user);
                if ($user->save()) {
                    $getEmailTemplate = EmailTemplate::where('id', 2)->where('status',"active")->select(['name', 'subject', 'description', 'footer'])->first();
                    if($getEmailTemplate)
                    {
                     $arr  =$getEmailTemplate->toArray();
                     $link = "<a href='".url('/')."'>Click Here</a>";
                       // dd($arr['description']);
                        $subject = $getEmailTemplate->subject;
                        $subject = str_replace("{subject}", "Welcome", $subject);
                        $record = (object)[];
                        $desc = $arr['description'];
                        $desc = str_replace("{name}",$input['name'],$desc);
                        $desc = str_replace("{phone_code}",'+'.$input['phone_code'],$desc);
                        $desc = str_replace("{mobile}",$input['phone_number'],$desc);
                        $desc = str_replace("{email}",$input['user_email'],$desc);
                        $desc = str_replace("{password}",$password,$desc);
                        // $desc = str_replace("{link}",$link,$desc);
                       
                        $record->description= $desc;
                        $record->footer = $getEmailTemplate->footer;
                        $record->subject = $subject;
                        //if (!filter_var($input['user_email'], FILTER_VALIDATE_EMAIL)) {
                            if ($input['user_email'] != '') {
                                Mail::send('emails.welcome', compact('record'), function ($message) use ($input, $subject) {
                                    $message->to($input['user_email'], config('app.name'))->subject($subject);
                                    $message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
                                });
                            }
                        //}
                    }
                        
                    $user->assignRole($request->post('roles'));
                    $request->session()->flash('success', 'User added successfully');
                    return redirect()->route('users');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('users');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('users');
            }

        }
    }

    public function show(Request $request, $id = null) {
        $type = 'View';
        if (isset($id) && $id != null) {
            $user = User::where('id', $id)->first();
            if(isset($user) && $user->marital_status=='inrelationship')
            {
                $user->marital_status ='In Relationship';
            }

            $amount = Transaction::where('user_id',$user->id)->sum('amount');

            $totalPurchases = BoxPurchase::where('user_id', $user->id)->count();

            if (isset($user->id)) {
                return view('backend.users.view', compact('user', 'type' ,'amount','totalPurchases'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('users');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('users');
        }
    }

    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            $user = User::where('id', $id)->first();
          //  $CountryCode =Country::select('shortname')->where("phonecode",$user->phone_code)->first();
            
            if (isset($user->id)) {
              //  $user->country_flag = $CountryCode->shortname ??'IL';
            
                $type = 'edit';
                $url = route('updateUsers', ['id' => $user->id]);
                $roles = Role::pluck('name','name')->all();
                $userRole = $user->roles->pluck('name','name')->all();
               
                // $countrylist = Country::where("status","active")->get(['id',"name"]);
                 $statelist = State::where("status","active")->where("country_id",$user->country_id)->get(['id',"name"]);
                 $cityList = City::where("state_id",$user->state_id)->get(['id',"name"]);
                 $regionList = Region::where("city_id",$user->city_id)->get(['id',"name"]);
               
                return view('backend.users.create', compact('user', 'type', 'url', 'roles', 'userRole','statelist','cityList','regionList'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('users');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('users');
        }
    }

    public function update(Request $request, $id = null) {
        if (isset($id) && $id != null) {

            $user = User::where('id', $id)->first();
            if($checkedMail = User::where("id","!=", $id)->where("email", $request->user_email)->first())
            {
                 $request->session()->flash('error', 'Email address already exists');
                 return redirect()->back();
            }
            if($checkedPhone = User::where("status","!=","delete")->where("mobile",$request->phone_number)->where("phone_code",$request->phone_code)->where("id","!=",$id)->first())
            {
                    $request->session()->flash('error', 'Phone number already exists');
                return redirect()->back();
            }

            if (isset($user->id)) {
                $validate = Validator($request->all(),  [
                    'name' => 'required',
                    'user_email' => 'required|email',
                    'phone_number' => 'required|min:8|numeric',
                ]);
                $attr = [
                    'name' => 'First Name',
                    'user_email' => 'Email',
                    'phone_number' => 'Mobile',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editUsers', ['id' => $user->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $filename = "";
                        if ($request->hasfile('user_image')) {
                            $file = $request->file('user_image');
                            $filename = time() . $file->getClientOriginalName();
                            $filename = str_replace(' ', '', $filename);
                            $filename = str_replace('.jpeg', '.jpg', $filename);
                            $file->move(public_path('img/avatars'), $filename);
                            if ($user->avatar != null && file_exists(public_path('img/avatars/' . $user->avatar))) {
                                if ($user->avatar != 'noimage.jpg') {
                                    unlink(public_path('img/avatars/' . $user->avatar));
                                }
                            }
                        }
                        if ($filename != "") {
                            $user->avatar = $filename;
                        }
                        $user->name = $request->post('name');
                        $user->email = $request->user_email;    
                        // $user->contact_person_name = $request->contact_person_name;
                        // $user->address = $request->address;
                        $user->mobile = $request->phone_number;
                        $user->phone_code = $request->phone_code;
                        $user->status = $request->status; 
                        $user->updated_at = date('Y-m-d H:i:s');
                        
                       
                        if ($user->save()) {
                            // DB::table('model_has_roles')->where('model_id',$id)->delete();
                            //$user->assignRole($request->post('roles'));
                            $request->session()->flash('success', 'User updated successfully');
                            return redirect()->route('users');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('editUsers', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('editUsers', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('editUsers', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('editUsers', ['id' => $id]);
        }

    }
    // activate/deactivate user
    public function updateStatus(Request $request) {

        if (isset($request->statusid) && $request->statusid != null) {
            $user = User::find($request->statusid);

            if (isset($user->id)) {
                $user->status = $request->status;
                if ($user->save()) {
                    $request->session()->flash('success', 'User updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update user. Please try again later.');
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
            $user = User::find($request->statusid);

            if (isset($user->id)) {
                $user->status = $request->status;
                if ($user->save()) {
                    echo json_encode(['status' => 1, 'message' => 'customer Status updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update user. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid user']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid user']);
        }
    }
    public function destroy(Request $request) {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $user = User::find($request->deleteid);

            if (isset($user->id)) {
                $user->status = 'delete';
                if ($user->save()) {
                    DB::table('model_has_roles')->where('model_id',$user->id)->delete();
                    echo json_encode(["status" => 1, 'ids' => json_encode($request->deleteid), 'message' => 'customers deleted successfully.']);
                } else {
                    echo json_encode(["status" => 0, 'message' => 'Not all users were deleted. Please try again later.']);
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
                $user = User::find($id);

                if (isset($user->id)) {
                    $user->status = 'delete';
                    if ($user->save()) {
                        DB::table('model_has_roles')->where('model_id',$user->id)->delete();
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'customers deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all users were deleted. Please try again later.']);
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
                $user = User::find($id);

                if (isset($user->id)) {
                    if ($user->status == 'active') {
                        $user->status = 'inactive';
                    } elseif ($user->status == 'inactive') {
                        $user->status = 'active';
                    }

                    if ($user->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'customers status updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all users were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

 

}