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
                <h6 class="page-title">Purchase Reports</h6>
            </div>
            <div class="row mb-none-30">
                <div class="col-lg-12 col-md-12 mb-30">
                    <div class="card">
                        <div class="card-body">
                            <form id="purchaseFilterForm">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="row gy-4 justify-content-end align-items-end">
                                            <div class="col-lg-4">
                                                <label class="required">Start Date</label>
                                                <input type="date" class="form-control" name="start_date"
                                                    id="start_date" required>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="required">End Date</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    id="end_date" required>
                                            </div>

                                            <div class="col-lg-4">
                                                <button class="btn btn--primary h-45 w-100" type="button"
                                                    id="filterpurchaseBtn">
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
                            <!-- purchase Report Table -->
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="table table-responsive">
                                        <table
                                            class="display table table--light style--two bg--white dataTable no-footer"
                                            style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Invoice No</th>
                                                    <th>Purchase Date</th>
                                                    <th> Warehouse </th>
                                                    <th>Supplier Name</th>
                                                    <th>Items Category</th>
                                                    <th>Items Name</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchaseTableBody">
                                                <!-- Filtered purchase Data Will Append Here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
        let headers = ['Invoice No', 'Purchase Date', 'Warehouse', 'Supplier Name', 'Items Category', 'Item Name',
            'Quantity'
        ];

        // Get the purchase data
        const rows = [];
        let totalDiscount = 0;
        let totalNetAmount = 0;

        $('#purchaseTableBody tr').each(function() {
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
        doc.text(Total Discount: $ {
            totalDiscount.toFixed(2)
        }, 10, doc.lastAutoTable.finalY + 10);
        doc.text(Total Net Amount: $ {
            totalNetAmount.toFixed(2)
        }, 10, doc.lastAutoTable.finalY + 20);

        // Add Date to PDF
        const currentDate = new Date().toLocaleDateString();
        doc.text(Report Date: $ {
            currentDate
        }, 10, doc.lastAutoTable.finalY + 30);

        // Save the PDF
        doc.save('purchase_report.pdf');
    }
</script>
<script>
    $(document).ready(function() {
        $('#filterpurchaseBtn').click(function() {
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();
            $.ajax({
                url: "{{ route('filter.purchase') }}",
                method: "GET",
                data: {
                    start_date: start_date,
                    end_date: end_date
                },
                beforeSend: function() {
                    $('#purchaseTableBody').html(
                        '<tr><td colspan="7" class="text-center">Loading...</td></tr>');
                    $('#totalDiscount').text("0");
                    $('#totalNetAmount').text("0");
                },
                success: function(response) {
    console.log(response); // Check if the response is correct

    let tableData = "";
    let totalDiscount = 0;
    let totalNetAmount = 0;

    if (response.length > 0) {
        response.forEach(purchase => {
            // Parse JSON fields
            const itemCategory = purchase.item_category ? JSON.parse(purchase.item_category)[0] || 'N/A' : 'N/A';
            const itemName = purchase.item_name ? JSON.parse(purchase.item_name)[0] || 'N/A' : 'N/A';
            const quantity = purchase.quantity ? JSON.parse(purchase.quantity)[0] || '0' : '0';
            const price = purchase.price ? JSON.parse(purchase.price)[0] || '0' : '0';
            const total = purchase.total ? JSON.parse(purchase.total)[0] || '0' : '0';

            // Build the table row
            tableData += `<tr>
                <td>${purchase.invoice_no || 'N/A'}</td>
                <td>${purchase.purchase_date || 'N/A'}</td>
                <td>${purchase.warehouse_id || 'N/A'}</td>
                <td>${purchase.supplier || 'N/A'}</td>
                <td>${itemCategory}</td>
                <td>${itemName}</td>
                <td>${quantity}</td>
                <td>${price}</td>
                <td>${total}</td>
            </tr>`;
        });
    } else {
        tableData =
            '<tr><td colspan="7" class="text-center">No purchases found for the selected date range.</td></tr>';
    }

    $('#purchaseTableBody').html(tableData);
},

                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });

        });
    });
</script>
