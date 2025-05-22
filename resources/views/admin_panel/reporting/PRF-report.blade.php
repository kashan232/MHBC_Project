<!-- meta tags and other links -->
@include('admin_panel.include.header_include')

<div class="page-wrapper default-version">
    @include('admin_panel.include.sidebar_include')
    @include('admin_panel.include.navbar_include')

    <div class="body-wrapper">
        <div class="bodywrapper__inner">
            <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
                <h6 class="page-title">PRF Reports</h6>
            </div>
            <div class="row mb-none-30">
                <div class="col-lg-12 col-md-12 mb-30">
                    <div class="card">
                        <div class="card-body">
                            <form id="salesFilterForm">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="row gy-4 justify-content-end align-items-end">
                                            <div class="col-lg-4">
                                                <label class="required">Start Date</label>
                                                <input type="date" class="form-control" name="start_date" id="start_date" required>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="required">End Date</label>
                                                <input type="date" class="form-control" name="end_date" id="end_date" required>
                                            </div>
                                            <div class="col-lg-4">
                                                <button class="btn btn--primary h-45 w-100" type="button" id="filterSalesBtn">
                                                    <i class="la la-filter"></i> Filter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>
                    <div class="card">
                        <div class="card-body">
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="display table table--light style--two bg--white dataTable no-footer" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Invoice No</th>
                                                    <th>Customer</th>
                                                    <th>Sale Date</th>
                                                    <th>Items</th>
                                                    <th>Quantity</th>
                                                    <th>Payable Amount</th>
                                                    <th>Profit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="salesTableBody">
                                                <!-- Filtered Sales Data Will Append Here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h5 class="text-success">Total Sale: <strong id="totalSale">0</strong></h5>
                                <h5 class="text-success">Total Profit: <strong id="totalProfit">0</strong></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin_panel.include.footer_include')
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#filterSalesBtn').click(function() {
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();

            $.ajax({
                url: "{{ route('PRF-filter-sales') }}", // adjust route accordingly
                method: "GET",
                data: {
                    start_date: start_date,
                    end_date: end_date
                },
                beforeSend: function() {
                    $('#salesTableBody').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
                    $('#totalSale').text("0");
                    $('#totalProfit').text("0");
                },
                success: function(response) {
                    let tableData = "";
                    let totalSale = 0;
                    let totalProfit = 0;

                    if (response.length > 0) {
                        response.forEach(sale => {
                            let payable = parseFloat(sale.Payable_amount) || 0;

                            let itemNames = [];
                            let quantities = [];
                            let profitAmounts = [];

                            try {
                                itemNames = JSON.parse(sale.item_name);
                            } catch (e) {
                                itemNames = [sale.item_name];
                            }
                            try {
                                quantities = JSON.parse(sale.quantity);
                            } catch (e) {
                                quantities = [sale.quantity];
                            }
                            try {
                                profitAmounts = sale.profit_amounts;
                            } catch (e) {
                                profitAmounts = [];
                            }

                            totalSale += payable;
                            totalProfit += parseFloat(sale.total_profit || 0);

                            tableData += `<tr>
                <td>${sale.invoice_no}</td>
                <td>${sale.customer ?? 'N/A'}</td>
                <td>${sale.sale_date}</td>
                <td>${itemNames.join(", ")}</td>
                <td>${quantities.join(", ")}</td>
                <td>${payable.toFixed(2)}</td>
                <td>
                    Profit: ${profitAmounts.join(", ")}<br>
                    Total: <strong>${parseFloat(sale.total_profit).toFixed(2)}</strong>
                </td>
            </tr>`;
                        });
                    } else {
                        tableData = '<tr><td colspan="7" class="text-center">No sales found for the selected date range.</td></tr>';
                    }

                    $('#salesTableBody').html(tableData);
                    $('#totalSale').text(totalSale.toFixed(2));
                    $('#totalProfit').text(totalProfit.toFixed(2));
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>