@if (session()->has('error'))
<div class="text-sm text-red-600 mt-2">
    {{ session('error') }}
</div>
@endif