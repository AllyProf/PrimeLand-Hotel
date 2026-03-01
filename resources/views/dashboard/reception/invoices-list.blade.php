@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text-o"></i> Sent Quotations</h1>
    <p>View and manage all sent proforma invoices</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Sent Quotations</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-paper-plane fa-2x"></i>
      <div class="info">
        <h4>Total Sent</h4>
        <p><b>{{ $invoices->total() }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-clock-o fa-2x"></i>
      <div class="info">
        <h4>Pending</h4>
        <p><b>{{ $invoices->total() }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Conversions</h4>
        <p><b>0</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-usd fa-2x"></i>
      <div class="info">
        <h4>Estimates</h4>
        <p><b>${{ number_format($invoices->sum('total_price'), 0) }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-4 d-flex justify-content-between align-items-center">
        <h3 class="title">Filter Quotations</h3>
        <a href="{{ route('reception.invoices.create') }}" class="btn btn-primary px-4 shadow-sm">
          <i class="fa fa-plus-circle mr-1"></i> Create Quick Invoice
        </a>
      </div>

      <!-- Navigation Tabs -->
      <ul class="nav nav-tabs mb-4" id="invoiceTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="individual-tab" data-toggle="tab" href="#individual" role="tab" aria-controls="individual" aria-selected="true" onclick="filterByTab('individual')">
            <i class="fa fa-user mr-1"></i> Individual Invoices ({{ $totalIndividual }})
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="corporate-tab" data-toggle="tab" href="#corporate" role="tab" aria-controls="corporate" aria-selected="false" onclick="filterByTab('corporate')">
            <i class="fa fa-building-o mr-1"></i> Company Invoices ({{ $totalCorporate }})
          </a>
        </li>
      </ul>
      
      <!-- Search Filter -->
      <div class="row mb-3 px-2">
        <div class="col-md-9">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-light"><i class="fa fa-search"></i></span>
                </div>
                <input type="text" class="form-control" id="searchInput" placeholder="Search by reference, guest name, or company..." onkeyup="filterInvoices()">
            </div>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary btn-block" onclick="resetFilters()">
                <i class="fa fa-refresh"></i> Reset All
            </button>
        </div>
      </div>
      
      <div class="tile-body">
        @if($invoices->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="invoicesTable">
            <thead class="bg-light">
              <tr>
                <th width="50">#</th>
                <th>Reference</th>
                <th>Client Details</th>
                <th>Room Type & Stay</th>
                <th>Estimated Total</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoices as $index => $invoice)
              <tr class="invoice-row {{ $invoice->is_corporate_booking ? 'type-corporate' : 'type-individual' }}" 
                  data-ref="{{ strtolower($invoice->booking_reference) }}"
                  data-name="{{ strtolower($invoice->guest_name) }}"
                  data-company="{{ strtolower($invoice->company->name ?? '') }}"
                  data-email="{{ strtolower($invoice->guest_email) }}">
                <td class="text-center align-middle">{{ $invoices->firstItem() + $index }}</td>
                <td class="align-middle">
                  <strong>{{ $invoice->booking_reference }}</strong><br>
                  <small class="text-muted"><i class="fa fa-calendar-o mr-1"></i> {{ $invoice->created_at->format('M d, Y') }}</small>
                </td>
                <td class="align-middle">
                  @if($invoice->is_corporate_booking)
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="fa fa-building"></i>
                        </div>
                        <div>
                            <strong>{{ $invoice->company->name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">Rep: {{ $invoice->guest_name }}</small>
                        </div>
                    </div>
                  @else
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary text-white rounded-circle p-2 mr-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="fa fa-user"></i>
                        </div>
                        <div>
                            <strong>{{ $invoice->guest_name }}</strong><br>
                            <small class="text-muted">{{ $invoice->guest_email }}</small>
                        </div>
                    </div>
                  @endif
                </td>
                <td class="align-middle text-nowrap">
                  <span class="badge badge-primary px-2 mb-1">{{ $invoice->room->room_type ?? 'N/A' }}</span><br>
                  <small><strong>{{ \Carbon\Carbon::parse($invoice->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($invoice->check_out)->format('M d, Y') }}</strong></small><br>
                  <small class="text-muted"><i class="fa fa-moon-o mr-1"></i> {{ \Carbon\Carbon::parse($invoice->check_in)->diffInDays(\Carbon\Carbon::parse($invoice->check_out)) }} night(s)</small>
                </td>
                <td class="align-middle">
                  <div class="text-primary font-weight-bold h5 mb-0">${{ number_format($invoice->total_price, 2) }}</div>
                  <small class="text-muted">
                    Approx. {{ number_format($invoice->total_price * ($invoice->locked_exchange_rate ?? $exchangeRate), 0) }} TZS
                  </small>
                </td>
                <td class="text-center align-middle">
                    <a href="{{ route('reception.invoices.download', $invoice->id) }}" class="btn btn-outline-danger shadow-sm px-3" title="Download Premium PDF Invoice" style="border-radius: 20px;">
                      <i class="fa fa-file-pdf-o mr-1"></i> PDF
                    </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3 p-2 bg-light rounded">
            <div class="small text-muted">Showing <b>{{ $invoices->firstItem() }}</b> to <b>{{ $invoices->lastItem() }}</b> of <b>{{ $invoices->total() }}</b></div>
            <div>
                {{ $invoices->links() }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
          <i class="fa fa-file-text-o fa-4x text-muted mb-3"></i>
          <h4 class="text-muted">No quotations found</h4>
          <p>Start by creating a proforma invoice for your clients.</p>
          <a href="{{ route('reception.invoices.create') }}" class="btn btn-primary mt-2">Create New Invoice</a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
.nav-tabs .nav-link.active {
    font-weight: 700;
    color: #e07632;
    border-bottom: 3px solid #e07632;
    background: #fff;
}
.nav-tabs .nav-link {
    color: #64748b;
    border: none;
    padding: 12px 20px;
}
.table thead th {
    border-top: none;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
}
#invoicesTable tr:hover {
    background-color: #f8fafc;
}
</style>
@endsection

@section('scripts')
<script>
let currentTab = 'individual';

$(document).ready(function() {
    // Initial filter
    filterByTab('individual');
});

function filterByTab(tab) {
    currentTab = tab;
    let rows = document.getElementsByClassName('invoice-row');
    let targetClass = (tab === 'corporate') ? 'type-corporate' : 'type-individual';
    
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].classList.contains(targetClass)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
    
    // Clear search when switching tabs
    document.getElementById('searchInput').value = '';
}

function filterInvoices() {
    let input = document.getElementById('searchInput');
    let filter = input.value.toLowerCase();
    let rows = document.getElementsByClassName('invoice-row');
    let targetClass = (currentTab === 'corporate') ? 'type-corporate' : 'type-individual';

    for (let i = 0; i < rows.length; i++) {
        // Only search within the active tab's rows
        if (rows[i].classList.contains(targetClass)) {
            let ref = rows[i].getAttribute('data-ref');
            let name = rows[i].getAttribute('data-name');
            let company = rows[i].getAttribute('data-company');
            let email = rows[i].getAttribute('data-email');
            
            if (ref.includes(filter) || name.includes(filter) || company.includes(filter) || email.includes(filter)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    filterByTab(currentTab);
}
</script>
@endsection
