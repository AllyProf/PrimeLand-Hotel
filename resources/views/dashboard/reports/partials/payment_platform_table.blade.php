<div class="row mb-4">
    <div class="col-md-6">
        <div class="p-3 bg-light border rounded text-center">
            <h6 class="text-muted text-uppercase mb-2">Total Earnings (TZS)</h6>
            <h4 class="text-success mb-0 font-weight-bold">{{ number_format($totalTZS, 0) }} TZS</h4>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light border rounded text-center">
            <h6 class="text-muted text-uppercase mb-2">Total Earnings (USD)</h6>
            <h4 class="text-info mb-0 font-weight-bold">${{ number_format($totalUSD, 2) }}</h4>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-bordered table-sm" id="modalTransactionsTable">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Description</th>
                <th>Method</th>
                <th class="text-end">Amount (TZS)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ \Carbon\Carbon::parse($t['date'])->format('M d, H:i') }}</td>
                <td><span class="badge badge-info">{{ $t['source'] }}</span></td>
                <td>{{ $t['description'] }}</td>
                <td><code class="text-primary">{{ strtoupper($t['method']) }}</code></td>
                <td class="text-end fw-bold">{{ number_format($t['amount_tsh'], 0) }} TZS</td>
                <td>
                    @if($t['link'] !== '#')
                        <a href="{{ $t['link'] }}" class="btn btn-xs btn-outline-primary" target="_blank text-decoration-none">
                            <i class="fa fa-eye"></i>
                        </a>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <p class="text-muted mb-0">No transactions found for {{ $platform }}.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
        @if(count($transactions) > 0)
        <tfoot class="table-light">
            <tr class="fw-bold">
                <td colspan="4" class="text-end">TOTAL</td>
                <td class="text-end text-success">{{ number_format($totalTZS, 0) }} TZS</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
