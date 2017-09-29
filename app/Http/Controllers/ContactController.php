<?php 
namespace App\Http\Controllers;
use App\Contact;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ContactController extends Controller{
	public function __construct(){
		$this->middleware('oauth', ['except' => ['store']]);
		$this->middleware('authorize:' . __CLASS__, ['except' => ['store','update']]);
	}

	public function store(Request $request){
		$conArray = [];
		//echo $request->all()['sitId']."<br>";
		//print_r($request->all()['contact']);
		$user = User::find($request->all()['sitId']);
		if($user!=null)
		{
			
			//print_r($request->all()->contact);
			print_r($request->all()['contact']);
			foreach(json_encode(json_decode($request->all()['contact'])) as $value) {
			    foreach($value->phoneNumbers as $contactPhone){
					// replace space and +
					$valueOfConMob = str_replace(' ','',$contactPhone->value);
					$valueOfConMob = str_replace('+','',$contactPhone->value);
					// check contact number associated with any user or not
					$conIsUser = User::where('mobile', '=', "+".$valueOfConMob)->first();
					if(sizeof($conIsUser)<1 || $conIsUser==null)
					{
						
						$data = array(
							'login'=>1000000001+sizeof(User::all()),
							'email' => 'createfromcontact@loreum.com',
							'password'=> Hash::make(uniqid(rand(99999,32876623), true)),
							'mobile'=>'+'.$valueOfConMob
						);
						// create user for contact number
						$userToCreate = User::create($data);
						if($userToCreate!=null){
							array_push($conArray,array("parent_id"=>$request->all()['sitId'],
								"usershare_phone"=>$user->mobile,"user_id"=>$userToCreate->id,
								"name"=>$value->displayName,"type"=>$contactPhone->type,
								"phone"=>'+'.$valueOfConMob)
							);
						}
						else{
							return $this->error("Not able to import Contact",401);
						}
					}
					else{
						array_push($conArray,array("parent_id"=>$request->all()['sitId'],
							"usershare_phone"=>$user->mobile,"user_id"=>$conIsUser->id,
							"name"=>$value->displayName,"type"=>$contactPhone->type,
							"phone"=>'+'.$valueOfConMob)
						);
					}
				}
			}
			if(sizeof($conArray)>0){
				$contact = Contact::insert($conArray);
			}
			return $this->success("success", 201);
		}
		else{
			return $this->error("User Not Registerd",401);
		}
	}

	public function update(Request $request){
		// find current user Id
		//print_r($request->all()['contactToShare']);
		$user = User::find(Controller::getUserId());
		if(!$user){
			return $this->error("You are not authorize", 404);
		}
		foreach(json_decode($request->all()['contactToShare']) as $conToUpdate){
			$userOfCon = User::find($conToUpdate->user_id);
			$userOfCon = json_decode(($userOfCon),true);
			if($userOfCon!=null){
				$conOfCon = User::find($userOfCon['id'])->contacts;
				$conOfCon = json_decode(($conOfCon),true);
				if(sizeOf($conOfCon)>0 && $conOfCon!=null){
					foreach ($conOfCon as $con) {
						if($con['user_id']==$user->id && $con['parent_id']=$userOfCon['id']){
							//print_r($con['id']);
							$conObj = Contact::find($conToUpdate->id);
							//print_r($conToUpdate['id']);
							if($conObj!=null){
								// save user number to their contact phonebook
								// echo $user->mobile;
								// print_r($conObj->id);
								$conObj->usershare_phone = $user->mobile;
								$conObj->save();
							}
							// save user new number to their contacts shared number
							$userToUpdateContacts = Contact::find($con['id']);
							// print_r($userToUpdateContacts->id);
							// echo $user->mobile;
							$userToUpdateContacts->phone = $user->mobile;
							$userToUpdateContacts->save();
						}
					}
				}
			}
		}
		
		return 1;
	}

// validate incoming request
	public function validateRequest(Request $request){
		$rules = [
			'email' => 'required|email|unique:users', 
			'password' => 'required|min:6'
		];
		$this->validate($request, $rules);
	}

// authorize request
	public function isAuthorized(Request $request){
		$resource = "contacts";
		//$contact     = Contact::find();
		return $this->authorizeUser($request, $resource);
	}
}