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
                    <h2>Customers / Users</h2>
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
                <a class="btn btn-success" onClick="add()" href="javascript:void(0)">Create Customer</a>
            </div>
            <table class="table table-bordered" id="customers">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Currency</th>
                        <th>Role</th>
                        <!-- <th>Image</th> -->
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
                            <label for="name" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Customer name" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter Email" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="col-sm-2 control-label">Phone</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="Enter Phone" maxlength="12" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Password" class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-12">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter password" maxlength="12" required="">
                            </div>
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
                            <div id='roleInput'>
                                <label>Role (Select Any one)</label><br>
                                <input type="radio" class="hidden" id="admin" name="role" value="admin" required>Admin
                                <input type="radio" class="hidden" id="user" name="role" value="user">User   
	                        </div>
                        </div>
                        <!-- <div class="form-group">
                            <label for="Image" class="col-sm-2 control-label">Image</label>
                            <div class="col-sm-12">
                                <input type='file' class="form-control" id="image" name="image" placeholder="Select Image" required="" onchange="readURL(this);" /><br>
                                <img id="blah" src="#" alt="your image" />
                            </div>
                        </div> -->
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
            $('#customers').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "{{ route('customers.index') }}",
                    data: function(data){
                        data.searchGender = $('#sel_currency').val();
                        data.searchNameEmail = $('#searchNameEmail').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id', 
                        visible: false,
                        defaultContent:"-"
                    },
                    {
                        data: 'name',
                        name: 'name',
                        defaultContent:"-"
                    },
                    {
                        data: 'email',
                        name: 'email',
                        defaultContent:"-"
                    },
                    {
                        data: 'phone',
                        name: 'phone',
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
                        data: 'role',
                        name: 'role',
                        render: function(data, type, row) {
                            if(data == 'admin')
                                return '<span class="label label-primary">Admin</span>'
                            else if(data == 'user')
                                return '<span class="label label-success">User</span>'
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
            $('#CustomerModal').html("Add Customer");
            $('#customer-modal').modal('show');
            $('#id').val('');
        }

        function editFunc(id) {
            $.ajax({
                type: "POST",
                url: "{{ url('edit-customer') }}",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    $('#CustomerModal').html("Edit Customer");
                    $('#customer-modal').modal('show');
                    $('#id').val(res.id);
                    $('#name').val(res.name);
                    $('#email').val(res.email);
                    $('#phone').val(res.phone);
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
                    url: "{{ url('delete-customer') }}",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        var oTable = $('#customers').dataTable();
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
                url: "{{ url('store-customer') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    $("#customer-modal").modal('hide');
                    var oTable = $('#customers').dataTable();
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
            var oTable = $('#customers').dataTable();
            oTable.fnDraw(true);
        });

        $('#searchNameEmail').keyup(function(){
            var oTable = $('#customers').dataTable();
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
