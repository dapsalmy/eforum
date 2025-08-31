@extends('layouts.front')

@section('styles')

<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/ui/trumbowyg.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/emoji/ui/trumbowyg.emoji.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/giphy/ui/trumbowyg.giphy.min.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/prism/prism.css') }}">
<link rel="stylesheet" href="{{ my_asset('assets/vendors/trumbowyg/plugins/highlight/ui/trumbowyg.highlight.min.css') }}">

@endsection

@section('content')

    <div class="vine-header mb-4" data-aos="fade-down" data-aos-easing="linear">
        <div class="container">
            <div class="row px-3">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="[{ route('home') }]"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li><a href="{{ route('home.post', ['post_id' => $comment->post->post_id, 'slug' => $comment->post->slug]) }}">{{ trans('back_to_post') }}</a></li>
                        <li>{{ trans('edit') }} {{ trans('comment') }}</li>
                    </ul>
                    <h2 class="mb-2">{{ trans('edit') }} {{ trans('comment') }}</h2>
                </div>
            </div>
        </div>
    </div><!--/vine-header-->

    <div class="comment-form" data-aos="fade-up" data-aos-easing="linear">
        <h3 class="comment-form-title">{{ trans('edit') }} {{ trans('comment') }}</h3>
        <form id="edit_comment_form" method="POST">
            @csrf

            <input type="hidden" name="comment_id" id="comment_id" value="{{ $comment->id }}">

           <div class="row">
              <div class="col-lg-12 d-flex mb-4">
                <div class="comment-form-avatar d-none d-sm-block">
                    <img src="{{ Auth::check() == null ? my_asset('uploads/users/avatar.jpg') : my_asset('uploads/users/'.Auth::user()->image) }}" alt="User">
                </div>
                 <div class="comment-input">

                    <p class="small mb-3">* {{ trans('here_is_a_list_of_sites_you_can_embed_video_from') }} <a href="https://noembed.com/#supported-sites" target="_blank">{{ trans('list_of_sites') }}</a> </p>

                    <div class="form-group">
                        <div id="trumbowyg-change">
                            <textarea name="bodyComment" id="bodyComment" rows="5" placeholder="Edit your comment ...">{{ $comment->body }}</textarea>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="comment-btn mt-3">
                        <button type="submit" class="btn btn-mint" id="edit_comment_btn">{{ trans('edit') }} {{ trans('comment') }}</button>
                    </div>

                 </div>
              </div>
           </div>
        </form>
    </div>


@endsection

@section('scripts')

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

    $(function() {


     // add comment ajax request
     $(document).on('submit', '#edit_comment_form', function(e) {
       e.preventDefault();

       const fd = new FormData(this);
       $("#edit_comment_btn").text('{{ trans('posting') }} {{ trans('comment') }}...');
       $.ajax({
         url: '{{ route('user.comments.edit', ['id' => $comment->id]) }}',
         method: 'post',
         data: fd,
         cache: false,
         contentType: false,
         processData: false,
         dataType: 'json',
         success: function(response) {

           if (response.status == 400) {

               showError('bodyComment', response.messages.bodyComment);
               $("#edit_comment_btn").text('{{ trans('edit') }} {{ trans('comment') }}');

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
@endsection
