@include('admin_panel.include.header_include')

<body>
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
                    <h6 class="page-title">Dashboard</h6>
                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
                    </div>
                </div>


                <div class="row gy-4 mb-30">

                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two box--shadow2 b-radius--5 bg--white">
                            <i class="las la-shopping-bag overlay-icon text--success"></i>
                            <div class="widget-two__icon b-radius--5   bg--info  ">
                                <i class="las la-shopping-bag"></i>
                            </div>
                            <div class="widget-two__content">
                                <h3>{{ number_format($totalStockValue, 2) }}</h3>
                                <p>Total Stock Value</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two box--shadow2 b-radius--5 bg--white">
                            <i class="las la-shopping-bag overlay-icon text--success"></i>
                            <div class="widget-two__icon b-radius--5   bg--success  ">
                                <i class="las la-shopping-bag"></i>
                            </div>
                            <div class="widget-two__content">
                                <h3>{{ $totalPurchasesPrice }}</h3>
                                <p>Purchases</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two box--shadow2 b-radius--5 bg--white">
                            <i class="las la-share overlay-icon text--danger"></i>

                            <div class="widget-two__icon b-radius--5   bg--danger  ">
                                <i class="las la-share"></i>
                            </div>
                            <div class="widget-two__content">
                                <h3>{{ $totalPurchaseReturnsPrice }}</h3>
                                <p>Purchases Return</p>
                            </div>
                           
                        </div>
                    </div>

                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two box--shadow2 b-radius--5 bg--white">
                            <i class="las la-shopping-cart overlay-icon text--primary"></i>
                            <div class="widget-two__icon b-radius--5   bg--primary  ">
                                <i class="las la-shopping-cart"></i>
                            </div>
                            <div class="widget-two__content">
                                <h3>{{$totalsales }}</h3>
                                <p>Sales</p>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="row gy-4 mb-30">
                    <div class="col-xl-6">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <h5>Product Alert Items </h5>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive--sm table-responsive">
                                    <table class="table table--light">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Alert</th>
                                                <th>Stock</th>
                                                <th>Unit</th>
                                                <th>W.Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($all_product as $product)
                                            <tr>
                                                <td class="fw-bold"> {{ $product->product_name }} </td>
                                                <td>
                                                    <span class="bg--danger px-2 rounded">
                                                        {{ $product->alert_quantity }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="bg--warning px-2 rounded">
                                                        {{ $product->stock }}
                                                    </span>
                                                </td>
                                                <td>{{ $product->unit }}</td>
                                                <td>{{ $product->wholesale_price }}</td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table><!-- table end -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <h5>Total Stock Value </h5>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive--sm table-responsive">
                                    <table class="table table--light">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Stock </th>
                                                <th>W.Price</th>
                                                <th>Stock Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($all_product as $product)
                                            <tr>
                                                <td>{{ $product->product_name }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>{{ $product->wholesale_price }}</td>
                                                <td>{{ $product->total_stock_value }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table><!-- table end -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row gy-4">
                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--1">
                            <div class="widget-two__icon b-radius--5 bg--1">
                                <i class="lab la-buffer"></i>
                            </div>

                            <div class="widget-two__content">
                                <h3 class="text-white">{{ $categories }}</h3>
                                <p class="text-white">Categories</p>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
                            <div class="widget-two__icon b-radius--5 bg--primary">
                                <i class="lab la-product-hunt"></i>
                            </div>

                            <div class="widget-two__content">
                                <h3 class="text-white">{{ $products }}</h3>
                                <p class="text-white">Products</p>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->

                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--18">
                            <div class="widget-two__icon b-radius--5 bg--18">
                                <i class="las la-user-friends"></i>
                            </div>

                            <div class="widget-two__content">
                                <h3 class="text-white">{{ $suppliers }}</h3>
                                <p class="text-white">Suppliers</p>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->

                    <div class="col-xxl-3 col-sm-6">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                            <div class="widget-two__icon b-radius--5 bg--19">
                                <i class="las la-users"></i>
                            </div>

                            <div class="widget-two__content">
                                <h3 class="text-white">{{ $customers }}</h3>
                                <p class="text-white">Customers</p>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->
                </div><!-- row end-->



            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>

    @include('admin_panel.include.footer_include')