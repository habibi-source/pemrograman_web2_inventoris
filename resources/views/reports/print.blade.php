<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LogisticsPro - Report Print</title>
    <!-- Use Bootstrap CSS from CDN for reliable printing -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        body { padding: 40px; background-color: #fff; }
        .header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print mb-4 text-center">
        <button class="btn btn-primary" onclick="window.print()">Print Document</button>
        <button class="btn btn-secondary ms-2" onclick="window.close()">Close Tab</button>
    </div>

    <div class="header text-center">
        <h2 class="fw-bold mb-1">LogisticsPro Inventory System</h2>
        <h4 class="mb-2">Historical Movement Report</h4>
        <p class="text-muted mb-0">Generated on: {{ now()->format('F j, Y, g:i a') }} by {{ Auth::user()->name }}</p>
    </div>

    <table class="table table-bordered table-striped table-sm">
        <thead class="table-dark">
            <tr>
                <th>Date & Time</th>
                <th>SKU</th>
                <th>Item Name</th>
                <th>Type</th>
                <th>Location</th>
                <th class="text-end">Qty</th>
                <th>Status</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr>
                <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $trx->item->item_code ?? 'N/A' }}</td>
                <td>{{ $trx->item->name ?? 'N/A' }}</td>
                <td>{{ strtoupper($trx->type) }}</td>
                <td>{{ $trx->location ?? '-' }}</td>
                <td class="text-end">{{ $trx->type == 'incoming' ? '+' : '-' }}{{ number_format($trx->quantity) }}</td>
                <td>{{ ucfirst($trx->status) }}</td>
                <td>{{ $trx->reference ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">No transactions match the selected filters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-5 pt-5 text-end">
        <p>___________________________</p>
        <p class="text-muted">Authorized Signature</p>
    </div>

</body>
</html>
