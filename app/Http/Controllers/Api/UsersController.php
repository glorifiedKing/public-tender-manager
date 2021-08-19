<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['revoke_tokens']);
    }

    /**
     * @OA\Post(
     * path="/user/register",
     * summary="Register",
     * description="Register to begin using the platform",
     * 
     * tags={"user"},
     * @OA\RequestBody(
     *    required=true,
     *    description="User Details",
     *    @OA\JsonContent(
     *       required={"email","password","name"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *      @OA\Property(property="name", type="string", example="John Doe"),
     *       
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Invalid Data Response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="the given data was invalid"),
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(
     *           property="errors",
     *           type="object",
     *           @OA\Property(
     *              property="email",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The email field is required.","The email must be a valid email address.","The email has already been taken."},
     *              )
     *           ),
     *           @OA\Property(
     *              property="password",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The password field is required.","The password must be at least 6 characters."},
     *              )
     *           ),
     *           @OA\Property(
     *              property="name",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The name field is required.","The name must be at least 6 characters."},
     *              )
     *           ),   
     *        )
     *      )
     *   ),
     *  @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Successfully created account. You can now request for token"),
     *              @OA\Property(property="status", type="string", example="success"),
     *          )
     *       ),
     * )
     */
    public function register (Request $request)
    {
        $validator = Validator::make($request->all(),[
			'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',            
            'name' => 'required|min:6',
            
			
		]);
		if ($validator->fails()) {
			
			return response()->json([
				"message" => "the given data was invalid",
				"errors" => $validator->messages()->toArray(),
				"status" => "error"
			],422);
        }

        try {
            
            $user = new User;
            $user->email = $request->email;
            $user->name = $request->name;
            $user->password = Hash::make($request->password);           
            $user->save();
            
            return response()->json([
				"message" => "Successfully created account. You can now request for token",				
				"status" => "success"
			],200);


        }catch(\Exception $e)
        {
            Log::debug(["api user registration error" => $e->getMessage()]); 
            return response()->json([
				"message" => "Technical error",
				"errors" => $e->getMessage(),
				"status" => "error"
			],500);
        }
        


    }

    /**
     * @OA\Post(
     * path="/user/getToken",
     * summary="Get Token",
     * description="Get Token to use on api by supplying email, password",
     * 
     * tags={"user"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="the given data was invalid"),
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(
     *           property="errors",
     *           type="object",
     *           @OA\Property(
     *              property="email",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The email field is required.","The email must be a valid email address."},
     *              )
     *           )
     *        )
     *      )
     *   ),
     *  @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="successfully created token"),
     *              @OA\Property(property="status", type="string", example="success"),
     *          )
     *       ),
     * )
     */
    public function authenticate(Request $request)
    {       

        $validator = Validator::make($request->all(),[
			'email' => 'required|email',
            'password' => 'required',
            
			
		]);
		if ($validator->fails()) {
			
			return response()->json([
				"message" => "the given data was invalid",
				"errors" => $validator->messages()->toArray(),
				"status" => "error"
			],422);
        }

        

        $user = User::query()
            ->where('email', $request->email) ->first();

        if(!$user || !Hash::check($request->password, $user->password)){

            throw ValidationException::withMessages([
                'email' => ['Wrong email or password.'],
            ]);

        }       

        $accessToken = $user->createToken("default_access")->plainTextToken;      
        
        return response()->json([
            "message" => "Successfully created token",
            "token" => $accessToken,				
            "status" => "success"
        ],200);
    }


    /**
     * @OA\Post(
     * path="/user/logout",
     * summary="Logout",
     * description="Logout user and invalidate token",
     * 
     * tags={"user"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(        
     *         @OA\Property(property="message", type="string", example="Successfully revoked tokens"),
     *         @OA\Property(property="status", type="string", example="success"),
     *       )       
     *  ),
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not authorized"),
     *    )
     *  ) 
     * )
     */
    public function revoke_tokens()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully revoked tokens',
            'status' => 'success'
        ], 200);
    }


    
}
