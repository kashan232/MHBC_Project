<!-- meta tags and other links -->
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
                    <h6 class="page-title">All Products</h6>
                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
                       
                        <a href="{{ route('add-product') }}"
                            class="btn btn-outline--primary">
                            <i class="la la-plus"></i>Add New </a>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card b-radius--10">
                            <div class="card-body p-0">
                                 @if (session()->has('success'))
                                <div class="alert alert-success">
                                    <strong>Success!</strong> {{ session('success') }}.
                                </div>
                                @endif
                                <div class="table-responsive--md table-responsive">
                                    <table id="example" class="display  table table--light style--two bg--white" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Name </th>
                                                <th>Category | Brand</th>
                                                <th>Stock </th>
                                                <th>Alert Qty</th>
                                                <th>Unit</th>
                                                <th>Wholesale Price</th>
                                                <th>Retail Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($all_product as $product)
                                            <tr>
                                                <td>
                                                    <img
                                                        src="{{ asset('product_images/' . $product->image) }}"
                                                        alt="Product Image" style="max-width: 100px;">
                                                </td>
                                                <td class="long-text">
                                                    <span class="fw-bold text--primary">{{ $product->product_name }}</span>
                                                </td>
                                                <td>
                                                    {{ $product->category }}
                                                    <br>
                                                    <span class="text--primary">{{ $product->brand }}</span>
                                                </td>
                                                <td>
                                                    {{ $product->stock }}
                                                </td>
                                                <td>
                                                    <strong class="badge badge--danger">{{ $product->alert_quantity }}</strong>
                                                </td>
                                                <td>{{ $product->unit }}</td>
                                                <td>{{ $product->wholesale_price }}</td>
                                                <td>{{ $product->retail_price }}</td>
                                                <td>
                                                    <div class="button--group">
                                                        <a href="{{ route('edit-product',['id' => $product->id ]) }}"
                                                            class="btn btn-sm btn-outline--primary ms-1 editBtn"><i
                                                                class="las la-pen"></i> Edit</a>
                                                    </div>
                                                </td>
                                                @endforeach
                                        </tbody>
                                    </table>
                                    <!-- table end -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin_panel.include.footer_include')
