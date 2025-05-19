@include('admin_panel.include.header_include')

<body>
    <!-- page-wrapper start -->
    <div class="page-wrapper default-version">

        <!-- sidebar start -->

        @include('admin_panel.include.sidebar_include')
        <!-- sidebar end -->

        <!-- navbar-wrapper start -->
        @include('admin_panel.include.navbar_include')
        <!-- navbar-wrapper end -->
        <div class="body-wrapper">
            <div class="bodywrapper__inner">

                <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
                    <h6 class="page-title">Password Setting</h6>
                </div>
                <div class="row mb-none-30">
                    <div class="col-lg-12 col-md-12 mb-30">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4 border-bottom pb-2">Change Password</h5>
                                @if (session()->has('success'))
                                <div class="alert alert-success">
                                    <strong>Success!</strong> {{ session('success') }}.
                                </div>
                                @endif
                                <form action="{{ route('updte-change-Password') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="old_password" class="required">Password</label>
                                        <input class="form-control" type="password" name="old_password" id="old_password">
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="required">New Password</label>
                                        <input class="form-control" type="password" name="new_password" id="password">
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation" class="required">Confirm Password</label>
                                        <input class="form-control" type="password" name="retype_new_password" id="password_confirmation">
                                    </div>
                                    <button type="submit" class="btn btn--primary w-100 btn-lg h-45">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div><!-- body-wrapper end -->

    </div>
    @include('admin_panel.include.footer_include')