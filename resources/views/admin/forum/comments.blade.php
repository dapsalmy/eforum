@extends('layouts.admin')

@section('styles')

<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/ui/trumbowyg.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/emoji/ui/trumbowyg.emoji.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/giphy/ui/trumbowyg.giphy.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/prism/prism.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/highlight/ui/trumbowyg.highlight.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/bootstrap-taginput/bootstrap-tagsinput.css') }}">

@endsection

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-lg-12">
            @if(Route::is('admin.comments.list'))
                <div class="card">
                    <div class="card-header">

                        <div class="d-md-flex justify-content-between align-items-center mb-10">
                            <h5 class="h4 mb-0">{{ trans('comments') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable_cms" class="table table-bordered table-reload">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('user') }}</th>
                                    <th>{{ trans('comment') }}</th>
                                    <th>{{ trans('post') }}</th>
                                    <th>{{ trans('status') }}</th>
                                    <th>{{ trans('date') }}</th>
                                    <th class="text-right">{{ trans('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comments as $key => $comment)
                                    <tr>
                                        <td>{{ ($key+1) }}</td>
                                        <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($comment->user_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                         {{ App\Models\User::find($comment->user_id)->name }}
                                        </td>
                                        <td>{!! strip_tags(substr($comment->body, 0, get_setting('maximum_preview_chars'))) !!}</td>
                                        <td><a href="{{ route('home.post', ['post_id' => $comment->post->post_id, 'slug' => $comment->post->slug]) }}" target="_blank">{{ $comment->post->title }}</a></td>

                                        @if ($comment->status == 1)
                                            <td> <span class="badge bg-success">Active</span> </td>
                                        @else
                                            <td> <span class="badge bg-danger">Not Active</span> </td>
                                        @endif
                                        <td>{{ \Carbon\Carbon::parse($comment->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="text-right">

                                            <a href="{{ route('admin.comments.edit', ['id' => $comment->id]) }}" class="btn btn-soft-success btn-icon btn-circle btn-sm btn icon" title="Edit">
                                                <i class="align-middle" data-feather="edit-2"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" title="Delete"
                                            onclick="delete_item('{{ route('admin.comments.destroy') }}','{{ $comment->id }}','{{ trans('delete_this_comment') }}?');">
                                                <i class="align-middle" data-feather="trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    </div>
                </div>
            @elseif (Route::is('admin.comments.edit'))

                <div class="card-style settings-card-2 mb-30">
                    <h4 class="mb-3">{{ trans('edit_comment') }}</h4>
                    <form id="edit_comment_form" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label class="form-label">{{ trans('body') }}</label>
                                    <p class="small mb-3">* {{ trans('here_is_a_list_of_sites_you_can_embed_video_from') }} <a href="https://noembed.com/#supported-sites" target="_blank">{{ trans('list_of_sites') }}</a> </p>
                                    <textarea name="bodyComment" id="bodyComment" rows="5" placeholder="Edit your comment ...">{{ $comment->body }}</textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-style-1">
                                    <label for="status">{{ trans('status') }}</label>
                                    <select name="status" id="status" class="form-select form-control">
                                        <option value="1" {{ $comment->status == 1 ? 'selected="selected"' : '' }}>{{ trans('active') }}</option>
                                        <option value="0" {{ $comment->status == 0 ? 'selected="selected"' : '' }}>{{ trans('not_active') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                            <button type="submit" id="edit_comment_btn" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            @endif
        </div>


      </div><!-- row -->
     </div><!-- container -->
    </section>

</main>
@endsection

@section('scripts')

<script src="{{ my_asset('assets/vendors/bootstrap-taginput/bootstrap-tagsinput.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/trumbowyg.min.js') }}"></script>
<!-- Import Trumbowyg plugins... -->
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/colors/trumbowyg.colors.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/emoji/trumbowyg.emoji.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/giphy/trumbowyg.giphy.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/prism/prism.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/highlight/trumbowyg.highlight.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/noembed/trumbowyg.noembed.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/indent/trumbowyg.indent.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/cleanpaste/trumbowyg.cleanpaste.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/pasteimage/trumbowyg.pasteimage.min.js') }}"></script>
<script src="{{ my_asset('assets/vendors/trumbowyg/plugins/upload/trumbowyg.upload.min.js') }}"></script>

<script>
    $(document).ready(function () {

        $('#bodyComment').trumbowyg({
            removeformatPasted: true,
            btnsDef: {
                // Create a new dropdown
                image: {
                    dropdown: ['insertImage', 'upload'],
                    ico: 'insertImage'
                }
            },
            btns: [
                ['viewHTML'],
                ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['indent', 'outdent'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['foreColor', 'backColor'],
                ['horizontalRule'],
                ['removeformat'],
                ['link'],
                ['emoji'],
                ['giphy'],
                ['noembed'],
                ['highlight'],
                ['image']
            ],
            plugins: {
                giphy: {
                    apiKey: 'dNhCbN6hrhpBMxXhIswM34wIR2UBpCns'
                },

                upload: {
                    serverPath: '{{ route('trumb.upload') }}',
                    fileFieldName: 'image',
                    data: [],
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    urlPropertyName: 'file',
                    statusPropertyName: 'success',
                    imageWidthModalEdit: false,
                    success: function (data, trumbowyg, $modal, values) {

                        if (data.status == 200) {

                            var url = data.url;
                            trumbowyg.execCmd('insertImage', url, false, true);
                            var $img = $('img[src="' + url + '"]:not([alt])', trumbowyg.$box);
                            $img.attr('alt', values.alt);
                            setTimeout(function () {
                                trumbowyg.closeModal();
                            }, 250);
                            trumbowyg.$c.trigger('tbwuploadsuccess', [trumbowyg, data, url]);

                        } else if(data.status == 400){

                            trumbowyg.closeModal();

                            tata.error("Error", data.messages, {
                            position: 'tr',
                            duration: 3000,
                            animate: 'slide'
                            });
                        }
                    },
                    error: null
                }
            }
        });

    });
</script>

    @if(Route::is('admin.comments.edit'))
      <script>
        $(function() {
            // add comment ajax request
            $(document).on('submit', '#edit_comment_form', function(e) {
                e.preventDefault();

                const fd = new FormData(this);
                $("#edit_comment_btn").text('{{ trans('submitting') }}...');
                $.ajax({
                    url: '{{ route('admin.comments.edit', ['id' => $comment->id]) }}',
                    method: 'post',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {

                    if (response.status == 400) {

                        showError('bodyComment', response.messages.bodyComment);
                        $("#edit_comment_btn").text('{{ trans('submit') }}');

                    }else if (response.status == 200) {

                        tata.success("Success", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        removeValidationClasses("#edit_comment_form");
                        $("#edit_comment_form")[0].reset();
                        window.location.reload();

                    }else if(response.status == 401){

                        tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        $("#edit_comment_form")[0].reset();
                        window.location.reload();

                    }else if(response.status == 402){

                            tata.error("Error", response.messages, {
                                position: 'tr',
                                duration: 3000,
                                animate: 'slide'
                            });

                            $("#edit_comment_form")[0].reset();

                        }

                    }
                });
            });

        });
      </script>

    @endif

@endsection
