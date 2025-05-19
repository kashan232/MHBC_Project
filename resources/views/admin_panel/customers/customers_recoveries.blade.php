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
                    <h6 class="page-title">Customer Recoveries</h6>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card b-radius--10">
                            <div class="card-body p-0">
                                <div class="table-responsive--sm table-responsive">
                                <table id="example" class="display  table table--light" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>S.N.</th>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Recovery Amount</th>
                                                <th>Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($Customers as $Customer)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $Customer->customer_name }}</td>
                                                <td>{{ $Customer->recovery_date }}</td>
                                                <td>{{ $Customer->recovery_amount }}</td>
                                                <td>{{ $Customer->closing_balance }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>
    @include('admin_panel.include.footer_include')