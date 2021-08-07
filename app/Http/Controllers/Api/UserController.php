<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Transformers\PaginationTransformer;
use App\Http\Traits\RespondTrait;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    use RespondTrait;

    protected $userTransformer, $paginationTransformer;
    private $paginate = 10;

    public function __construct(UserTransformer $userTransformer, PaginationTransformer $paginationTransformer)
    {
        $this->userTransformer = $userTransformer;
        $this->paginationTransformer = $paginationTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginatedData = User::with('role:id,name')->paginate($request->input('per_page') ?? $this->paginate)->toArray();

        $users = $paginatedData['data'];
        unset($paginatedData['data']);
        $transformedData = $this->userTransformer
            ->transformCollection($users);

        return response()->json([
            'success' => true,
            'message' => __('app.get.success'),
            'data' => [
                'users' => $transformedData,
                'pagination' => $this->paginationTransformer->transform($paginatedData)
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => "required|string|in:user,manager,admin",
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6|max:255'
        ]);

        if ($validator->fails()) {
            return $this->respondNotValidated('', $validator->errors()->all());
        }

        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();

            $user = new User();
            $user['role_id'] = Role::where('name', $validatedData['user_type'])->first()->id;
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('app.create.success'),
                'data' => [
                    'user' => $user,
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollback();

            return $this->respondInternalError('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $user = User::where('id', $id)->with(['author'])->first();
        if (empty($user)) {
            return $this->respondNotFound('', __('app.user.not_found', ['attribute' => $id]));
        }

        return response()->json([
            'success' => true,
            'message' => __('app.get.success'),
            'data' => [
                'user' => $user
            ]
        ], 200);
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
        $user = User::where('id', $id)->first();

        if (empty($user)) {
            return $this->respondNotFound('', __('app.user.not_found'));
        }

        $validator = Validator::make($request->all(), [
            'user_type' => "required|string|in:user,manager,admin",
            'name' => 'required|min:3|max:255',
            'email' => 'required|max:255|unique:users,id,' . $id,
            'password' => 'required|min:6|max:255'
        ]);

        if ($validator->fails()) {
            return $this->respondNotValidated('', $validator->errors()->all());
        }

        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();

            $user = User::where('id', $id)->first();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = $validatedData['password'];
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('app.update.success'),
                'data' => [
                    'user' => $user
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollback();

            return $this->respondInternalError('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        if (empty($user)) {
            return $this->respondNotFound('', __('app.user.not_found'));
        }

        try {
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => __('app.delete.success'),
                'data' => [
                    'user' => []
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollback();

            return $this->respondInternalError('', $e->getMessage());
        }
    }
}
