@include('admin_panel.include.header_include')

<style>
    @media print {
        /* Hide unnecessary elements */
        .page-wrapper .navbar-wrapper,
        .page-wrapper .sidebar,
        .d-flex.justify-content-between.mt-4 { 
            display: none; 
        }

        /* Adjust the layout for print */
        .container.my-4 {
            margin-top: 0;
            padding-top: 0;
        }

        /* Ensure the invoice starts at the top */
        .body-wrapper {
            margin-top: 0;
        }
        .print-btn,
        .back-btn
        {
            display: none;
        }

    }
</style>

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
                <div class="container my-4">
                    <div class="row">
                        <div class="col-12 text-center mb-4">
                            <!-- Logo Section -->
                            <img src="{{ asset('assets/admin/images/logo.png') }}" alt="Company Logo" class="mb-3" style="max-width: 150px;">
                            <h2>Purchase  Invoice</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Invoice Header -->
                                    <div class="d-flex justify-content-between mb-4">
                                        <div>
                                            <h5 class="card-title">Invoice No: <strong>{{ $purchase->invoice_no }}</strong></h5>
                                            <p><strong>Purchase Date:</strong> {{ $purchase->purchase_date }}</p>
                                        </div>
                                        <div>
                                            <p><strong>Supplier:</strong> {{ $purchase->supplier }}</p>
                                            <p><strong>Warehouse:</strong> {{ $purchase->warehouse_id }}</p>
                                        </div>
                                    </div>

                                    <!-- Items Table -->
                                    <h5 class="mt-4">Items</h5>
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Category</th>
                                                <th>Name</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchase->item_name as $index => $item)
                                            <tr>
                                                <td>{{ $purchase->item_category[$index] }}</td>
                                                <td>{{ $item }}</td>
                                                <td>{{ $purchase->quantity[$index] }}</td>
                                                <td>{{ $purchase->price[$index] }}</td>
                                                <td>{{ $purchase->total[$index] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" style="border: none;"></td>
                                                <td colspan="2"><strong>Total Price:</strong></td>
                                                <td>{{ $purchase->total_price }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border: none;"></td>
                                                <td colspan="2"><strong>Discount:</strong></td>
                                                <td>{{ $purchase->discount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border: none;"></td>
                                                <td colspan="2"><strong>Payable Amount:</strong></td>
                                                <td>{{ $purchase->Payable_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border: none;"></td>
                                                <td colspan="2"><strong>Paid Amount:</strong></td>
                                                <td>{{ $purchase->paid_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border: none;"></td>
                                                <td colspan="2"><strong>Due Amount:</strong></td>
                                                <td>{{ $purchase->due_amount }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <!-- Print and Back Buttons -->
                                    <div class="d-flex justify-content-between mt-4">
                                        <button class="btn btn-danger print-btn" onclick="window.print()">
                                            <i class="la la-print"></i> Print Invoice
                                        </button>
                                        <a href="{{ url()->previous() }}" class="btn btn-secondary back-btn">
                                            <i class="la la-arrow-left"></i> Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->

    </div>

    @include('admin_panel.include.footer_include')
</body>
