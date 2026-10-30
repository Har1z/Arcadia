@if (session('success'))
    <div class="mb-4 flex items-start gap-2 rounded-ui border border-success/25 bg-success/10 px-4 py-3 text-sm text-success" role="status">
        {{ session('success') }}
    </div>
@elseif (session('error'))
    <div class="mb-4 flex items-start gap-2 rounded-ui border border-danger/25 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">
        {{ session('error') }}
    </div>
@elseif (session('status'))
    <div class="mb-4 flex items-start gap-2 rounded-ui border border-info/25 bg-info/10 px-4 py-3 text-sm text-info" role="status">
        {{ session('status') }}
    </div>
@endif
