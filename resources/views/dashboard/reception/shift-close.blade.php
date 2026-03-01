@extends('dashboard.layouts.app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title text-danger"><i class="fa fa-lock me-2"></i> End Shift & Reconciliation</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Side: Reconciliation Report -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Shift Summary Report</h5>
                        <small class="text-muted">Shift started at: {{ $shift->opened_at->format('M d, Y H:i A') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-4">
                            <div class="col-md">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="text-muted text-uppercase mb-2 small">Expected Cash</h6>
                                    <h4 class="text-success mb-0">{{ number_format($expectedCash) }} TZS</h4>
                                    <small class="text-muted">(Inc. {{ number_format($shift->opening_cash) }} Float)</small>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="text-muted text-uppercase mb-2 small">Mobile Payments</h6>
                                    <h4 class="text-info mb-0">{{ number_format($expectedMobile) }} TZS</h4>
                                    <small class="text-muted small">M-PESA, HALO, TIGO, etc</small>
                                    <div class="mt-2">
                                        <button type="button" onclick="showTransactionModal('mobile', 'Mobile Payments')" class="btn btn-xs btn-outline-info" style="font-size: 10px;">
                                            <i class="fa fa-list"></i> View Transactions
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="text-muted text-uppercase mb-2 small">Bank Payments</h6>
                                    <h4 class="text-warning mb-0">{{ number_format($expectedBank) }} TZS</h4>
                                    <small class="text-muted small">NMB, KCB, CRDB, etc</small>
                                    <div class="mt-2">
                                        <button type="button" onclick="showTransactionModal('bank', 'Bank Payments')" class="btn btn-xs btn-outline-warning" style="font-size: 10px;">
                                            <i class="fa fa-list"></i> View Transactions
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="text-muted text-uppercase mb-2 small">Card Payments</h6>
                                    <h4 class="text-primary mb-0">{{ number_format($expectedCard) }} TZS</h4>
                                    <small class="text-muted small">MasterCard, Visa, etc</small>
                                    <div class="mt-2">
                                        <button type="button" onclick="showTransactionModal('card', 'Card Payments')" class="btn btn-xs btn-outline-primary" style="font-size: 10px;">
                                            <i class="fa fa-list"></i> View Transactions
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="text-muted text-uppercase mb-2 small">Online/OTAs</h6>
                                    <h4 class="text-secondary mb-0">{{ number_format($expectedOnline) }} TZS</h4>
                                    <small class="text-muted small">Booking.com, Expedia</small>
                                    <div class="mt-2">
                                        <button type="button" onclick="showTransactionModal('online', 'Online/OTA Payments')" class="btn btn-xs btn-outline-secondary" style="font-size: 10px;">
                                            <i class="fa fa-list"></i> View Transactions
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Source</th>
                                        <th class="text-end">Cash (TZS)</th>
                                        <th class="text-end">Mobile (TZS)</th>
                                        <th class="text-end">Bank (TZS)</th>
                                        <th class="text-end">Card (TZS)</th>
                                        <th class="text-end">Online (TZS)</th>
                                        <th class="text-end">Total (TZS)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Opening Float / Start Cash</td>
                                        <td class="text-end fw-bold">{{ number_format($shift->opening_cash) }}</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end fw-bold">{{ number_format($shift->opening_cash) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Room Bookings & Extensions</td>
                                        <td class="text-end">{{ number_format($cashBookings) }}</td>
                                        <td class="text-end">{{ number_format($mobileBookings) }}</td>
                                        <td class="text-end">{{ number_format($bankBookings) }}</td>
                                        <td class="text-end">{{ number_format($cardBookings) }}</td>
                                        <td class="text-end">{{ number_format($onlineBookings) }}</td>
                                        <td class="text-end">{{ number_format($cashBookings + $mobileBookings + $bankBookings + $cardBookings + $onlineBookings) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Restaurant & Bar Orders</td>
                                        <td class="text-end">{{ number_format($cashServices) }}</td>
                                        <td class="text-end">{{ number_format($mobileServices) }}</td>
                                        <td class="text-end">{{ number_format($bankServices) }}</td>
                                        <td class="text-end">{{ number_format($cardServices) }}</td>
                                        <td class="text-end">{{ number_format($onlineServices) }}</td>
                                        <td class="text-end">{{ number_format($cashServices + $mobileServices + $bankServices + $cardServices + $onlineServices) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td>GRAND TOTALS</td>
                                        <td class="text-end text-success">{{ number_format($expectedCash) }}</td>
                                        <td class="text-end text-info">{{ number_format($expectedMobile) }}</td>
                                        <td class="text-end text-warning">{{ number_format($expectedBank) }}</td>
                                        <td class="text-end text-primary">{{ number_format($expectedCard) }}</td>
                                        <td class="text-end text-secondary">{{ number_format($expectedOnline) }}</td>
                                        <td class="text-end">{{ number_format($expectedCash + $expectedMobile + $expectedBank + $expectedCard + $expectedOnline) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        {{-- Staff Breakdown --}}
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3"><i class="fa fa-users me-2"></i> Order Collections by Staff</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover border">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Staff Member</th>
                                            <th class="text-end">Cash Collected</th>
                                            <th class="text-end">Non-Cash (M/B/C)</th>
                                            <th class="text-end">Total Collections</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($servicePaymentsByStaff as $sData)
                                            <tr>
                                                <td>{{ $sData['name'] }}</td>
                                                <td class="text-end font-monospace">{{ number_format($sData['cash']) }} TZS</td>
                                                <td class="text-end font-monospace">{{ number_format($sData['non_cash']) }} TZS</td>
                                                <td class="text-end fw-bold font-monospace">{{ number_format($sData['total']) }} TZS</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3 small italic">No service payments recorded by staff in this shift.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Closure Form -->
            <div class="col-lg-6 mx-auto">
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0 text-white"><i class="fa fa-check-square-o"></i> Finalize Closure</h5>
                    </div>
                    <div class="card-body">
                        <form id="shiftCloseForm">
                            @csrf
                            <input type="hidden" name="closing_cash_expected" value="{{ $expectedCash }}">
                            <input type="hidden" name="total_mobile_expected" value="{{ $expectedMobile }}">
                            <input type="hidden" name="total_card_expected" value="{{ $expectedCard }}">
                            <input type="hidden" name="total_bank_expected" value="{{ $expectedBank }}">
                            <input type="hidden" name="total_online_expected" value="{{ $expectedOnline }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold">Actual Cash in Drawer (Physical Count)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="fa fa-money"></i></span>
                                    <input type="number" step="0.01" class="form-control text-center font-weight-bold" name="closing_cash_actual" id="actual_cash" placeholder="Enter amount counted" required>
                                </div>
                                <div id="cash-diff" class="mt-2 fw-bold text-center"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Handover Notes / Discrepancies</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Mention any reasons for missing cash, handover items, etc."></textarea>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle me-2"></i> After closing, you will not be able to record any more payments until you open a new shift.
                            </div>

                            <div class="d-grid shadow">
                                <button type="submit" class="btn btn-danger btn-lg" id="submitBtn">
                                    <i class="fa fa-print me-2"></i> Close Shift & Print Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="transactionModalTitle">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="transactionModalBody">
                <div class="p-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Fetching transaction data...</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('actual_cash').addEventListener('input', function() {
        const expected = {{ $expectedCash }};
        const actual = parseFloat(this.value) || 0;
        const diff = actual - expected;
        const diffEl = document.getElementById('cash-diff');
        
        if (diff === 0) {
            diffEl.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> Perfectly Balanced</span>';
        } else if (diff > 0) {
            diffEl.innerHTML = '<span class="text-primary"><i class="fa fa-plus-circle"></i> Surplus: +' + diff.toLocaleString() + ' TZS</span>';
        } else {
            diffEl.innerHTML = '<span class="text-danger"><i class="fa fa-minus-circle"></i> Shortage: ' + diff.toLocaleString() + ' TZS</span>';
        }
    });

    document.getElementById('shiftCloseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        confirmAction(
            'Are you sure you want to finalize your reports? This will not log you out.', 
            'Close Shift?', 
            'Yes, Close Shift', 
            'No, Keep Open'
        ).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(this);
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Processing...';

                fetch("{{ route('reception.shift.finalize') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Open print report in new window if desired
                        if (data.print_url) {
                            window.open(data.print_url, '_blank');
                        }
                        
                        showSuccessMessage(
                            'Your shift has been finalized and the report has been opened for printing.', 
                            'Shift Closed Successfully!'
                        );
                        
                        // Redirect after a short delay to allow user to see success
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    } else {
                        showErrorMessage(data.message || 'Something went wrong', 'Closure Failed');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fa fa-print me-2"></i> Close Shift & Print Report';
                    }
                })
                .catch(error => {
                    console.error(error);
                    showErrorMessage('An unexpected error occurred while finalising the shift.', 'System Error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-print me-2"></i> Close Shift & Print Report';
                });
            }
        });
    });
    function showTransactionModal(platform, title) {
        $('#transactionModalTitle').text(title + ' - Today');
        $('#transactionModalBody').html(`
            <div class="p-5 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Fetching transaction data...</p>
            </div>
        `);
        $('#transactionModal').modal('show');

        // Use custom range from shift start to now to ensure all transactions are captured
        const startDate = "{{ $shift->opened_at->format('Y-m-d') }}";
        const endDate = "{{ date('Y-m-d') }}";
        const url = "{{ route('admin.reports.payment-platform-report') }}?platform=" + platform + "&report_type=custom&start_date=" + startDate + "&end_date=" + endDate;
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            $('#transactionModalBody').html('<div class="p-3">' + html + '</div>');
        })
        .catch(error => {
            console.error(error);
            $('#transactionModalBody').html('<div class="p-5 text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x mb-2"></i><p>Failed to load transaction data.</p></div>');
        });
    }
</script>
@endsection
