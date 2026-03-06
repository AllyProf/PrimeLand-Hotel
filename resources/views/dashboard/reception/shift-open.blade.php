@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-key"></i> Open Shift</h1>
    <p>Begin your operational session</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($role === 'manager' ? 'admin.dashboard' : 'reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Open Shift</li>
  </ul>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-md-10 col-lg-8">
        <div class="tile p-0 shadow-lg border-0 overflow-hidden" style="border-radius: 15px;">
            <div class="row no-gutters">
                <!-- Left Side: Branding & Info -->
                <div class="col-md-5 d-flex flex-column justify-content-center p-5 text-center text-white" style="background: linear-gradient(135deg, #009688 0%, #00695c 100%); min-height: 400px;">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
                        <i class="fa fa-unlock-alt fa-3x" style="color: #009688;"></i>
                    </div>
                    <h2 class="font-weight-bold mb-3">Shift Activation</h2>
                    <p class="mb-4 opacity-75">Securely bridge your account to the front-desk operations.</p>
                    
                    <div class="mt-auto pt-4 border-top border-light" style="border-top-color: rgba(255,255,255,0.1) !important;">
                         <span class="small font-weight-bold uppercase letter-spacing-1">
                            <i class="fa fa-clock-o mr-1"></i> {{ now()->format('Y-m-d H:i') }}
                         </span>
                    </div>
                </div>

                <!-- Right Side: Action Form -->
                <div class="col-md-7 p-5 bg-white d-flex flex-column justify-content-center">
                    <div class="mb-4">
                        <h4 class="text-dark font-weight-bold">Drawer Initialization</h4>
                        <p class="text-muted">Welcome, <strong>{{ $userName }}</strong>. Please record your starting balance to proceed.</p>
                    </div>

                    <form action="{{ route('reception.shift.start') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="opening_cash" class="font-weight-bold text-muted small text-uppercase">Opening Cash Balance (Float)</label>
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0" style="border-radius: 10px 0 0 10px;">
                                        <strong class="text-dark">TZS</strong>
                                    </span>
                                </div>
                                <input type="number" step="0.01" class="form-control border-left-0" 
                                       id="opening_cash" name="opening_cash" value="0" 
                                       required autofocus style="border-radius: 0 10px 10px 0; height: 60px; font-weight: 700; font-size: 1.4rem;">
                            </div>
                        </div>

                        <div class="alert alert-secondary border-0 small py-2 px-3 mb-4" style="border-radius: 8px; background: #f8f9fa;">
                            <i class="fa fa-info-circle text-info mr-2"></i> Input the physical cash in your drawer. <strong>If previous shift cash was already submitted, enter 0.</strong>
                        </div>

                        <div class="row">
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm py-3" style="border-radius: 10px; font-weight: 700; font-size: 1.1rem; letter-spacing: 0.5px;">
                                    <i class="fa fa-play-circle mr-2"></i> ACTIVATE SHIFT
                                </button>
                            </div>
                            <div class="col-sm-4 mt-3 mt-sm-0">
                                <a href="{{ route($role === 'manager' ? 'admin.dashboard' : 'reception.dashboard') }}" class="btn btn-light btn-lg btn-block py-3" style="border-radius: 10px; font-weight: 600; font-size: 1rem; color: #666;">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
    .opacity-75 { opacity: 0.75; }
    .tile { transition: transform 0.3s ease-out, box-shadow 0.3s ease; }
    .tile:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important; }
    .no-gutters { margin-right: 0; margin-left: 0; }
    .no-gutters > [class*='col-'] { padding-right: 0; padding-left: 0; }
</style>
@endsection
