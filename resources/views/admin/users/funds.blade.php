@extends('layouts.admin')

@section('content')

<main class="content">
    <!-- ========== section start ========== -->
    <section class="section">
      <div class="container-fluid">
      <div class="row mt-50">

        <div class="col-lg-12">

            <div class="card-style settings-card-2 mb-30">
                <h5 class="h4 mb-3">{{ trans('edit') }} {{ trans('user') }} {{ trans('wallet') }}</h5>
                <form action="{{ route('admin.users.update_funds') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <p>{{ $user->name }}</p>
                            <img src="{{ my_asset('uploads/users/'.$user->image) }}" width="70" height="70" alt="User">
                        </div>
                        <div class="col-12">
                            <div class="input-style-1">
                                <label for="title">{{ trans('wallet') }}</label>
                                <input type="number" name="wallet" id="wallet" value="{{ $user->wallet }}" class="form-control my-2">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="main-btn primary-btn btn-hover">{{ trans('submit') }}</button>
                </form>
            </div>
        </div><!-- col-lg-12 -->

    </div><!-- row -->
    </div><!-- container -->
 </section>

</main>
@endsection
