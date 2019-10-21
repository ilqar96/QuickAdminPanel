@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.post.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.posts.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                <label for="image">{{ trans('cruds.post.fields.image') }}</label>
                <div class="needsclick dropzone" id="image-dropzone">

                </div>
                @if($errors->has('image'))
                    <p class="help-block">
                        {{ $errors->first('image') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.post.fields.image_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('images') ? 'has-error' : '' }}">
                <label for="images">{{ trans('cruds.post.fields.images') }}</label>
                <div class="needsclick dropzone" id="images-dropzone">

                </div>
                @if($errors->has('images'))
                    <p class="help-block">
                        {{ $errors->first('images') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.post.fields.images_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <label for="title">{{ trans('cruds.post.fields.title') }}*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($post) ? $post->title : '') }}" required>
                @if($errors->has('title'))
                    <p class="help-block">
                        {{ $errors->first('title') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.post.fields.title_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                <label for="content">{{ trans('cruds.post.fields.content') }}*</label>
                <textarea id="content" name="content" class="form-control ckeditor">{{ old('content', isset($post) ? $post->content : '') }}</textarea>
                @if($errors->has('content'))
                    <p class="help-block">
                        {{ $errors->first('content') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.post.fields.content_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('publish_date') ? 'has-error' : '' }}">
                <label for="publish_date">{{ trans('cruds.post.fields.publish_date') }}*</label>
                <input type="text" id="publish_date" name="publish_date" class="form-control datetime" value="{{ old('publish_date', isset($post) ? $post->publish_date : '') }}" required>
                @if($errors->has('publish_date'))
                    <p class="help-block">
                        {{ $errors->first('publish_date') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.post.fields.publish_date_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('active') ? 'has-error' : '' }}">
                <label>{{ trans('cruds.post.fields.active') }}*</label>
                @foreach(App\Post::ACTIVE_RADIO as $key => $label)
                    <div>
                        <input id="active_{{ $key }}" name="active" type="radio" value="{{ $key }}" {{ old('active', 1) === (string)$key ? 'checked' : '' }} required>
                        <label for="active_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('active'))
                    <p class="help-block">
                        {{ $errors->first('active') }}
                    </p>
                @endif
            </div>
            <div class="form-group {{ $errors->has('category_id') ? 'has-error' : '' }}">
                <label for="category">{{ trans('cruds.post.fields.category') }}</label>
                <select name="category_id" id="category" class="form-control select2">
                    @foreach($categories as $id => $category)
                        <option value="{{ $id }}" {{ (isset($post) && $post->category ? $post->category->id : old('category_id')) == $id ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
                @if($errors->has('category_id'))
                    <p class="help-block">
                        {{ $errors->first('category_id') }}
                    </p>
                @endif
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    Dropzone.options.imageDropzone = {
    url: '{{ route('admin.posts.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="image"]').remove()
      $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($post) && $post->image)
      var file = {!! json_encode($post->image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, '{{ $post->image->getUrl('thumb') }}')
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}
</script>
<script>
    var uploadedImagesMap = {}
Dropzone.options.imagesDropzone = {
    url: '{{ route('admin.posts.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="images[]" value="' + response.name + '">')
      uploadedImagesMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedImagesMap[file.name]
      }
      $('form').find('input[name="images[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($post) && $post->images)
      var files =
        {!! json_encode($post->images) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="images[]" value="' + file.file_name + '">')
        }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@stop