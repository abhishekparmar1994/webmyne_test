@extends('layouts.app')
@section('page-style')
	<style>
		/*Checkbox Design CSS*/
		fieldset {
			display: block;
			margin-left: 0;
			margin-right: 0;
			padding-top: 0;
			padding-bottom: 0;
			padding-left: 0;
			padding-right: 0;
			border: none;
		}
		label {display: block; padding: 0 0 5px 0;}
		.checkboxes label {display: block; float: left;}
		input[type="radio"] + span {
			display: block;
			border: 1px solid black;
			border-left: 1;
			padding: 5px 10px;
		}
		label:first-child input[type="radio"] + span {border-left: 1px solid black;}
		input[type="radio"]:checked + span {background: silver;}
		/*Checkbox Design CSS*/
	</style>
@stop
@section('content')
    <div class="container mt-2">

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Product List</h2>
                </div>
                <!-- <div class="pull-right mb-2">
                    <a class="btn btn-success" onClick="add()" href="javascript:void(0)">Create Customer</a>
                </div> -->
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="card-body">
            <div class='mb-2'>
                <input type="text" id="searchNameEmail" placeholder="Search Name & Email"> &nbsp;
                <select id='sel_currency'>
                    <option value=''>-- Select Currency --</option>
                    <option value=''>All</option>
                    <option value='INR'>INR</option>
                    <option value='USD'>USD</option>
                    <option value='EURO'>EURO</option>
                </select>&nbsp;
                <a class="btn btn-success" onClick="add()" href="javascript:void(0)">Create Product</a>
            </div>
            <table class="table table-bordered" id="products">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Currency</th>
                        <th>Price</th>
                        <th>InStock</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- boostrap customer model -->
    <div class="modal fade" id="customer-modal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="CustomerModal"></h4>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="CustomerForm" name="CustomerForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Title</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Product Title" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Image</label>
                            <div class="col-sm-12">
                                <input type="file" class="form-control" id="image" name="image"
                                    placeholder="Image" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Enter description" maxlength="12" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Price" class="col-sm-2 control-label">Price</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="price" name="price"
                                    placeholder="Enter price" maxlength="12" required="">
                            </div>
                        </div>
                        @php
                            $category = App\Models\Category::get();
                        @endphp
                        <div class="form-group">
                            <label for="category_id" class="col-sm-2 control-label">Category</label>
                                <select name="category_id" id="sel_category" class="form-control category">
                                    <option value=''>-- Select Category --</option>
                                        @foreach($category as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                        @endforeach
                                </select>
                        </div>
                         <div class="form-group">
                            <div id='currencyinput'>
                                    <label>Currency (Select Any one)</label><br>
                                        <input type="radio" class="hidden" id="INR" name="currency" value="INR" required>INR
                                        <input type="radio" class="hidden" id="USD" name="currency" value="USD">USD
                                        <input type="radio" class="hidden" id="EURO" name="currency" value="EURO">EURO
	                        </div>
                        </div>
                        <div class="form-group">
                            <div id='inStockInput'>
                                <label>In Stock (Select Any one)</label><br>
                                <input type="radio" class="hidden" id="instock" name="in_stock" value="1" required>In Stock
                                <input type="radio" class="hidden" id="outofstock" name="in_stock" value="2">Out Of Stock
	                        </div>
                        </div>
                        <div class="form-group">
                            <div id='statusInput'>
                                    <label>Status (Select Any one)</label><br>
                                    <input type="radio" class="hidden" id="status_act" name="status" value="1" required>Active
                                    <input type="radio" class="hidden" id="status_in_act" name="status" value="2">Inactive
                            </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="btn-save">Save changes
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <!-- end bootstrap model -->

    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#products').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "{{ route('products.index') }}",
                    data: function(data){
                        data.searchGender = $('#sel_currency').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false,
                        defaultContent:"-"
                    },
                    {
                        data: 'title',
                        name: 'title',
                        defaultContent:"-"
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: function(data, type, row ){
                            if(data != null){
                                var path = '/uploads/product_image/'+ data;
                            }else{
                                var path = '/uploads/product_image/no-image.jpg';
                            }
                            return '<img src='+path+' border="0" width="100" align="center" />';
                        },
                        defaultContent:"-"
                    },
                    {
                        data: 'description',
                        name: 'description',
                        defaultContent:"-"
                    },
                    {
                        data: 'currency',
                        name: 'currency',
                        render: function(data, type, row ){
                                return data;
                        },
                        defaultContent:"-"
                    },
                    {
                        data: 'price',
                        name: 'price',
                        render: function(data, type, row) {
                            return data;
                            // console.log(row.currency);
                            // if(row.currency == 'INR')
                            //     return Number(data).toLocaleString('en-IN', {
                            //         maximumFractionDigits: 2,
                            //         style: 'currency',
                            //         currency: 'INR'
                            //     });
                            // else if(row.currency == 'USD')
                            //     return Number(data).toLocaleString('en-IN', {
                            //         maximumFractionDigits: 2,
                            //         style: 'currency',
                            //         currency: 'USD'
                            //     });
                            // else if(row.currency == 'EURO')
                            //     return Number(data).toLocaleString('en-IN', {
                            //         maximumFractionDigits: 2,
                            //         style: 'currency',
                            //         currency: 'EURO'
                            //     });
                        },
                        defaultContent:"-"
                    },
                    {
                        data: 'in_stock',
                        name: 'in_stock',
                        render: function(data, type, row) {
                            if(data == '1')
                                return '<span class="label label-primary">In-Stock</span>'
                            else if(data == '2')
                                return '<span class="label label-success">Out-of-Stock</span>'
                            else
                                return '-'
                        },
                        defaultContent:"-"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if(data == 1)
                                return '<span class="label label-primary">Active</span>'
                            else if(data == 2)
                                return '<span class="label label-success">Inactive</span>'
                            else
                                return '-'
                        },
                        defaultContent:"-"
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        defaultContent:"-"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });
        });

        function add() {
            $('#CustomerForm').trigger("reset");
            $('#CustomerModal').html("Add Product");
            $('#customer-modal').modal('show');
            $('#id').val('');
        }

        function editFunc(id) {
            $.ajax({
                type: "POST",
                url: "{{ url('edit-product') }}",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    $('#CustomerModal').html("Edit Product");
                    $('#customer-modal').modal('show');
                    $('#id').val(res.id);
                    $('#title').val(res.title);
                    $('#description').val(res.description);
                    $('#price').val(res.price);
                    if (res.currency === "INR")
                        $('#currencyinput').find(':radio[name=currency][value="INR"]').prop('checked', true);
                    else if(res.currency === "USD")
                        $('#currencyinput').find(':radio[name=currency][value="USD"]').prop('checked', true);
                    else if(res.currency === "EURO")
                        $('#currencyinput').find(':radio[name=currency][value="EURO"]').prop('checked', true);

                    if (res.role == "admin")
                        $('#roleInput').find(':radio[name=role][value="admin"]').prop('checked', true);
                    else if(res.role == "user")
                        $('#roleInput').find(':radio[name=role][value="user"]').prop('checked', true);

                    if (res.status == 1)
                        $('#statusInput').find(':radio[name=status][value=1]').prop('checked', true);
                    else if(res.status == 2)
                        $('#statusInput').find(':radio[name=status][value=2]').prop('checked', true);
                }

            });
        }

        function deleteFunc(id) {
            if (confirm("Delete record?") == true) {
                var id = id;
                // ajax
                $.ajax({
                    type: "POST",
                    url: "{{ url('delete-product') }}",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        var oTable = $('#products').dataTable();
                        oTable.fnDraw(false);
                    }
                });
            }
        }
        $('#CustomerForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('store-product') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    $("#customer-modal").modal('hide');
                    var oTable = $('#products').dataTable();
                    oTable.fnDraw(false);
                    $("#btn-save").html('Submit');
                    $("#btn-save").attr("disabled", false);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
         $('#sel_currency').change(function(){
            var oTable = $('#products').dataTable();
            oTable.fnDraw(true);
        });

        $('#searchNameEmail').keyup(function(){
            var oTable = $('#products').dataTable();
            oTable.fnDraw(true);
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width(300)
                        .height(200);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
