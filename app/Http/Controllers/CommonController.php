<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Event;
use App\Models\GivenPermission;
use App\Models\Manager;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Dirape\Token\Token;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class CommonController extends Controller
{
    public function register(Request $request)
    {
        $rules =array(
            "name" => "required",
            "company_name" => "required",
            "email " => "required",
            "contact" => "required",
            "event_id" => "required",
            "role_id" => "required",
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            return QrCode::generate("Fuck wdicj wcn");
            if(User::where('event_id',$request->event_id)->where('contact',$request->contact)->first()){
                return response(["status" =>"failed", "message"=>"User is Already Registered For Event"], 401);
            }
            $user = new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->company_name=$request->company_name;
            $user->contact=$request->contact;
            $user->event_id=$request->event_id;
            $user->role_id=$request->role_id;
            $token_qr = (new Token())->Unique('users', 'qr_token', 20);
            $user->qr_token = $token_qr;
            // $qr  = QrCode::format('png')->generate($token_qr);
            // $file =  $qr->store('public/event/qr');
            // $user->qr_link = $file;
            $result= $user->save();
            // if(isset($role_id)){
            //     if(Permission::where('role_id',$role_id)->first()){
            //         $permissions = Permission::where('role_id',$role_id)->get();
            //         foreach($permissions as $permissions){
            //         $temp = new GivenPermission();
            //         $temp->permission_id = $permissions->id;
            //         $temp->user_id = $user->id;
            //         $temp->save();
            //     }
            //     }
            // }
            if ($result) {
                $response = [
                'message' => 'User created successfully',
                'user' => $user,
            ];
                return response($response, 201);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function registerBulk(Request $request)
    {
        $rules =array(
            "file" => "required",
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $file = $_FILES["file"]["tmp_name"];
            $file_open = fopen($file,"r");
            $pass = 1;
            while (($csv = fgetcsv($file_open, 1000, ",")) !== false) {
                if($pass == 1){
                    $pass = 0;
                    continue;
                }
                $name = $csv[0];
                $company_name = $csv[1];
                $contact = $csv[2];
                $event_id = $csv[3];
                $role_id = $csv[4];
                $email = $csv[5];

                if(User::where('event_id',$event_id)->where('contact',$contact)->first()){
                    return response(["status" =>"failed", "message"=>"User is Already Registered For Event", "user_name"=>$name], 401);
                }
                $user = new User();
                $user->name=$name;
                $user->email=$email;
                $user->company_name=$company_name;
                $user->contact=$contact;
                $user->event_id=$event_id;
                $user->role_id=$role_id;
                $token_qr = (new Token())->Unique('users', 'qr_token', 20);
                $user->qr_token = $token_qr;
                // $qr  = QrCode::format('png')->generate($token_qr);
                // $file =  $qr->store('public/event/qr');
                // $user->qr_link = $file;
                $result= $user->save();
                // if(isset($role_id)){
                //     if(Permission::where('role_id',$role_id)->first()){
                //         $permissions = Permission::where('role_id',$role_id)->get();
                //         foreach($permissions as $permissions){
                //         $temp = new GivenPermission();
                //         $temp->permission_id = $permissions->id;
                //         $temp->user_id = $user->id;
                //         $temp->save();
                //     }
                //     }
                // }
               
            }
           
            if (true) {
                $response = [
                'message' => 'User created successfully',
            ];
                return response($response, 201);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function registerManagers(Request $request)
    {
        $rules =array(
            "name" => "required",
            "email" => "required",
            "contact" => "required",
            "event_id" => "required",
            "user_type" => "required|in:agent,operator",
            "password" => "required|min:6"            
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(Manager::where('event_id',$request->event_id)->where('email',$request->email)->first()){
                return response(["status" =>"failed", "message"=>"Operator Or Agent is Already Registered For Event"], 401);
            }
            $user = new Manager();
            $user->name=$request->name;
            $user->contact=$request->contact;
            $user->email=$request->email;
            $user->event_id=$request->event_id;
            $user->user_type=$request->user_type;
            $user->password=Hash::make($request->password);
            $token_qr = (new Token())->Unique('managers', 'user_token', 60);
            $user->user_token = $token_qr;
            $result= $user->save();
            if ($result) {
                $response = [
                'message' => 'Agent/Operator created successfully',
                'user' => $user,
            ];
                return response($response, 200);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function registerAdmins(Request $request)
    {
        $rules =array(
            "name" => "required",
            "email" => "required",
            "contact" => "required",
            "password" => "required|min:6"            
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(Admin::where('email',$request->email)->first()){
                return response(["status" =>"failed", "message"=>"Operator Or Agent is Already Registered For Event"], 401);
            }
            $user = new Admin();
            $user->name=$request->name;
            $user->contact=$request->contact;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $token_qr = (new Token())->Unique('managers', 'user_token', 60);
            $user->user_token = $token_qr;
            $result= $user->save();
            if ($result) {
                $response = [
                'message' => 'Admin created successfully',
                'user' => $user,
            ];
                return response($response, 200);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function events(Request $request)
    {
        $rules =array(
            "event_name" => "required",
            "description" => "required",
            "start" => "required",
            "end" => "required",           
            "location" => "required",           
            "token" => "required"            
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(!Admin::where('user_token',$request->token)->first()){
                return response(["status" =>"failed", "message"=>"Invaild Token"], 401);
            }
            $user = Admin::where('user_token',$request->token)->first();
            $event = new Event();
            $event->event_name = $request->event_name;
            $event->description = $request->description;
            $event->start = $request->start;
            $event->end = $request->end;
            $event->location = $request->location;
            $event->admin_id = $user->id;
            $result= $event->save();
            if ($result) {
                $response = [
                'message' => 'Event created successfully',
                'Event-Details' => $event,
            ];
                return response($response, 200);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function roles(Request $request)
    {
        $rules =array(
            "role" => "required",
            "event_id" => "required",
            "token" => "required"            
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(!Admin::where('user_token',$request->token)->first()){
                return response(["status" =>"failed", "message"=>"Invaild Token"], 401);
            }
            $event = new Role();
            $event->role = $request->role;
            $event->event_id = $request->event_id;
            $result= $event->save();
            if ($result) {
                $response = [
                'message' => 'Event Role created successfully',
                'Role-Details' => $event,
            ];
                return response($response, 200);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function permissions(Request $request)
    {
        $rules =array(
            "role_id" => "required",
            "event_id" => "required",
            "name" => "required",
            "token" => "required"            
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(!Admin::where('user_token',$request->token)->first()){
                return response(["status" =>"failed", "message"=>"Invaild Token"], 401);
            }
            $event = new Permission();
            $event->role_id = $request->role_id;
            $event->event_id = $request->event_id;
            $event->name = $request->name;
            $result= $event->save();
            if ($result) {
                $response = [
                'message' => 'Event Role created successfully',
                'Role-Permission' => $event,
            ];
                return response($response, 200);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function QrDetails(Request $request)
    {
        $rules =array(           
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(!User::where('qr_token', $request->id)->first()){
                return response(["status" =>"failed", "message"=>"Invalid Scan Code"], 401);
            }
            $qr = User::where('qr_token', $request->id)->first();
            $role = Role::where('id',$qr->role_id)->first();
            $event = Event::where('id',$qr->event_id)->first();
            $permissions =  Permission::where('role_id',$qr->role_id)->where('event_id',$qr->event_id)->get();
            // $d[]= "";
            foreach($permissions as $permission){
                if(GivenPermission::where('user_id',$qr->id)->where('permission_id',$permission->id)->where('is_completed','1')->first()){
                    $visited = GivenPermission::where('user_id',$qr->id)->where('permission_id',$permission->id)->where('is_completed','1')->first();
                    $p_name = Permission::where('id',$visited->permission_id)->first();
                    $d[] = array(
                        'given_permission_id' => $p_name->id,
                        'visited' => $p_name->name,
                    );
                }
            }
            if(isset($d)){
            }
            else{
                $d = "Not Visited Any Where";
            }

            $data[]= array(
                "name" => $qr->name,
                "comapany_name" => $qr->company_name,
                "contact" => $qr->contact,
                "event" => $event->event_name,
                "role" => $role->role,
                "given_permission_to_role" => $permissions,
                "permissions_visted" => $d
            );
            if (true) {
                $response = [
                'message' => 'User Fetched',
                'data' => $data,
            ];
                return response($response, 200);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
    public function QrUpdate(Request $request)
    {
        $rules =array(
            "qr_token" => "required",
            "is_visited" => "required",
            "given_permission_id" => "required"        
                );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(!User::where('qr_token', $request->qr_token)->first()){
                return response(["status" =>"failed", "message"=>"Invalid Scan Code"], 401);
            }
            $qr = User::where('qr_token', $request->qr_token)->first();
            if(!Permission::where('role_id',$qr->role_id)->where('event_id',$qr->event_id)->where('id',$request->given_permission_id)->first()){
                return response(["status" =>"failed", "message"=>"Invalid Ask For Permission"], 401);
            }
            if(GivenPermission::where('user_id',$qr->id)->where('permission_id',$request->given_permission_id)->first()){
                $temp = GivenPermission::where('user_id',$qr->id)->where('permission_id',$request->given_permission_id)->first();
            }
            else{
                $temp = new GivenPermission();
            }
            $temp->user_id = $qr->id;
            $temp->permission_id = $request->given_permission_id;
            $temp->is_completed = $request->is_visited;
            $temp->save();
            if ($temp->save()) {
                $response = [
                'message' => 'User Fetched Visited Permission Success',
            ];
                return response($response, 200);
            } else {
                return response(["status" =>"failed", "message"=>"User is not created"], 401);
            }
        }
    }
}