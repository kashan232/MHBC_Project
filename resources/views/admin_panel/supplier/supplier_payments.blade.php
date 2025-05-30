@include('admin_panel.include.header_include')

<body>
    <div class="page-wrapper default-version">
        @include('admin_panel.include.sidebar_include')
        @include('admin_panel.include.navbar_include')
        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
                    <h6 class="page-title">Suppliers Payments Management</h6>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card b-radius--10">
                            <div class="card-body p-0">
                                <form action="{{ route('supplier-payment-store') }}" method="POST">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="Vendor" class="form-label text-dark">Payment TO <span class="text-danger">*</span></label>
                                            <select id="Vendor" name="supplier_id" class="form-control">
                                                <option selected disabled>Select Vendor</option>
                                                @foreach($Suppliers as $Vendor)
                                                <option value="{{ $Vendor->name }}">{{ $Vendor->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="amount" class="form-label text-dark">Payment Amount (PKR) <span class="text-danger">*</span></label>
                                            <input type="number" id="amount" name="amount" class="form-control" placeholder="Enter payment amount">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="date" class="form-label text-dark">Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" id="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="detail" class="form-label text-dark">Payment Method Details (e.g. JazzCash, EasyPaisa)</label>
                                        <input type="text" id="detail" name="detail" class="form-control" placeholder="Enter additional payment details">
                                    </div>

                                    <div class="text-end fw-bold text-secondary mb-3">
                                        Vendor Balance: <span id="supplier_balance" class="text-dark">PKR 0</span>
                                    </div>

                                    <div class="table-responsive mb-4">
                                        <label class="form-label text-dark d-block mb-2">Vendor Purchase History</label>
                                        <table class="table table-bordered" id="purchase_table">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th>Purchase Date</th>
                                                    <th>Amount (PKR)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Will be populated via JavaScript --}}
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="d-flex justify-content-center gap-3">
                                        <button type="submit" class="btn btn-success">Save & Close</button>
                                        <button type="submit" class="btn btn-primary">Save & Add New</button>
                                    </div>

                                </form>
                            </div>
                        </div><!-- card end -->
                    </div>
                </div>
            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>
    @include('admin_panel.include.footer_include')
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const select = document.getElementById("Vendor");
        select.addEventListener("change", function() {
            const selectedId = this.value;
            fetchVendorData(selectedId);
        });
    });

    const baseUrl = "{{ url('/get-supplier-balance') }}";

    function fetchVendorData(vendorId) {
        let url = `${baseUrl}/${vendorId}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                console.log("Data received:", data);
                document.getElementById('supplier_balance').innerText = 'PKR ' + (data.balance ?? 0);
                let tbody = document.querySelector('#purchase_table tbody');
                tbody.innerHTML = '';

                if (data.purchases && data.purchases.length > 0) {
                    data.purchases.forEach(row => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${new Date(row.purchase_date).toLocaleDateString()}</td>
                                <td>${row.Payable_amount}</td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="2">No purchases found</td></tr>`;
                }
            })
            .catch(err => console.error('Fetch error:', err));
    }
</script>
