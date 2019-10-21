<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\Admin\PostResource;
use App\Post;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('post_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PostResource(Post::with(['category'])->get());
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->all());

        if ($request->input('image', false)) {
            $post->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
        }

        if ($request->input('images', false)) {
            $post->addMedia(storage_path('tmp/uploads/' . $request->input('images')))->toMediaCollection('images');
        }

        return (new PostResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Post $post)
    {
        abort_if(Gate::denies('post_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PostResource($post->load(['category']));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->all());

        if ($request->input('image', false)) {
            if (!$post->image || $request->input('image') !== $post->image->file_name) {
                $post->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
            }
        } elseif ($post->image) {
            $post->image->delete();
        }

        if ($request->input('images', false)) {
            if (!$post->images || $request->input('images') !== $post->images->file_name) {
                $post->addMedia(storage_path('tmp/uploads/' . $request->input('images')))->toMediaCollection('images');
            }
        } elseif ($post->images) {
            $post->images->delete();
        }

        return (new PostResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Post $post)
    {
        abort_if(Gate::denies('post_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
