@include('admin_panel.include.header_include')

<body>
    <!-- page-wrapper start -->
    <div class="page-wrapper default-version">
        <div class="modal fade" id="editRecoveryModal" tabindex="-1" aria-labelledby="editRecoveryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editRecoveryForm" method="POST" action="{{ route('customer.recovery.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="recovery_id" id="edit_recovery_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Customer Recovery</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" id="edit_customer_name" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Recovery Date</label>
                                <input type="date" name="recovery_date" id="edit_recovery_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Previous Recovery Amount</label>
                                <input type="number" id="edit_recovery_amount" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Transaction Type</label>
                                <select name="adjustment_type" id="adjustment_type" class="form-control">
                                    <option value="plus">Plus</option>
                                    <option value="minus">Minus</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" name="adjustment_amount" id="adjustment_amount" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Closing Balance</label>
                                <input type="text" id="edit_closing_balance" class="form-control" readonly>
                            </div>

                            <!-- Hidden fields for calculated values -->
                            <input type="hidden" name="updated_recovery_amount" id="updated_recovery_amount">
                            <input type="hidden" name="updated_closing_balance" id="updated_closing_balance">
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100">Update Recovery</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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
                                                <th>Action</th>
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
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline--primary editRecoveryBtn"
                                                        data-id="{{ $Customer->id }}"
                                                        data-name="{{ $Customer->customer_name }}"
                                                        data-date="{{ $Customer->recovery_date }}"
                                                        data-amount="{{ $Customer->recovery_amount }}"
                                                        data-closing="{{ $Customer->closing_balance }}"
                                                        data-toggle="modal"
                                                        data-target="#editRecoveryModal">
                                                        <i class="la la-edit"></i> Edit
                                                    </button>
                                                </td>

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
    <script>
        function calculateRecoveryUpdate() {
            const type = $('#adjustment_type').val();
            const transactionAmount = parseFloat($('#adjustment_amount').val() || 0);
            const prevRecovery = parseFloat($('#edit_recovery_amount').val());
            const prevClosing = parseFloat($('#edit_closing_balance').val());

            let newRecovery = prevRecovery;
            let newClosing = prevClosing;

            if (type === 'plus') {
                newRecovery += transactionAmount;
                newClosing -= transactionAmount;
            } else {
                newRecovery -= transactionAmount;
                newClosing += transactionAmount;
            }

            $('#updated_recovery_amount').val(newRecovery);
            $('#updated_closing_balance').val(newClosing);
        }


        $('#transaction_type, #transaction_amount').on('input change', function() {
            calculateRecoveryUpdate();
        });

        $('.editRecoveryBtn').click(function() {
            $('#edit_recovery_id').val($(this).data('id'));
            $('#edit_customer_name').val($(this).data('name'));
            $('#edit_recovery_date').val($(this).data('date'));
            $('#edit_recovery_amount').val($(this).data('amount'));
            $('#edit_closing_balance').val($(this).data('closing'));

            // Reset adjustment values
            $('#adjustment_type').val('plus');
            $('#adjustment_amount').val('');
        });

        // Submit se pehle adjustment_type and adjustment_amount ko manually append karke bhej do:
        $('#editRecoveryForm').submit(function(e) {
            // Optional: Prevent default and re-submit after appending
            // e.preventDefault();

            let type = $('#adjustment_type').val();
            let amount = $('#adjustment_amount').val();

            // Remove old if exist
            $('input[name="adjustment_type"]').remove();
            $('input[name="adjustment_amount"]').remove();

            // Add hidden inputs
            $('<input>').attr({
                type: 'hidden',
                name: 'adjustment_type',
                value: type
            }).appendTo('#editRecoveryForm');

            $('<input>').attr({
                type: 'hidden',
                name: 'adjustment_amount',
                value: amount
            }).appendTo('#editRecoveryForm');
        });
    </script>