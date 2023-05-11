@extends('layouts.app')
@section('page-style')
	<style>
        body{
            background-color:#dce3f0;
        }

        .height{

            height:100vh;
        }

        .card{

            width:350px;
            height:370px;
        }

        .image{
            position:absolute;
            right:12px;
            top:10px;
        }

        .main-heading{

            font-size:40px;
            color:red !important;
        }

        .ratings i{

            color:orange;

        }

        .user-ratings h6{
            margin-top:2px;
        }

        .colors{
            display:flex;
            margin-top:2px;
        }

        .colors span{
            width:15px;
            height:15px;
            border-radius:50%;
            cursor:pointer;
            display:flex;
            margin-right:6px;
        }

        .colors span:nth-child(1) {

            background-color:red;

        }

        .colors span:nth-child(2) {

            background-color:blue;

        }

        .colors span:nth-child(3) {

            background-color:yellow;

        }

        .colors span:nth-child(4) {

            background-color:purple;

        }

        .btn-danger{

            height:48px;
            font-size:18px;
        }

        /*Checkbox Design CSS*/
	</style>
@stop
@section('content')
    <div class="container mt-2">

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Product List / User Preferred Currency is : {{ Auth::user()->currency }}</h2>
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="card-body">
            <div class='mb-2'>
                <input type="text" id="searchProduct" placeholder="Search Product by Title">
                <select id='sel_currency'>
                    <option value=''>-- Select Currency --</option>
                    <option value=''>Default</option>
                    <option value='INR'>INR</option>
                    <option value='USD'>USD</option>
                    <option value='EURO'>EURO</option>
                </select>&nbsp;
                @php
                    $category = App\Models\Category::get();
                @endphp
                <select name="category" id="sel_category" class="category">
                    <option value=''>-- Select Category --</option>
                    @foreach($category as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                    @endforeach
                </select>

                <div id="product-list"></div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function() {
            loadProduct();
        });
        function loadProduct(){
            $.ajax({
                type: "GET",
                url: "{{ url('user-products-list') }}",
                data: {
                    searchCurrency: $('#sel_currency').val(),
                    searchCategory: $('#sel_category').val(),
                    searchProduct: $('#searchProduct').val(),
                },
                success: function(products) {
                    let productHtml = '';
                    productHtml += "<div class='row mt-4'>";
                    for (let i = 0; i < products.length; i++) {
                        productHtml += "<div class='col-md-4'>";
                        productHtml += '<div class="height d-flex justify-content-center align-items-center">';
                        productHtml += '<div class="card p-3">';
                        productHtml += '<div class="d-flex justify-content-between align-items-center ">';
                        productHtml += '<div class="image">';
                        productHtml += '<img src="/uploads/product_image/'+ products[i].image +'" width="300" height="200">';
                        productHtml += '</div>';
                        productHtml += '</div>';
                        productHtml += '<div class="d-flex justify-content-between align-items-center mt-2 mb-2">';
                        productHtml += '<span><b>Name:</b> '+ products[i].title +'</span>';
                        productHtml += '<div class="colors">';
                        productHtml += '<span></span>';
                        productHtml += '<span></span>';
                        productHtml += '</div>';
                        productHtml += '</div>';
                        productHtml += '<div class="d-flex justify-content-between align-items-center mb-2">';
                        productHtml += '<p><b> Description:</b> '+ products[i].description +'</p>';
                        productHtml += '<div class="colors">';
                        productHtml += '<span></span>';
                        productHtml += '<span></span>';
                        productHtml += '</div>';
                        productHtml += '</div>';
                        productHtml += '<p><b> Price:</b> '+ products[i].price +'</p>';
                        productHtml += '<button class="btn btn-danger">Add to cart</button>';
                        productHtml += '</div>';
                        productHtml += '</div>';
                        productHtml += '</div>';
                    }
                    productHtml += ' </div>';
                    $('#product-list').html(productHtml);
                }
            });
        }
        $('#sel_currency').change(function(){
            loadProduct();
        });
        $('#sel_category').change(function(){
            loadProduct();
        });
        $('#searchProduct').keyup(function(){
            loadProduct();
        });
    </script>
@endsection
