@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible show fade" id="success-alert">
        <div class="alert-body">
            <button class="close" data-dismiss="alert">
                <span>×</span>
            </button>
            <p>{{ $message }}</p>
        </div>
    </div>

    <script>
        // Tunggu 5 detik (5000 milidetik) sebelum menutup alert
        setTimeout(function() {
            var alertElement = document.getElementById('success-alert');
            if (alertElement) {
                alertElement.classList.remove('show');
                alertElement.classList.add('fade');
                setTimeout(function() {
                    alertElement.remove();
                }, 150); // Waktu transisi fade (150ms)
            }
        }, 3000);
    </script>
@endif
