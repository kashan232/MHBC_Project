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
                    <h6 class="page-title">Add Product</h6>
                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
                        <a href="{{ route('all-product') }}"
                            class="btn btn-sm btn-outline--primary">
                            <i class="la la-undo"></i> Back</a>
                    </div>
                </div>
                <div class="row mb-none-30">
                    <div class="col-lg-12 col-md-12 mb-30">
                        <div class="card">
                            <div class="card-body">
                                @if (session()->has('success'))
                                <div class="alert alert-success">
                                    <strong>Success!</strong> {{ session('success') }}.
                                </div>
                                @endif

                                <form action="{{ route('store-product') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-group">
                                                <div class="image-upload">
                                                    <div class="thumb">
                                                        <div class="avatar-preview">
                                                            <div class="profilePicPreview"
                                                                style="background-image: url(https://script.viserlab.com/torylab/placeholder-image/400x400)">
                                                                <button type="button" class="remove-image"><i
                                                                        class="fa fa-times"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="avatar-edit">
                                                            <input type="file" class="profilePicUpload"
                                                                name="image" id="profilePicUpload1"
                                                                accept=".png, .jpg, .jpeg">
                                                            <label for="profilePicUpload1" class="bg--success">Upload
                                                                Image
                                                            </label>
                                                            <small class="mt-2">Supported files:
                                                                <b>jpeg, jpg.</b> Image will be resized into 400x400px
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group ">
                                                        <label>Name</label>
                                                        <input type="text" name="product_name" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="form-group" id="category-wrapper">
                                                            <label class="form-label">Category</label>
                                                            <select name="category" class="select2-basic form-control" required>
                                                                <option value="" selected disabled>Select One</option>
                                                                @foreach($all_category as $category)
                                                                <option value="{{ $category->category }}">
                                                                    {{ $category->category }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class=" col-sm-6">
                                                    <div class="form-group">
                                                        <div class="form-group" id="brand-wrapper">
                                                            <label class="form-label">Brand</label>
                                                            <select name="brand" class="select2-basic form-control" required>
                                                                <option value="" selected disabled>Select One</option>
                                                                @foreach($all_brand as $brand)
                                                                <option value="{{ $brand->brand }}">
                                                                    {{ $brand->brand }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Unit(UoM)</label>
                                                        <select name="unit" class="select2-basic form-control" required>
                                                            <option value="" selected disabled>Select One</option>
                                                            @foreach($all_unit as $unit)
                                                            <option value="{{ $unit->unit }}">
                                                                {{ $unit->unit }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group ">
                                                        <label class="form-label">Initial stock</label>
                                                        <input type="text" class="form-control " name="stock" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Alert Quantity</label>
                                                        <input type="number" name="alert_quantity"
                                                            class="form-control" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group ">
                                                        <label class="form-label">Wholesale Price</label>
                                                        <input type="number" class="form-control " name="wholesale_price" value="Null">
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group ">
                                                        <label class="form-label">Retail Price</label>
                                                        <input type="number" class="form-control " name="retail_price" value="Null">
                                                    </div>
                                                </div>

                                                <!-- Barcode Number -->




                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--primary w-100 h-45">Submit</button>
                                    </div>
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
        // Barcode input field ko target kar rahe hain
        const barcodeInput = document.getElementById('barcodeInput');

        // Event listener jab barcode scanner se koi value aaye
        barcodeInput.addEventListener('input', function(event) {
            const barcodeValue = event.target.value;

            // Jab barcode ki length sufficient ho (tumhare barcode ki length pe depend karega)
            if (barcodeValue.length >= 6) { // 6 ko tum adjust kar sakte ho barcode length ke hisaab se
                console.log("Barcode scanned: " + barcodeValue);

                // Tum yahan koi additional action bhi kar sakte ho
                // Jaise form submit ya barcode ko validate karna
            }
        });
    </script>