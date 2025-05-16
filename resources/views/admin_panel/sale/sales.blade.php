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
                    <h6 class="page-title">All Sales</h6>
                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
                        <a href="{{ route('add-Sale') }}"
                            class="btn btn-outline--primary h-45">
                            <i class="la la-plus"></i>Add New </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card b-radius--10 bg--transparent">
                            <div class="card-body p-0 ">
                                @if (session()->has('success'))
                                <div class="alert alert-success">
                                    <strong>Success!</strong> {{ session('success') }}.
                                </div>
                                @endif
                                <div class="table-responsive--md table-responsive">
                                    <table id="example" class="display  table table--light style--two bg--white" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Invoice No. | Date</th>
                                                <th>Customer | Warehouse </th>
                                                <th>Total Amount </th>
                                                <th>Discount </th>
                                                <th>Payable </th>
                                                <th>Received </th>
                                                <!-- <th>Recipt </th> -->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($Sales as $Sale)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold">
                                                        {{ $Sale->invoice_no }}
                                                    </span>
                                                    <br>
                                                    <small>{{ $Sale->sale_date }}</small>
                                                </td>

                                                <td>
                                                    <span class="text--primary fw-bold"> {{ $Sale->customer}} <br>
                                                        {{ $Sale->warehouse_id }} </span>
                                                </td>

                                                <td>
                                                    <span class="fw-bold">{{ $Sale->total_price }} </span>

                                                </td>
                                                <td>
                                                    {{ $Sale->discount }}
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $Sale->Payable_amount }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $Sale->cash_received }}</span>
                                                </td>
                                                <!-- <td>
                                                    <a class="dropdown-item btn btn-sm btn-outline--primary ms-1 editBtn" href="#"> <i class="la la-pen"></i> Edit</a>
                                                </td> -->
                                                <td>
                                                    <a class="btn btn-sm btn-primary me-2 editBtn" href="#">
                                                        <i class="la la-pen"></i> Edit
                                                    </a>
                                                    <a class="btn btn-sm btn-danger" href="{{ route('invoice.download', ['id' => $Sale->id]) }}">
                                                        <i class="la la-file"></i> Invoice
                                                    </a>
                                                </td>


                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table><!-- table end -->
                                </div>
                            </div>
                        </div><!-- card end -->
                    </div>
                </div>

                <!-- Payment Modal -->
                <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">Make Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('purchases-payment') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="purchase_id" class="form-label">Purchase ID</label>
                                        <input type="text" class="form-control" id="purchase_id" name="purchase_id" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="invoice_no" class="form-label">Invoice No</label>
                                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="supplier" class="form-label">Supplier</label>
                                        <input type="text" class="form-control" id="supplier" name="supplier" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payable_amount" class="form-label">Payable Amount</label>
                                        <input type="text" class="form-control" id="payable_amount" name="payable_amount" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="paid_amount" class="form-label">Paying Amount</label>
                                        <input type="text" class="form-control" id="paid_amount" name="paid_amount" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Make Payment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->

    </div>
    @include('admin_panel.include.footer_include')
    <script>
        $(document).ready(function() {
            $('.paymentBtn').on('click', function() {
                var purchaseId = $(this).data('id');
                var invoiceNo = $(this).data('invoice_no');
                var supplier = $(this).data('supplier');
                var payableAmount = $(this).data('payable_amount');

                $('#purchase_id').val(purchaseId);
                $('#invoice_no').val(invoiceNo);
                $('#supplier').val(supplier);
                $('#payable_amount').val(payableAmount);

                $('#paymentModal').modal('show');
            });
        });
    </script>