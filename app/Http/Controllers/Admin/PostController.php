<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPostRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Post;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Post::with(['category'])->select(sprintf('%s.*', (new Post)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'post_show';
                $editGate      = 'post_edit';
                $deleteGate    = 'post_delete';
                $crudRoutePart = 'posts';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('image', function ($row) {
                if ($photo = $row->image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('images', function ($row) {
                if (!$row->images) {
                    return '';
                }

                $links = [];

                foreach ($row->images as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank"><img src="' . $media->getUrl('thumb') . '" width="50px" height="50px"></a>';
                }

                return implode(' ', $links);
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : "";
            });

            $table->editColumn('active', function ($row) {
                return $row->active ? Post::ACTIVE_RADIO[$row->active] : '';
            });
            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });

            $table->editColumn('category.slug', function ($row) {
                return $row->category ? (is_string($row->category) ? $row->category : $row->category->slug) : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'image', 'images', 'category']);

            return $table->make(true);
        }

        return view('admin.posts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('post_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->all());

        if ($request->input('image', false)) {
            $post->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
        }

        foreach ($request->input('images', []) as $file) {
            $post->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('images');
        }

        return redirect()->route('admin.posts.index');
    }

    public function edit(Post $post)
    {
        abort_if(Gate::denies('post_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = Category::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $post->load('category');

        return view('admin.posts.edit', compact('categories', 'post'));
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

        if (count($post->images) > 0) {
            foreach ($post->images as $media) {
                if (!in_array($media->file_name, $request->input('images', []))) {
                    $media->delete();
                }
            }
        }

        $media = $post->images->pluck('file_name')->toArray();

        foreach ($request->input('images', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $post->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('images');
            }
        }

        return redirect()->route('admin.posts.index');
    }

    public function show(Post $post)
    {
        abort_if(Gate::denies('post_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post->load('category');

        return view('admin.posts.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        abort_if(Gate::denies('post_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post->delete();

        return back();
    }

    public function massDestroy(MassDestroyPostRequest $request)
    {
        Post::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
