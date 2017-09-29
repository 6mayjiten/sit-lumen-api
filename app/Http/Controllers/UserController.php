<?php 
namespace App\Http\Controllers;
use App\User;
use App\Contact;
use App\SIT\SendSMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller{
	public function __construct(){
		$this->middleware('oauth', ['except' => ['store']]);
		$this->middleware('authorize:' . __CLASS__, ['except' => ['store','show']]);
	}

	public function store(Request $request){
		$this->validateRequest($request);
		//$users = User::all();  get all user
		$data = array(
					'login'=>1000000001+sizeof(User::all()),
					'email' => $request->get('email'),
					'password'=> Hash::make($request->get('password')),
					'mobile'=>'+'.str_replace(' ','',$request->get('mobile'))
				);
		// /$this->validateRequest($data);
		$user = User::create($data);
		if($user!=null){
			$sendMsg = new SendSMS();
			$msgToUser = "Welcome to SIT. Your username is $user->login";
			try{
				$sendMsg->sendMsg($user->mobile,$msgToUser);
			}
			catch(exception $e){
				print_r($e);
			}
			$contact = Contact::create([
						'parent_id'=>$user->id,
						"user_id"=>$user->id,
						"usershare_phone"=>'+'.str_replace(' ','',$request->get('mobile')),
						"phone"=>'+'.str_replace(' ','',$request->get('mobile'))
					]);
			return $user->id;
		}
		else{
			return null;
		}
	}

	public function show(Request $request){
		$user = User::find(Controller::getUserId());
		//return $user;
		return $user::with('contacts')->find(Controller::getUserId());
	}

	public function update(Request $request){
		$user = User::find(Controller::getUserId());
		$duplicatConChk = [];
		$duplicatNumChk = [];	
		if(!$user){
			return $this->error("The user with {$id} doesn't exist", 404);
		}
		$this->validateUpdateRequest($request);
		$user->email 	= $request->get('email');
		$user->name 	= $request->get('name');
		//$user->mobile 	= '+'.str_replace(' ','',$request->get('mobile'));
		$contact  = Contact::where(["user_id"=>$user->id,"parent_id"=>$user->id])->get();
		//print_r(sizeOf($contact));
		foreach(json_decode($request->get('mobile')) as $userNumber){
			$usernum = str_replace('+','',$userNumber->mobile);
			//print_r($usernum);
			if($userNumber->isPrimaryNumber){
				$user->mobile = "+".$usernum;
				if(sizeOf($contact)>0){
					foreach($contact as $con){
						if(!in_array($con->id,$duplicatConChk) && !in_array($userNumber->mobile,$duplicatNumChk) ){
							// print_r(" is pri ==> not new ");
							// print_r($con->phone);
							array_push($duplicatNumChk,$userNumber->mobile);
							$con->phone = "+".$usernum;
							$con->name = $user->name;
							// print_r(" ==>".$con->phone."  ==>".$con->id. " ");
							array_push($duplicatConChk,$con->id);
							$con->save();
						}
					}
				}
				else{
					// print_r(" is pri ==> new ");
					$con = new Contact();
					$con->name = $user->name;
					$con->user_id = $user->id;
					$con->parent_id = $user->id;
					$con->phone =  "+".$usernum;
					$con->save();
					array_push($duplicatNumChk,$userNumber->mobile);
				}
			}
			else{
				if(sizeOf($contact)>0){
					foreach($contact as $con){
						if(!in_array($con->id,$duplicatConChk) && !in_array($userNumber->mobile,$duplicatNumChk)){
							// print_r(" no pri ==> not new");
							// print_r($con->phone);
							array_push($duplicatNumChk,$userNumber->mobile);
							$con->phone =  "+".$usernum;
							$con->name = $user->name;
							$con->save();
							array_push($duplicatConChk,$con->id);
							// print_r(" ==>".$con->phone."  ==>".$con->id. " ");
						}
					}
				}
				else{
					// print_r(" no pri ==> new");
					$con = new Contact();
					$con->name = $user->name;
					$con->user_id = $user->id;
					$con->parent_id = $user->id;
					$con->phone =  "+".$usernum;
					$con->save();
					array_push($duplicatNumChk,$userNumber->mobile);
				}
			}
			if(!in_array($userNumber->mobile,$duplicatNumChk)){
				// print_r(" nottt ==> not new");
				$con = new Contact();
				$con->name = $user->name;
				$con->user_id = $user->id;
				$con->parent_id = $user->id;
				$con->phone =  "+".$usernum;
				$con->save();
				array_push($duplicatNumChk,$userNumber->mobile);
			}
		}
		$user->save();
		return 1;
	}

	// validate request
	public function validateRequest(Request $request){
		$rules = [
			'mobile'=>'required|min:11|unique:users',
			'email' => 'required|email', 
			'password' => 'required|min:6'

		];
		$this->validate($request, $rules);
	}
	// validate update request
	public function validateUpdateRequest(Request $request){
		$rules = [
			'mobile'=>'required',
			'name' => 'required', 
			'email' => 'required|email'

		];
		$this->validate($request, $rules);
	}
	// chek authorize user	
	public function isAuthorized(Request $request){
		$resource = "users";
	    //$user     = User::find($this->getArgs($request)["user_id"]);
		$user     = User::find(Controller::getUserId());
		return $this->authorizeUser($request, $resource,$user);
	}
}