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
                        <li><a href="{{ route('home') }}"><span class="bi bi-house me-1"></span>{{ trans('home') }}</a></li>
                        <li><a href="{{ route('home.posts') }}">{{ trans('posts') }}</a></li>
                        <li>{{ $post->title }}</li>
                    </ul>
                    <h3 class="mb-2">{{ $post->title }}</h3>
                </div>
            </div>
        </div>
        @if(Auth::check())
            @if (get_setting('ads') == 1)
               @if(get_setting('payments_on_site') == 'Yes')
                    @if (Auth::user()->subscription()->ads == 0)
                        <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                            <div class="ad-box">
                                {!! get_setting('top_ad') !!}
                            </div>
                        </div>
                    @endif
               @else
                    <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                        <div class="ad-box">
                            {!! get_setting('top_ad') !!}
                        </div>
                    </div>
               @endif
            @endif
        @else
            @if (get_setting('ads') == 1)
                <div class="ad-spot text-center mt-4" data-aos="fade-up" data-aos-easing="linear">
                    <div class="ad-box">
                        {!! get_setting('top_ad') !!}
                    </div>
                </div>
            @endif
        @endif

    </div><!--/vine-header-->

    @if(get_setting('payments_on_site') == 'Yes')
        @if ($post->category->pro === 1)
            @include('frontend.partials.post.category_pro')
        @else
            @include('frontend.partials.post.category_not_pro')
        @endif
    @else
        @include('frontend.partials.post.no_payments')

    @endif

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
<script src="{{ my_asset('assets/vendors/clipboard/clipboard.min.js') }}"></script>

<script>

    $(document).ready(function () {

        $('#bodyComment, #bodyReply').trumbowyg({
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

    $("#formButton").on('click', function(){
        $(".reply-form").toggle();
    });

   $(function() {

        // add comment ajax request
        $(document).on('submit', '#add_comment_form', function(e) {
            e.preventDefault();

            const fd = new FormData(this);
            $("#add_comment_btn").text('{{ trans('posting') }} {{ trans('comment') }}...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('comments.add') }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {


                if (response.status == 400) {

                    showError('bodyComment', response.messages.bodyComment);
                    $("#add_comment_btn").text('{{ trans('post') }} {{ trans('comment') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#add_comment_form");
                    $("#add_comment_form")[0].reset();
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#add_comment_form")[0].reset();
                    window.location.reload();

                }else if(response.status == 402){

                    tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                    });

                    $("#add_comment_form")[0].reset();
                    $("#add_comment_btn").text('Post Comment');

                }

                }
            });
        });

        // add reply ajax request
        $(document).on('submit', '#add_reply_form', function(e) {
            e.preventDefault();

            const fd = new FormData(this);
            $("#add_reply_btn").text('{{ trans('posting') }} {{ trans('reply') }}...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('replies.add') }}',
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {


                if (response.status == 400) {

                    showError('bodyReply', response.messages.body);
                    $("#add_reply_btn").text('{{ trans('post') }} {{ trans('reply') }}');

                }else if (response.status == 200) {

                    tata.success("Success", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    removeValidationClasses("#add_reply_form");
                    $("#add_reply_form")[0].reset();
                    window.location.reload();

                }else if(response.status == 401){

                    tata.error("Error", response.messages, {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });

                    $("#add_reply_form")[0].reset();
                    window.location.reload();

                }else if(response.status == 402){

                    tata.error("Error", response.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                    });

                    $("#add_reply_form")[0].reset();
                    $("#add_reply_btn").text('Post Reply');

                }

                }
            });
        });

        $(document).on('click', '.likePost', function(e) {
            e.preventDefault();
            let a = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('like') }}',
                method: 'post',
                dataType: "json",
                data: {item: a},
                success: function(e) {
                    var t;
                    1 == e.bool ? ($("#like-icon-" + a).removeClass("text-muted").addClass("text-danger"), t = $("#like-" + a).text(), $("#like-" + a).text(++t)) : 0 == e.bool && ($("#like-icon-" + a).removeClass("text-danger").addClass("text-muted"), t = $("#like-" + a).text(), $("#like-" + a).text(--t))

                    if (e.status == 200) {

                        tata.success("Success", e.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                    }
                },
                error: function(e) {

                    tata.error("Error", '{{ trans('please_login_to_like') }}', {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });
                }
            });
        });

        $(document).on('click', '.likeComment', function(e) {
            e.preventDefault();
            let a = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('like.comment') }}',
                method: 'post',
                dataType: "json",
                data: {item: a},
                success: function(e) {
                    var t;
                    1 == e.bool ? ($("#like-comment-icon-" + a).removeClass("text-muted").addClass("text-danger"), t = $("#like-comment-" + a).text(), $("#like-comment-" + a).text(++t)) : 0 == e.bool && ($("#like-comment-icon-" + a).removeClass("text-danger").addClass("text-muted"), t = $("#like-comment-" + a).text(), $("#like-comment-" + a).text(--t))

                    if (e.status == 200) {

                        tata.success("Success", e.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                    }
                },
                error: function(e) {

                    tata.error("Error", '{{ trans('please_login_to_like') }}', {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });
                }
            });
        });

        $(document).on('click', '.likeReply', function(e) {
            e.preventDefault();
            let a = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('like.reply') }}',
                method: 'post',
                dataType: "json",
                data: {item: a},
                success: function(e) {
                    var t;
                    1 == e.bool ? ($("#like-reply-icon-" + a).removeClass("text-muted").addClass("text-danger"), t = $("#like-reply-" + a).text(), $("#like-reply-" + a).text(++t)) : 0 == e.bool && ($("#like-reply-icon-" + a).removeClass("text-danger").addClass("text-muted"), t = $("#like-reply-" + a).text(), $("#like-reply-" + a).text(--t))

                    if (e.status == 200) {

                        tata.success("Success", e.messages, {
                        position: 'tr',
                        duration: 3000,
                        animate: 'slide'
                        });

                    }
                },
                error: function(e) {

                    tata.error("Error", '{{ trans('please_login_to_like') }}', {
                    position: 'tr',
                    duration: 3000,
                    animate: 'slide'
                    });
                }
            });
        });

    });


    /*============================================
    Copy to Clipboard
    ==============================================*/
    document.querySelectorAll('pre').forEach(function (codeBlock) {
        var button = document.createElement('button');
        button.className = 'copy-code-button';
        button.type = 'button';
        var s = codeBlock.innerText;
        button.setAttribute('data-clipboard-text',s);
        button.innerText = 'Copy';
        // button.setAttribute('title', 'Copiar para a área de transferência');

        // var pre = codeBlock.parentNode;
        codeBlock.classList.add('prettyprint');
        // pre.parentNode.insertBefore(button, pre);
        codeBlock.appendChild(button);
    });

    var clipboard = new ClipboardJS('.copy-code-button');

    clipboard.on('success', function(e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);
        e.trigger.textContent = 'Copied';
        window.setTimeout(function() {
            e.trigger.textContent = 'Copy';
        }, 2000);
        e.clearSelection();

    });

    clipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
        e.trigger.textContent = 'Error on Copy';
        window.setTimeout(function() {
            e.trigger.textContent = 'Copy';
        }, 2000);
        e.clearSelection();
    });


</script>
@endsection
