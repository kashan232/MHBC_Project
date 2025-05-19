@include('admin_panel.include.header_include')

<style>
    .search-container {
        position: relative;
        width: 100%;
        /* Adjust width as needed */
    }

    #productSearch {
        width: 100%;
        padding: 8px;
    }

    #searchResults {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background-color: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .search-result-item {
        padding: 10px;
        cursor: pointer;
    }

    .search-result-item:hover {
        background-color: #f0f0f0;
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

                <div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center">
                    <h6 class="page-title">Add Sale</h6>
                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
                        <a href="https://script.viserlab.com/torylab/admin/purchase/all"
                            class="btn btn-sm btn-outline--primary">
                            <i class="la la-undo"></i> Back</a>
                    </div>
                </div>

                <div class="row gy-3">
                    <div class="col-lg-12 col-md-12 mb-30">
                        <div class="card">
                            <div class="card-body">
                                @if (session()->has('error'))
                                <div class="alert alert-danger">
                                    <strong>Error!</strong> {{ session('error') }}.
                                </div>
                                @endif
                                <form action="{{ route('store-Sale') }}" method="POST">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Sale Type</label>
                                                <select name="sale_type" id="sale_type" class="form-control" required>
                                                    <option value="" disabled selected>Select Sale Type</option>
                                                    <option value="credit">Credit</option>
                                                    <option value="cash">Cash</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="form-group" id="credit-customer-wrapper">
                                                <label class="form-label">Customers</label>
                                                <select name="customer_info" class="select2-basic form-control" id="customer-select">
                                                    <option selected disabled>Select One</option>
                                                    @foreach($Customers as $Customer)
                                                    <option value="{{ $Customer->id . '|' . $Customer->customer_name }}">{{ $Customer->customer_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group d-none" id="cash-customer-wrapper">
                                                <label class="form-label">Customer Name</label>
                                                <input type="text" name="cash_customer_name" class="form-control" placeholder="Enter Customer Name">
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Date</label>
                                                <input name="sale_date" type="date" data-language="en"
                                                    class="datepicker-here form-control bg--white"
                                                    value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Warehouse</label>
                                                <select name="warehouse_id" class="form-control " required>
                                                    <option selected disabled>Select One</option>
                                                    @foreach($Warehouses as $Warehouse)
                                                    <option value="{{ $Warehouse->name }}">{{ $Warehouse->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Product Items List -->
                                    <div class="row mt-2 mb-2">
                                        <div class="search-container">
                                            <label class="form-label" style="font-size: 20px;">Search Products</label>
                                            <input type="text" id="productSearch" placeholder="Search Products..." class="form-control">
                                            <ul id="searchResults" class="list-group"></ul>
                                        </div>



                                    </div>
                                    <div class="row mb-3">
                                        <div class="table-responsive">
                                            <table class="productTable table border">
                                                <thead class="border bg--dark">
                                                    <tr>
                                                        <th>Category</th>
                                                        <th>Name</th>
                                                        <th>Unit</th>
                                                        <th>Quantity<span class="text--danger">*</span></th>
                                                        <th>Price<span class="text--danger">*</span></th>
                                                        <th>Total</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="purchaseItems">
                                                </tbody>

                                            </table>
                                            <button type="button" class="btn btn-primary mt-4 mb-4" id="addRow">Add More</button>
                                        </div>
                                    </div>

                                    <div class="row mt-2 mb-2">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="row g-3"> <!-- g-3 adds gap between items -->

                                                <div class="col-6">
                                                    <label for="total_price" class="form-label">Total Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Pkr</span>
                                                        <input type="number" id="total_price" name="total_price" class="form-control total_price" required readonly>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <label for="discount" class="form-label">Discount</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Pkr</span>
                                                        <input type="number" id="discount" name="discount" class="form-control" step="any">
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <label for="scrap_amount" class="form-label">Scrap Amount</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Pkr</span>
                                                        <input type="number" id="scrap_amount" name="scrap_amount" class="form-control" step="any">
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <label for="payable_amount" class="form-label">Payable Amount</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Pkr</span>
                                                        <input type="number" id="payable_amount" name="payable_amount" class="form-control payable_amount" readonly>
                                                    </div>
                                                </div>


                                                <div class="col-6">
                                                    <label for="previous_balance" class="form-label">Previous Balance</label>
                                                    <input type="text" id="previous_balance" class="form-control" name="previous_balance" readonly>
                                                </div>

                                                <div class="col-6">
                                                    <label for="closing_balance" class="form-label">Closing Balance</label>
                                                    <input type="text" id="closing_balance" name="closing_balance" class="form-control" readonly>
                                                </div>

                                                <div class="col-6 d-none" id="cash-received-group">
                                                    <label for="cashReceived" class="form-label">Cash Received</label>
                                                    <input type="number" id="cashReceived" name="cash_received" class="form-control">
                                                </div>

                                                <div id="cash-return-group" class="form-group col-6">
                                                    <label for="cash_return">Cash Return</label>
                                                    <input type="text" id="cash_return" name="cash_return" class="form-control" readonly>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn--primary w-100 h-45">Submit</button>
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

            function toggleFieldsBasedOnSaleType(type) {
                if (type === 'credit') {
                    $('#credit-customer-wrapper').removeClass('d-none');
                    $('#cash-customer-wrapper').addClass('d-none');

                    $('#previous_balance').closest('.col-6').removeClass('d-none');
                    $('#closing_balance').closest('.col-6').removeClass('d-none');

                    $('#cash-received-group').addClass('d-none');
                    $('#cash-return-group').addClass('d-none');

                } else if (type === 'cash') {
                    $('#credit-customer-wrapper').addClass('d-none');
                    $('#cash-customer-wrapper').removeClass('d-none');

                    $('#previous_balance').closest('.col-6').addClass('d-none');
                    $('#closing_balance').closest('.col-6').addClass('d-none');

                    $('#cash-received-group').removeClass('d-none');
                    $('#cash-return-group').removeClass('d-none');
                }
            }

            // Initial load: hide all conditional fields
            $('#credit-customer-wrapper, #cash-customer-wrapper').addClass('d-none');
            $('#previous_balance').closest('.col-6').addClass('d-none');
            $('#closing_balance').closest('.col-6').addClass('d-none');
            $('#cash-received-group').addClass('d-none');
            $('#cash-return-group').addClass('d-none');

            // Trigger logic on sale type change
            $('#sale_type').on('change', function() {
                const type = $(this).val();
                toggleFieldsBasedOnSaleType(type);
            });

            // Optionally, trigger on page load if sale_type is already selected
            const preSelectedType = $('#sale_type').val();
            if (preSelectedType) {
                toggleFieldsBasedOnSaleType(preSelectedType);
            }


            // Customer selection change
            $('#customer-select').change(function() {
                const customerData = $(this).val().split('|');
                const customerId = customerData[0];
                // alert(customerId);
                if (customerId) {
                    $.ajax({
                        url: "{{ route('get-customer-amount', ':id') }}".replace(':id', customerId),
                        type: 'GET',
                        success: function(response) {
                            $('#previous_balance').val(response.previous_balance || 0);
                            updateClosingBalance(); // Calculate closing balance initially
                        },
                        error: function(xhr) {
                            console.error("Error fetching customer amount: ", xhr);
                        }
                    });
                }
            });

            // Update total price and payable amount on input change
            $('input[name="total_price"]').on('input', calculateTotalPrice);
            $('#discount').on('input', calculatePayableAmount);
            $('#cashReceived').on('input', updateClosingBalance); // Trigger closing balance update on cash received input
            $('.total_price, #discount, #scrap_amount, #cashReceived').on('input', function() {
                calculatePayableAmount();
                updateClosingBalance();
            });

            // Function to calculate total price
            function calculateTotalPrice() {
                let total = 0;
                $('#purchaseItems tr').each(function() {
                    const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                    const price = parseFloat($(this).find('.price').val()) || 0;
                    total += quantity * price;
                });

                $('.total_price').val(total.toFixed(2));
                calculatePayableAmount(); // Update payable amount
            }

            // Function to calculate payable amount
            function calculatePayableAmount() {
                const totalPrice = parseFloat($('.total_price').val()) || 0;
                const discount = parseFloat($('#discount').val()) || 0;
                const scrapAmount = parseFloat($('#scrap_amount').val()) || 0;

                // Payable Amount Formula
                const payableAmount = Math.max(0, totalPrice - discount - scrapAmount);

                $('.payable_amount').val(payableAmount.toFixed(2));
                updateClosingBalance(); // Update closing balance
            }

            // Function to update closing balance
            function updateClosingBalance() {
                const previousBalance = parseFloat($('#previous_balance').val()) || 0;
                const payableAmount = parseFloat($('.payable_amount').val()) || 0;
                const cashReceived = parseFloat($('#cashReceived').val()) || 0;

                const closingBalance = previousBalance + payableAmount - cashReceived;
                $('#closing_balance').val(closingBalance.toFixed(2));

                // Calculate cash return only if cash received is more than payable amount
                const cashReturn = cashReceived > payableAmount ? (cashReceived - payableAmount) : 0;
                $('#cash_return').val(cashReturn.toFixed(2));
            }

            // Add a new row
            $('#addRow').click(function() {
                const newRow = createNewRow();
                $('#purchaseItems').append(newRow);
                calculateTotalPrice();
            });


            // Function to create a new row
            function createNewRow(category = '', productName = '', price = '') {
                return `
            <tr>
                <td>
                    <select name="item_category[]" class="form-control item-category" style="width:150px;" required>
                        <option value="" disabled ${category ? '' : 'selected'}>Select Category</option>
                        @foreach($Category as $Categories)
                            <option value="{{ $Categories->category }}" ${category === '{{ $Categories->category }}' ? 'selected' : ''}>{{ $Categories->category }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="item_name[]" class="form-control item-name" style="width:180px;" required>
                        <option value="" disabled ${productName ? '' : 'selected'}>Select Item</option>
                        <option value="${productName}" selected>${productName}</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="unit[]" style="width:150px;" class="form-control unit" readonly>
                </td>
                <td><input type="number" name="quantity[]" style="width:150px;" class="form-control quantity" required></td>
                <td><input type="number" name="price[]" style="width:150px;" class="form-control price" value="${price}" required></td>
                <td><input type="number" name="total[]" style="width:150px;" class="form-control total" readonly></td>
                <td>
                    <button type="button" class="btn btn-danger remove-row">Delete</button>
                </td>
            </tr>`;
            }

            // Remove a row
            $('#purchaseItems').on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotalPrice();
            });

            // Update row total on quantity or price change
            $('#purchaseItems').on('input', '.quantity, .price', function() {
                const row = $(this).closest('tr');
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const price = parseFloat(row.find('.price').val()) || 0;
                row.find('.total').val((quantity * price).toFixed(2));
                calculateTotalPrice();
            });

            // Fetch items based on category
            $('#purchaseItems').on('change', '.item-category', function() {
                const categoryName = $(this).val();
                const row = $(this).closest('tr');
                const itemSelect = row.find('.item-name');

                if (categoryName) {
                    fetch(`{{ route('get-items-by-category', ':categoryId') }}`.replace(':categoryId', categoryName))
                        .then(response => response.json())
                        .then(items => {
                            itemSelect.html('<option value="" disabled selected>Select Item</option>');
                            items.forEach(item => {
                                itemSelect.append(`<option value="${item.product_name}">${item.product_name}</option>`);
                            });
                        })
                        .catch(error => console.error('Error fetching items:', error));
                }
            });

            // Fetch product details based on selected product
            $('#purchaseItems').on('change', '.item-name', function() {
                const productName = $(this).val();
                const row = $(this).closest('tr');
                const priceInput = row.find('.price');
                const unitInput = row.find('.unit');

                if (productName) {
                    fetch(`{{ route('get-product-details', ':productName') }}`.replace(':productName', productName))
                        .then(response => response.json())
                        .then(product => {
                            priceInput.val(product.retail_price);
                            unitInput.val(product.unit); // Update unit
                        })
                        .catch(error => console.error('Error fetching product details:', error));
                }
            });

            // Search product functionality
            $('#productSearch').on('keyup', function() {
                const query = $(this).val();
                if (query.length > 0) {
                    $.ajax({
                        url: "{{ route('search-products') }}",
                        type: 'GET',
                        data: {
                            q: query
                        },
                        success: displaySearchResults,
                        error: function(error) {
                            console.error('Error in product search:', error);
                        }
                    });
                } else {
                    $('#searchResults').html('');
                }
            });

            // Display search results
            function displaySearchResults(products) {
                const searchResults = $('#searchResults');
                searchResults.html('');
                products.forEach(product => {
                    const listItem = `<li class="list-group-item search-result-item" data-category="${product.category}" data-product-name="${product.product_name}" data-price="${product.retail_price}">
                    ${product.category} - ${product.product_name} - ${product.retail_price}
                </li>`;
                    searchResults.append(listItem);
                });
            }

            // Add searched product as a new row
            $('#searchResults').on('click', '.search-result-item', function() {
                const category = $(this).data('category');
                const productName = $(this).data('product-name');
                const price = $(this).data('price');

                const newRow = createNewRow(category, productName, price);
                $('#purchaseItems').append(newRow);
                $('#searchResults').html('');
                calculateTotalPrice();
            });
        });
    </script>

</body>