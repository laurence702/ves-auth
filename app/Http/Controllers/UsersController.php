<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Http\Requests\createUserRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $allUsers = User::all();
        } catch (Exception $e) {
            throw new Exception('something went wrong');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = new User(
                [
                    'first_name' => $request->get('first_name'),
                    'last_name' => $request->get('last_name'),
                    'email' => $request->get('email'),
                    'password' => Hash::make($request->get('password')),
                ]
            );
            if (!$user->save()) {
                Log::error(
                    'failed to create account',
                    [
                        'payload' => $request->except('password'),
                    ]
                );
                throw new Exception();
            }
            //Add event here
            return response()->json([
                'message' => 'Success',
                'data'=> User::latest()->first(),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            if($e instanceof QueryException) {
                return response()->json([
                    'message' => 'email already taken',
                    'meta'=> $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return response()->json([
                'message' => 'Failed to register',
                'meta'=> $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }
        
        $userInfo = User::where('email',$request->only(['email']))->first();
        $token = $request->user()->createToken('authorize');

        return new JsonResponse(
            [
                'data' => [
                    'token' => $token->plainTextToken,
                    'userInfo' => $userInfo
                ],
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
