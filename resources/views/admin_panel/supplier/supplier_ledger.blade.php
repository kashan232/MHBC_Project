@include('admin_panel.include.header_include')

<body>
    <div class="page-wrapper default-version">
        @include('admin_panel.include.sidebar_include')
        @include('admin_panel.include.navbar_include')
        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
                    <h6 class="page-title">Suppliers Ledger</h6>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card b-radius--10">
                            <div class="card-body p-0">
                                <div class="table-responsive--sm table-responsive">
                                    <table id="example" class="display  table table--light style--two bg--white" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Name</th>
                                                <th>Opening Balance</th>
                                                <th>Previous Balance</th>
                                                <th>Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($SupplierLedgers->isEmpty())
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function() {
                                                    document.getElementById("global-loader").style.display = "none";
                                                });
                                            </script>
                                            @endif
                                            @forelse($SupplierLedgers as $ledger)
                                            <tr>
                                                <td>{{ $ledger->supplier_id }}</td>
                                                <td>{{ $ledger->updated_at->format('Y-m-d') }}</td>
                                                <td>{{ $ledger->supplier_id }}</td>
                                                <td>{{ number_format($ledger->opening_balance, 0) }}</td>
                                                <td>{{ number_format($ledger->previous_balance, 0) }}</td>
                                                <td id="closing_balance_{{ $ledger->id }}">{{ number_format($ledger->closing_balance, 0) }}</td>

                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No records found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div><!-- card end -->
                        </div>
                    </div>
                </div><!-- bodywrapper__inner end -->
            </div><!-- body-wrapper end -->
        </div>
        @include('admin_panel.include.footer_include')