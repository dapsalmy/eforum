@extends('layouts.admin')

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <div class="d-md-flex justify-content-between align-items-center mb-10">
                        <h5 class="h4 mb-0">{{ trans('user_reports') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable_cms" class="table table-bordered table-reload">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('sender') }}</th>
                                <th>{{ trans('user') }}</th>
                                <th>{{ trans('category') }}</th>
                                <th>{{ trans('reason') }}</th>
                                <th>{{ trans('date') }}</th>
                                <th class="text-right">{{ trans('options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $key => $report)
                                <tr>
                                    <td>{{ ($key+1) }}</td>
                                    <td><img src="{{ my_asset('uploads/users/'.App\Models\User::find($report->sender_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                     {{ App\Models\User::find($report->sender_id)->name }}
                                    </td>
                                    <td>
                                        <img src="{{ my_asset('uploads/users/'.App\Models\User::find($report->user_id)->image) }}" class="img-fluid avatar avatar-rounded me-2" width="40px" height="40px" alt="Image">
                                        {{ App\Models\User::find($report->user_id)->name }}

                                        @if(App\Models\User::find($report->user_id)->isBanned())
                                            <h6 class="text-danger">{{ trans('banned') }}</h6>
                                        @endif
                                    </td>
                                    <td>{{ $report->category }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td>{{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMMM YYYY') }}</td>
                                    <td class="text-right">
                                        @if (App\Models\User::find($report->user_id)->isBanned())
                                            <a href="javascript:void(0)" onclick="remove_ban('{{ route('remove.ban') }}','{{ $report->user_id }}','{{ trans('remove_ban') }}')" class="btn btn-danger">
                                                <i class="align-middle" data-feather="x"></i> {{ trans('remove_ban') }}
                                            </a>
                                        @else
                                            <a href="#" id="{{ $report->user_id }}'" class="btn btn-success editIcon">
                                                <i class="align-middle" data-feather="slash"></i> {{ trans('ban_user') }}
                                            </a>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
        </div>

    </div><!-- row -->
   </div><!-- container -->
  </section>

  {{-- User modal start --}}
  <div class="modal fade" id="user_modal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">{{ trans('ban_user') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="model-body">

              <form id="user_ban_form" method="POST">
                  @csrf

                  <input type="hidden" name="user_id" id="user_id">
                  <div class="row px-3 py-3">

                    <div class="col-12">
                        <h5 id="name" class="mb-2"></h5>
                        <div id="current_image"></div>
                    </div>

                      <div class="col-12">
                        <div class="select-style-1">
                            <label for="time">{{ trans('duration') }}</label>
                            <div class="select-position">
                                <select name="time" id="time" class="light-bg">
                                  @foreach ($durations as $duration)
                                      <option value="{{ $duration->time }}">{{ $duration->title }}</option>
                                  @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                      </div>
                      <div class="col-12">
                          <div class="input-style-1">
                              <label for="reason">{{ trans('reason') }}</label>
                              <textarea name="reason" id="reason" rows="4" placeholder="Reason"></textarea>
                              <div class="invalid-feedback"></div>
                          </div>
                      </div>

                  </div>

                  <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                  <button type="submit" id="user_ban_btn" class="btn btn-success">{{ trans('ban_user') }}</button>
                  </div>
              </form>

          </div>
          </div>
      </div>
  </div>

</main>
@endsection
@section('scripts')

<script>

    // ban model ajax request
    $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        start_load();
        let id = $(this).attr('id');
        $.ajax({
            url: '{{ route('get.user') }}',
            method: 'get',
            data: {
            id: id,
            },
            success: function(response) {
                end_load();

                $('#user_modal').modal('show');

                $('#user_ban_form #name').text(response.name);

                $('#user_ban_form #current_image').prepend($('<img>',{id:'theImg',src:'../../public/uploads/users/'+response.image,class:'img-fluid mb-3',width:'100px',height:'100px'}));

                $('#user_ban_form #user_id').val(response.id);

            }
        });
    });

    // ban ajax request
    $(document).on('submit', '#user_ban_form', function(e) {
        e.preventDefault();
        start_load();
        const fd = new FormData(this);
        $("#user_ban_btn").text('{{ trans('banning') }}...');
        $.ajax({
        url: '{{ route('ban.user') }}',
        method: 'post',
        data: fd,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {

            end_load();

            if (response.status == 400) {

                showError('reason', response.messages.reason);
                $("#user_ban_btn").text('{{ trans('ban_user') }}');

            }else if (response.status == 200) {

                tata.success("Success", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                removeValidationClasses("#user_ban_form");
                $("#user_ban_form")[0].reset();
                $("#user_modal").modal('hide');
                window.location.reload();

            }else if(response.status == 401){

                tata.error("Error", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                $("#user_ban_form")[0].reset();
                $("#user_modal").modal('hide');
                window.location.reload();

            }

        }
        });
    });
</script>

@endsection
