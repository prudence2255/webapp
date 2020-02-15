<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\RequestGuard;
use Session;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Image;
 

class UserController extends Controller
{


    public function __construct() {
        $this->middleware('isAdmin')->only([
            'makeAdmin', 'register', 'destroy', 'show', 'index'
        ]);
      $this->middleware('auth:api')->except(['login']);  
     
    }
    public $successStatus = 200;
/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(Request $request){ 
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
                'status' => 'error',
            ], 201);
        }
        $remember_me = $request->has('remember_me') ? true : false; 

        if(Auth::attempt(['email' => request('email'),
                         'password' => request('password')],
                         $remember_me)){ 
            $user = Auth::user(); 
            $token = $user->createToken('token')->accessToken; 
         
            return response([
                'data' => new UserResource($user), 
                'token' => $token,
                'status' => 'success',
            ],
             $this->successStatus); 
        } 
        else{ 
            return response([
                            'message'=>'Email or password invalid',
                             ], 201); 
        } 
    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'email' => 'required|email|unique:users', 
            'password' => 'required|min:8', 
            'c_password' => 'required|same:password',
            'user_img' => 'nullable|string'
        ]);
        if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
                'status' => 'error',
            ], 201);
        }
         $password = Hash::make($request->password); 
        $user = User::create([
            'name' => $request->name,
            'about' => $request->about,
            'email' => $request->email,
            'password' => $password,
            'user_img' => $request->user_img,
        ]); 
        $token =  $user->createToken('token')->accessToken; 
        
        if($user){
            return response([
                                'data' => new UserResource($user), 'token' => $token,
                                'message' => 'User created successfully',
                                'status' => 'success',
                            ],
             $this->successStatus);
        }
    }
/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function user_details() 
    { 
        $user = Auth::user(); 
        return response(['data' => new UserResource($user)], $this->successStatus);
    } 

    //fetch all users api
    public function index() {
        return  UsersResource::collection(User::paginate(10));
         
    }
    
    //show user api
    public function show(User $user) {
        return response(['data' => new UserResource($user)]);
    }

//update user api

    public function update(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required', 
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
        ]);
        if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
                'status' => 'error',
            ], 201);
        }
        $user = auth()->user();
        $user->update($data);
        return response()->json([
            'message' => 'User updated successfully',
            'data' => new UserResource($user),
            'status' => 'success',
        ],  $this->successStatus);
    }

    public function destroy(User $user) {
        if($user === auth()->user() || $user->role === 'admin'){
            return response(['message' => 'Logged in user or admin cannot be deleted'], 200);
        }
        $user->delete_user_img();
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully'
        ],  $this->successStatus);
    }

    public function makeAdmin(User $user) {
        $user->role = 'admin';
        $user->save();
        
        return response([
            'message' => 'User successfully made an admin'
        ], $this->successStatus);
        }

     public function logout() {
        auth()->user()->token()->revoke();
        Session::flush();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
     } 
     
     public function user_img(Request $request) {
         $validator = Validator::make($request->all(), [
            'user_img' => 'nullable|mimes:bmp,gif,jpeg,jpg,png',
         ]);
         if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
                'status' => 'error',
            ], 201);
        }
       $imgValue = $request->user_img;
       $imgExt = $imgValue->getClientOriginalExtension();
       $filename = 'user'.'_'.time().'.'.$imgExt;
       $filePath = public_path('storage/users/'.$filename);
       $this->createThumbnail(
        $imgValue->getRealPath(), $filePath, 300, 300
    );
      
       $url = url('storage/users/'.$filename);

       return response(['url' => $url]);
     }

     public function createThumbnail($requestPath, $path, $width, $height){
        $img = Image::make($requestPath)->resize($width, $height, function($constraint){
            $constraint->aspectRatio();
        });
        $img->save($path);
    }  

    public function updatePassword(Request $request)
                                 {
                            $validator = Validator::make($request->all(), [
                                'cur_password' => 'required|string|min:8',
                                'new_password' => 'required|string|min:8',
                                'c_password' => 'required|string|same:new_password',
                            ]);       
                            if($validator->fails()){
                                return response([
                                    'errors' => $validator->errors(),
                                    'status' => 'error',
                                ], 201);
                            }                          
                        $cur_password = $request->cur_password;
                         $new_password  = $request->new_password;
        
         if(!Hash::check($cur_password,Auth::user()->password)){
            return response(['message' => 'The old password does not match'], 201);
             }
        else{
            $request->user()->fill(['password' => Hash::make($new_password)])->save();
            return response([
                'message' => 'Password reset successfully',
                'status' => 'success',
            ], 201);

     }
    }
}

