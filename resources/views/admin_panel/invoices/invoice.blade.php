<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .invoice-container {
            width: 100%;
            background-color: #fff;
            padding: 30px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .invoice-header .row {
            display: flex;
            align-items: center;
        }

        .invoice-header .logo img {
            width: 150px;
        }

        .invoice-header .details {
            text-align: right;
            flex-grow: 1;
        }

        .billing-info {
            padding: 15px;
            background-color: #f1f1f1;
            margin-bottom: 20px;
        }

        .invoice-table {
            width: 100%;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="row p-2">
                <div class="col-md-6 d-flex align-items-center">
                    <div class="logo">
                        <!-- <img src="assets/admin/images/logo.png" alt="Logo"> -->
                        <img src="{{ asset('assets/admin/images/logo.png') }}" alt="Logo">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="details text-right">
                        <h1>Invoice</h1>
                        <span><strong>Invoice No:</strong> #{{ $sale->invoice_no }}</span><br>
                        <span><strong>Date:</strong> {{ date('d F Y', strtotime($sale->sale_date)) }}</span><br>
                        @if ($sale->sale_type === 'cash')
                        <span><strong>Name:</strong> {{ $sale->customer }}</span><br>
                        @else
                        <span><strong>Name:</strong> {{ $customer->customer_name }}</span><br>
                        <span><strong>Mobile:</strong> {{ $customer->customer_phone }}</span><br>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row p-2 mt-2">
                <div class="col-md-12 text-right">
                    <strong>Qazi Qayum Road, Ghari Khatan, Hyderabad</strong><br>
                    <strong>Contact: 0311-0876473 | Hasnain Shaikh</strong>
                </div>
            </div>
        </div>

        <table class="table table-bordered invoice-table mt-3">
            <thead class="text-white" style="background: #004cac;">
                <tr>
                    <th>Item Category</th>
                    <th>Item Name</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $categories = json_decode($sale->item_category);
                $names = json_decode($sale->item_name);
                $unit = json_decode($sale->unit);
                $quantities = json_decode($sale->quantity);
                $prices = json_decode($sale->price);
                $totals = json_decode($sale->total);
                @endphp

                @for ($i = 0; $i < count($categories); $i++)
                    <tr>
                    <td>{{ $categories[$i] }}</td>
                    <td>{{ $names[$i] }}</td>
                    <td>{{ $unit[$i] }}</td>
                    <td>{{ $quantities[$i] }}</td>
                    <td>{{ number_format($prices[$i], 0) }}</td>
                    <td>{{ number_format($totals[$i], 0) }}</td>
                    </tr>
                    @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td><strong>SubTotal:</strong></td>
                    <td>{{ number_format($sale->total_price, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Discount:</strong></td>
                    <td>{{ number_format($sale->discount, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Scrap Amount:</strong></td>
                    <td>{{ number_format($sale->scrap_amount, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Net Amount:</strong></td>
                    <td>{{ number_format($sale->Payable_amount, 0) }}</td>
                </tr>

                @if($sale->sale_type === 'cash')
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Cash Received:</strong></td>
                    <td>{{ number_format($sale->cash_received, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Cash Return:</strong></td>
                    <td>{{ number_format($sale->change_return, 0) }}</td>
                </tr>
                @elseif($sale->sale_type === 'credit' && isset($creditInfo))
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Previous Balance:</strong></td>
                    <td>{{ number_format($creditInfo->net_total, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Closing Balance:</strong></td>
                    <td>{{ number_format($creditInfo->closing_balance, 0) }}</td>
                </tr>
                @endif
            </tfoot>

        </table>

        <div class="footer">
            <strong>For Home Delivery or Assistance, Contact Us Now!</strong><br>
            Software Developed by ProWave Software Solutions<br>
            +92 317 3836223 | +92 317 3859647
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>