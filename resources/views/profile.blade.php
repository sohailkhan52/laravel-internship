@extends('layouts.app')
@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
             <div id="alert-container"></div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <!-- error Message -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
        <div class="col-lg-8">

            <!-- User Profile Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="row">
                        <div class="col-md-6"> <h4 class="mb-0">User Profile</h4></div>
                        <div class="col-md-6"> <h4 class="mb-0">Change Password</h4></div>
                    </div>
                   
                </div>
                <div class="card-body">
                    <div class="row">

                        <!-- Left: User Info -->
                        <div class="col-md-6 mb-4 mb-md-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Name:</strong>
                                    <span>{{$user->name}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Email:</strong>
                                    <span>{{$user->email}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Points:</strong>
                                    <span>{{$user->points}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Role:</strong>
                                    <span>{{$user->role}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Joined:</strong>
                                    <span>{{$user->created_at->diffForHumans()}}</span>
                                </li>
                            </ul>
                        </div>


                        <div class="col-md-6 mt-3">
                        <!-- Right: Password Update Form -->
                    <form method="POST" action="/change_password">
                        @csrf
                        <div class="row mb-3">                         
                            <label  class="col-md-4 col-form-label text-md-end">Old Password</label>
                            <div class="col-md-6">
                                <input type=password class="form-control is-invalid" name="old_password"  >
                                @error('old_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror  
                            </div>
                        </div>
                        <div class="row mb-3">                      
                            <label  class="col-md-4 col-form-label text-md-end">New Password</label>
                            <div class="col-md-6">
                                <input type=password class="form-control is-invalid" name="new_password"  >  
                                @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror    
                            </div>
                        </div>
                        <div class="row mb-3"> 
                            <label  class="col-md-4 col-form-label text-md-end">Confirm Password</label>
                            <div class="col-md-6">
                                <input type=password class="form-control is-invalid" name="new_password_confirmation">
                                    @error('new_password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror                           
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">                            
                            <button  type=Submit class=" class from-control btn btn-outline-primary">Submit</button>
                        </div>
                        </div>

                            </form>
                </div> 
            </div> 

        </div> 
    </div> 
</div> 

@endsection
