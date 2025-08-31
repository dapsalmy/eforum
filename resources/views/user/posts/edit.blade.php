@extends('layouts.user')

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

<h4 class="mb-0" data-aos="fade-down" data-aos-easing="linear"><i class="bi bi-plus-circle-dotted me-2"></i> {{ trans('edit_post') }}</h4>

<div class="row g-0">
    <div class="col-12">
        <div class="dashboard-card" data-aos="fade-up" data-aos-easing="linear">
            <div class="dashboard-body">


                <form id="editpost_form" method="POST">
                    @csrf

                    <input type="hidden" name="old_tags" id="old_tags" value="@foreach ($post->tags as $tag){{$tag->name}}, @endforeach">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('title') }}</label>
                            <input type="text" name="title" id="title" value="{{ $post->title }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('description') }}</label>
                            <textarea name="description" id="description" rows="4">{{ $post->description }}</textarea>
                            <div id="count">{{ trans('characters') }}: 400 </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('keywords') }}</label>
                            <input type="text" name="keywords" id="keywords"  value="{{ $post->keywords }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('categories') }}</label>
                            <select name="category_id" id="category_id">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('tags') }}</label>
                            <input type="text" name="tags" id="tags" data-role="tagsinput" value="@foreach ($post->tags as $tag){{$tag->name}}, @endforeach">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{ trans('body') }}</label>
                            <p class="small mb-3">* {{ trans('here_is_a_list_of_sites_you_can_embed_video_from') }} <a href="https://noembed.com/#supported-sites" target="_blank">{{ trans('list_of_sites') }}</a> </p>

                            <div id="trumbowyg-change">
                                <textarea name="body" id="body" rows="4">{{ $post->body }}</textarea>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-sm-12">
                            <label>{{ trans('status') }}</label>
                            <div class="form-group">
                                <div class="form-check mb-2 me-2">
                                    <input type="radio" name="public" value="1" id="public1" {{ $post->public == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="public1">
                                        {{ trans('public') }}
                                    </label>
                                </div>
                        @if(get_setting('payments_on_site') == 'Yes')
                            @if (Auth::user()->subscription()->followers === 1)
                                <div class="form-check mb-2">
                                    <input type="radio" name="public" value="2" id="public2" {{ $post->public == '2' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="public2">
                                        {{ trans('show_to_followers') }}
                                    </label>
                                </div>
                            @else
                                <span class="small text-danger ms-4">{{ trans('to_show_posts_only_to_followers') }}, {{ trans('subscribe_to_a_new_pricing_plan') }}</span>
                            @endif
                        @else
                            <div class="form-check mb-2">
                                <input type="radio" name="public" value="2" id="public2" {{ $post->public == '2' ? 'checked' : '' }}>
                                <label class="form-check-label" for="public2">
                                    {{ trans('show_to_followers') }}
                                </label>
                            </div>
                        @endif
                                <div class="public-msg"></div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label>{{ trans('comments') }} & {{ trans('replies') }}</label>
                            <div class="form-group">
                                <div class="form-check mb-2 me-2">
                                    <input type="radio" name="comments" value="1" id="comments1" {{ $post->comments == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="comments1">
                                        {{ trans('allow') }}
                                    </label>
                                </div>
                        @if(get_setting('payments_on_site') == 'Yes')
                            @if (Auth::user()->subscription()->comments === 1)
                                <div class="form-check mb-2">
                                    <input type="radio" name="comments" value="2" id="comments2" {{ $post->comments == '2' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="comments2">
                                        {{ trans('close') }}
                                    </label>
                                </div>
                            @else
                                <span class="small text-danger ms-4">{{ trans('to_close_comments_and_replies_on_posts') }}, {{ trans('subscribe_to_a_new_pricing_plan') }}</span>
                            @endif
                        @else
                            <div class="form-check mb-2">
                                <input type="radio" name="comments" value="2" id="comments2" {{ $post->comments == '2' ? 'checked' : '' }}>
                                <label class="form-check-label" for="comments2">
                                    {{ trans('close') }}
                                </label>
                            </div>
                        @endif

                                <div class="comments-msg"></div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label>{{ trans('reactions') }} or {{ trans('likes') }}</label>
                            <div class="form-group">
                                <div class="form-check mb-2 me-2">
                                    <input type="radio" name="likes" value="1" id="likes1" {{ $post->likes == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="likes1">
                                        {{ trans('show') }}
                                    </label>
                                </div>
                        @if(get_setting('payments_on_site') == 'Yes')
                            @if (Auth::user()->subscription()->reactions === 1)
                                <div class="form-check mb-2">
                                    <input type="radio" name="likes" value="2" id="likes2" {{ $post->likes == '2' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="likes2">{{ trans('hide') }}</label>
                                </div>
                            @else
                                <span class="small text-danger ms-4">{{ trans('to_hide_reactions_or_likes_on_posts') }}, {{ trans('subscribe_to_a_new_pricing_plan') }}</span>
                            @endif
                        @else
                            <div class="form-check mb-2">
                                <input type="radio" name="likes" value="2" id="likes2" {{ $post->likes == '2' ? 'checked' : '' }}>
                                <label class="form-check-label" for="likes2">{{ trans('hide') }}</label>
                            </div>
                        @endif

                                <div class="likes-msg"></div>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex pt-5">
                        <button type="submit" id="editpost_btn" class="btn btn-mint me-3">{{ trans('submit') }}</button>
                    </div>
                </form>

            </div>
        </div><!--/dashboard-card-->
    </div>
</div>

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

        $('#body').trumbowyg({
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

        // update user ajax request
        $(document).on('submit', '#editpost_form', function(e) {
            e.preventDefault();
            start_load();
            const fd = new FormData(this);
            $("#editpost_btn").text('{{ trans('sending') }}...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method: 'POST',
                url: '{{ route('user.posts.edit', ['id' => $post->id]) }}',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    end_load();

                    if (response.status == 400) {

                        showError('title', response.messages.title);
                        showError('category_id', response.messages.category_id);
                        showError('tags', response.messages.tags);
                        showError('body', response.messages.body);

                        if($('#public1').prop('checked') || $('#public2').prop('checked')){
                            $(".public-msg").html("");
                        }else{
                            $(".public-msg").html("<span class='text-danger small'>"+ "{{ trans("please_choose_atleast_one") }}</span>");
                        }
                        if($('#comments1').prop('checked') || $('#comments2').prop('checked')){
                            $(".comments-msg").html("");
                        }else{
                            $(".comments-msg").html("<span class='text-danger small'>"+ "{{ trans("please_choose_atleast_one") }}</span>");
                        }
                        if($('#likes1').prop('checked') || $('#likes2').prop('checked')){
                            $(".likes-msg").html("");
                        }else{
                            $(".likes-msg").html("<span class='text-danger small'>"+ "{{ trans("please_choose_atleast_one") }}</span>");
                        }
                        $("#editpost_btn").text('{{ trans('submit') }}');

                    }else if (response.status == 200) {

                        tata.success("Success", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        removeValidationClasses("#editpost_form");
                        $("#editpost_form")[0].reset();
                        window.location.reload();

                    }else if(response.status == 401){

                        tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                        $("#editpost_form")[0].reset();
                        window.location.reload();

                    }

                }
            });

        });


    });
</script>

@endsection
