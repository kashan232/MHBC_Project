<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        .invoice-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .invoice-header img {
            width: 100px;
        }

        .invoice-header .invoice-details {
            text-align: right;
        }

        .invoice-header .invoice-details h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .billing-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .billing-info div {
            width: 48%;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .billing-info h4 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #007bff;
        }

        .billing-info p {
            margin: 5px 0;
        }

        /* Items Table */
        .table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .table th, .table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        /* Total Section */
        .total-section {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }

        .total-section p {
            margin-bottom: 5px;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="invoice-container">
        <!-- Header Section -->
        <div class="invoice-header">
            <div>
                <img src="assets/admin/images/logo.png" alt="image">
            </div>
            <div class="invoice-details">
                <h1>Invoice</h1>
                <p><strong>Invoice No:</strong> #{{ $sale->invoice_no }}</p>
                <p><strong>Date:</strong> {{ date('d F Y', strtotime($sale->sale_date)) }}</p>
            </div>
        </div>

        <!-- Billing Info -->
        <div class="billing-info">
            <div>
                <h4>Billing</h4>
                <p><strong>Name:</strong> {{ $customer->customer_name }}</p>
                <p><strong>Mobile:</strong> {{ $customer->customer_phone }}</p>
                <p><strong>Email:</strong> {{ $customer->customer_email }}</p>
                <p><strong>Address:</strong> {{ $customer->customer_address }}</p>
                <p><strong>Warehouse:</strong> {{ $sale->warehouse_id }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Category</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $categories = json_decode($sale->item_category);
                $names = json_decode($sale->item_name);
                $quantities = json_decode($sale->quantity);
                $prices = json_decode($sale->price);
                $totals = json_decode($sale->total);
                @endphp

                @for ($i = 0; $i < count($categories); $i++)
                    <tr>
                        <td>{{ $categories[$i] }}</td>
                        <td>{{ $names[$i] }}</td>
                        <td>{{ $quantities[$i] }}</td>
                        <td>{{ number_format($prices[$i], 0) }}</td>
                        <td>{{ number_format($totals[$i], 0) }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <p>Subtotal: {{ number_format($sale->total_price, 0) }}</p>
            <p>Discount: {{ number_format($sale->discount, 0) }}</p>
            <p>Grand Total: {{ number_format($sale->total_price - $sale->discount, 0) }}</p>
            <p>Received: {{ number_format($sale->cash_received, 0) }}</p>
            <p>Change to Return: {{ number_format($sale->change_return, 0) }}</p>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <p>Thank you for your business!</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
