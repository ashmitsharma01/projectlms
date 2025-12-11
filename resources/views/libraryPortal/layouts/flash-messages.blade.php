<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var notyf = new Notyf({
            duration: 3000,
            position: {
                x: 'right',
                y: 'top'
            },
            ripple: true,
        });

        @if (session('success'))
            notyf.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            notyf.error("{{ session('error') }}");
        @endif

        @if (session('info'))
            notyf.open({
                type: 'info',
                message: "{{ session('info') }}"
            });
        @endif

        @if ($errors->any())
            notyf.error("Please fix the form errors.");
        @endif

        @if (session('exception'))
            notyf.error("{{ session('exception') }}");
        @endif
    });
</script>

{{-- For detailed duplicate list (toast is not enough) --}}
@if (session('errorMessages'))
    <div class="mt-2 p-3 bg-light border rounded">
        <strong>Skipped Duplicates:</strong>
        <ul>
            @foreach (session('errorMessages') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
