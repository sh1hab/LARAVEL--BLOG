<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserTypes;
use App\Http\Controllers\Controller;
use App\Http\Traits\RespondTrait;
use App\Http\Traits\UploadTrait;
use App\Models\Post;
use App\Transformers\PaginationTransformer;
use App\Transformers\PostTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    use RespondTrait, UploadTrait;

    protected $postTransformer, $paginationTransformer;
    private $paginate = 10;

    public function __construct(PostTransformer $postTransformer, PaginationTransformer $paginationTransformer)
    {
        $this->postTransformer = $postTransformer;
        $this->paginationTransformer = $paginationTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = Post::with(['author', 'upload']);

        if(Auth::user()->role->name ==  UserTypes::user){
            $query = $query->createdBy(Auth::user()->id);
        }

        $paginatedData = $query->paginate($request->input('per_page') ?? $this->paginate)->toArray();
        $posts = $paginatedData['data'];
        unset($paginatedData['data']);
        $transformedData = $this->postTransformer
            ->transformCollection($posts);

        return response()->json([
            'success' => true,
            'message' => __('app.get.success'),
            'data' => [
                'posts' => $transformedData,
                'pagination' => $this->paginationTransformer->transform($paginatedData)
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:255|unique:posts,title',
            'content' => 'required|min:3',
            'slug' => 'required|min:3|max:255|unique:posts,slug',
            'post_image' => 'image|mimes:jpeg,png'
        ]);

        if ($validator->fails()) {
            return $this->respondNotValidated('', $validator->errors()->all());
        }

        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();

            $post = new Post();
            $post->title = $validatedData['title'];
            $post->content = $validatedData['content'];
            $post->slug = $validatedData['slug'];

            if ($request->hasFile('post_image')) {
                $image = $request->file('post_image');
                $name = time();
                $folder = '/uploads/images/post';
                $fileID = $this->uploadFile($image, $folder, 'public', $name, 'post_image');
                $post->upload_id = $fileID;
            }

            $post->save();

            $post = Post::where('id', $post->id)->with(['author', 'upload'])->first();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => __('app.create.success'),
                'data' => [
                    'post' => $post,
                ]
            ], 201);
        } catch (\Exception $e) {
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
        $post = Post::where('id', $id)->with(['author', 'upload'])->first();
        if (empty($post)) {
            return $this->respondNotFound('', __('app.post.not_found', ['attribute' => $id]));
        }

        if (Auth::user()->role->name == UserTypes::user && Auth::user()->id != $post->created_by) {
            return $this->respondNotAuthorized('', __('app.user.role.forbidden'));
        }

        return response()->json([
            'success' => true,
            'message' => __('app.get.success'),
            'data' => [
                'post' => $post
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $post = Post::where('id', $id)->with(['author', 'upload'])->first();

        if (empty($post)) {
            return $this->respondNotFound('', __('app.post.not_found', ['attribute' => $id]));
        }

        if (Auth::user()->role->name == UserTypes::user && Auth::user()->id != $post->created_by) {
            return $this->respondNotAuthorized('', __('app.user.role.forbidden'));
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:255|unique:posts,title,' . $id,
            'content' => 'required|min:3',
            'slug' => 'required|min:3|max:255|unique:posts,slug,' . $id,
            'post_image' => 'image|mimes:jpeg,png'
        ]);

        if ($validator->fails()) {
            return $this->respondNotValidated('', $validator->errors()->all());
        }

        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();

            $post->title = $validatedData['title'];
            $post->content = $validatedData['content'];
            $post->slug = $validatedData['slug'];

            if ($request->hasFile('post_image')) {
                $image = $request->file('post_image');
                $name = time();
                $folder = '/uploads/images/post';
                $fileID = $this->uploadFile($image, $folder, 'public', $name, 'post_image');
                $post->upload_id = $fileID;
            }

            $post->update();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('app.update.success'),
                'data' => [
                    'post' => $post,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->respondInternalError('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $post = Post::where('id', $id)->first();
        if (empty($post)) {
            return $this->respondNotFound('', __('app.post.not_found'));
        }

        if (Auth::user()->role->name == UserTypes::user && Auth::user()->id != $post->created_by) {
            return $this->respondNotAuthorized('', __('app.user.role.forbidden'));
        }

        try {
            DB::beginTransaction();
            $post->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => __('app.delete.success'),
                'data' => [
                    'post' => []
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->respondInternalError('', $e->getMessage());
        }
    }
}
