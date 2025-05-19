<!-- meta tags and other links -->
@include('admin_panel.include.header_include')

<!-- page-wrapper start -->
<div class="page-wrapper default-version">
    @include('admin_panel.include.sidebar_include')
    <!-- sidebar end -->

    <!-- navbar-wrapper start -->
    @include('admin_panel.include.navbar_include')
    <!-- navbar-wrapper end -->

    <div class="body-wrapper">
        <div class="bodywrapper__inner">
            <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
                <h6 class="page-title">Sale Reports</h6>
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
                            <!-- Sales Report Table -->
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
                                                    <th>Total Price</th>
                                                    <th>Discount</th>
                                                    <th>Scrap Amount</th>
                                                    <th>Payable Amount</th>
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
                                <h5 class="text-danger">Total Discount: <strong id="totalDiscount">0</strong></h5>
                                <h5 class="text-danger">Total Scrap Amount: <strong id="totalScrapAmount">0</strong></h5>
                                <h5 class="text-danger">Total Net Amount: <strong id="totalNetAmount">0</strong></h5>
                            </div>
                            <!-- <button id="generatePDFBtn" class="btn btn--primary" onclick="generatePDF()">Generate PDF</button> -->
                        </div>
                    </div>
                </div><!-- bodywrapper__inner end -->
            </div><!-- body-wrapper end -->
        </div>
        @include('admin_panel.include.footer_include')
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // PDF generation script
    function generatePDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();

        // Table Heading
        let headers = ['Invoice No', 'Customer', 'Sale Date', 'Items', 'Quantity', 'Total Price', 'Discount', 'Payable Amount'];

        // Get the sales data
        const rows = [];
        let totalDiscount = 0;
        let totalNetAmount = 0;

        $('#salesTableBody tr').each(function() {
            let row = [];
            $(this).find('td').each(function(index) {
                if (index === 6) { // Discount column
                    totalDiscount += parseFloat($(this).text());
                }
                if (index === 7) { // Payable Amount column
                    totalNetAmount += parseFloat($(this).text());
                }
                row.push($(this).text());
            });
            rows.push(row);
        });

        // Add table to PDF
        doc.autoTable({
            head: [headers],
            body: rows
        });

        // Add the totals to the PDF
        doc.text(`Total Discount: ${totalDiscount.toFixed(2)}`, 10, doc.lastAutoTable.finalY + 10);
        doc.text(`Total Net Amount: ${totalNetAmount.toFixed(2)}`, 10, doc.lastAutoTable.finalY + 20);

        // Add Date to PDF
        const currentDate = new Date().toLocaleDateString();
        doc.text(`Report Date: ${currentDate}`, 10, doc.lastAutoTable.finalY + 30);

        // Save the PDF
        doc.save('sales_report.pdf');
    }

    $(document).ready(function() {
        $('#filterSalesBtn').click(function() {
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();

            $.ajax({
                url: "{{ route('filter.sales') }}", // Adjust route accordingly
                method: "GET",
                data: {
                    start_date: start_date,
                    end_date: end_date
                },
                beforeSend: function() {
                    $('#salesTableBody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                    $('#totalDiscount').text("0");
                    $('#totalNetAmount').text("0");
                },
                success: function(response) {
                    let tableData = "";
                    let totalDiscount = 0;
                    let totalNetAmount = 0;
                    let totalScrapAmount = 0;

                    if (response.length > 0) {
                        response.forEach(sale => {
                            let discount = parseInt(sale.discount);
                            let payable = parseInt(sale.Payable_amount);
                            let scrapAmount = parseInt(sale.scrap_amount);

                            totalDiscount += discount;
                            totalNetAmount += payable;
                            totalScrapAmount += scrapAmount;

                            tableData += `<tr>
                <td>${sale.invoice_no}</td>
                <td>${sale.customer}</td>
                <td>${sale.sale_date}</td>
                <td>${JSON.parse(sale.item_name).join(", ")}</td>
                <td>${JSON.parse(sale.quantity).join(", ")}</td>
                <td>${sale.total_price}</td>
                <td>${discount}</td>
                <td>${scrapAmount}</td>
                <td><strong>${payable}</strong></td>
            </tr>`;
                        });
                    } else {
                        tableData = '<tr><td colspan="9" class="text-center">No sales found for the selected date range.</td></tr>';
                    }

                    $('#salesTableBody').html(tableData);
                    $('#totalDiscount').text(totalDiscount);
                    $('#totalScrapAmount').text(totalScrapAmount);
                    $('#totalNetAmount').text(totalNetAmount);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>