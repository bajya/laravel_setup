<?php
    
namespace App\Http\Controllers\Backend;
    
use App\Library\Helper;
use App\Library\Notify;    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Push;
use App\PushNotificationUser;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Mail;

use Illuminate\Support\Arr;
    
class PushController extends Controller
{
    public $push;
    public $columns;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->push = new Push;
        $this->columns = [
            "select", "title", "description", "created_at", "status", "activate", "action",
        ];
        $this->middleware('permission:push-list|push-create', ['only' => ['index','store']]);
        $this->middleware('permission:push-create', ['only' => ['create','store']]);
    }

    public function index(Request $request) {
        $users = User::all()->where('status', 'active')->where('id', '!=', 1)->where('role', 'User')->pluck('name', 'id');
        // dd($users);
        $vendors = User::all()->where('status', 'active')->where('id', '!=', 1)->where('role', 'Vendor')->pluck('name', 'id');
       
        return view('backend.pushs.index', compact('users','vendors'));
    }

    public function pushsAjax(Request $request) {
        if (isset($request->search['value'])) {
            $request->search = $request->search['value'];
        }else{
            $request->search = '';
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->push->fetchPushs($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $pushs = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $pushs = $records->offset($request->start)->limit(count($total))->get();
        }

        //  $usertype = PushNotificationUser::with('usertype')
		// 				->orderBy('id','desc')
        //           		->get();

        $result = [];
        $i = 1;
        foreach ($pushs as $push) {
            $data = [];
            $data['sno'] = $i++;

            // $usertypes = $usertype->where('push_id', $push->id)->first();
            // // dd($usertypes);
            // $data['user_type'] = $usertypes->usertype->user_type ?? 'N/A';

            // $data['user_type'] = $push->user_type;
            $data['title'] = (ucfirst($push->title) != null) ? \Str::limit(ucfirst($push->title), 50, '...') : '-';
            $data['description'] = (ucfirst($push->description) != null) ? \Str::limit(ucfirst($push->description), 50, '...') : '-'; 
            $data['is_send'] = ucfirst(config('constants.CONFIRM.' . $push->is_send)); 
            $data['created_at'] = date('d-m-Y H:i:s', strtotime($push->created_at));
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';
            $action .= '<a href="' . route('viewPushs', ['id' => $push->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
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
    public function create() {

    }
    public function store(Request $request) {
        $validate = Validator($request->all(), [
            'title' => 'required',
            // 'users.*' => 'required|integer',
            'description' => 'required',
        ]);

        $attr = [
            'title' => 'Title',
            // 'users.*' => 'Title',
            'description' => 'Description',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->back()->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $push = new Push;
                $push->title = ucfirst($request->title);
                $push->description = ucfirst($request->description);
                $push->sender_id = 1;
                $push->status = 'active';
                $push->created_at = date('Y-m-d H:i:s');
                $push->is_send = 0;
                if ($push->save()) {
                    if (!empty($request->users)) {
                        foreach ($request->users as $key => $value) {
                            $push_user = new PushNotificationUser;
                            $push_user->push_id = $push->id;
                            $push_user->user_id = $value;
                            $push_user->user_type = $request->user_type;
                            $push_user->save();
                            $userEmail = $push_user->user->email;
                            // $this->sendMail($request->title,$request->description,$userEmail);
                        }
                    }
                    $request->session()->flash('success', 'Push added successfully');
                    return redirect()->route('pushs');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('pushs');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('pushs');
            }

        }
    }
    public function show(Request $request, $id = null) {
        $type = 'View';
        if (isset($id) && $id != null) {
            $push = Push::where('id', $id)->first();
            if (isset($push->id)) {
                return view('backend.pushs.view', compact('push', 'type'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('pushs');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('pushs');
        }
    }
    public function edit(Request $request, $id = null) {
        
    }
    public function update(Request $request, $id = null) {
        

    }
    public function sendMail($subject,$description,$email){
          $record = (object)[];
            $record->description= $description;
            $record->subject = $subject;
             Mail::send('emails.pushNotification', compact('record'), function ($message) use ($email, $subject) {
                $message->to($email, config('app.name'))->subject($subject);
                $message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
            });
    }
}