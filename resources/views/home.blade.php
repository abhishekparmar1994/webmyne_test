@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        
                        <div>
                            @if(Auth::user()->role == "admin")
                                {{ __('You are logged in as Admin!') }}<br>
                                Go to: <a href="/customers">Customers List</a><br>
                                Go to: <a href="/admin-products-list">Products List</a>
                            @elseif (Auth::user()->role == "user")
                            {{ __('You are logged in as Customer / User!') }}<br>
                                Go to: <a href="#">Product</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
