@include('admin_panel.include.header_include')

<body>
    <div class="page-wrapper default-version">
        @include('admin_panel.include.sidebar_include')
        @include('admin_panel.include.navbar_include')
        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
                    <h6 class="page-title">Suppliers Paid Amounts</h6>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card b-radius--10">
                            <div class="card-body p-0">
                                <div class="table-responsive--sm table-responsive">
                                    <div class="table-responsive">
                                        <table id="example" class="display  table table--light style--two bg--white" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th> Name</th>
                                                    <th>Paid Amount</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($supplierPayments as $key => $VendorPayment)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $VendorPayment->payment_date }}</td>
                                                    <td>{{ $VendorPayment->supplier_id }}</td>
                                                    <td>{{ $VendorPayment->amount_paid }}</td>
                                                    <td>{{ $VendorPayment->description }}</td>


                                                </tr>
                                                @endforeach
                                                @if($supplierPayments->isEmpty())
                                                <tr>
                                                    <td colspan="7" class="text-center">No Payments found.</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div><!-- card end -->
                    </div>
                </div>
            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>
    @include('admin_panel.include.footer_include')