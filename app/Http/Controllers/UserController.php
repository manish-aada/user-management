<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{


	public function store(Request $request){
	
		$validator = Validator::make($request->all(), [
			'name' => 'required|string',
			'email' => 'required|email|unique:users,email',
			'phone' => 'required|regex:/^[6-9]\d{9}$/',
			'description' => 'nullable',
			'role_id' => 'required|exists:roles,id',
			'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		$data = $request->all();

		if ($request->hasFile('profile_image')) {
			$data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
		}

		$user = User::create($data);

		return response()->json(['message' => 'User created successfully!', 'user' => $user->load('role')], 201);
	}

	public function index(Request $request){
	
		$searchTerm = $request->input('search');

		$users = User::when($searchTerm, function ($query, $searchTerm) {
				return $query->where('name', 'like', "%$searchTerm%")
							 ->orWhere('email', 'like', "%$searchTerm%")
							 ->orWhere('phone', 'like', "%$searchTerm%");
			})
			->with('role')
			->paginate(4);
		return response()->json($users);
	}

	public function home(){
		$roles = Role::all(); // Fetch all roles
		return view('home', compact('roles'));
	}

}
