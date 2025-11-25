<!-- Mainly scripts -->

<script src="{{ asset('assets/backend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/backend/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('assets/backend/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>


<!-- Peity -->
<script src="{{ asset('assets/backend/js/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('assets/backend/js/demo/peity-demo.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('assets/backend/js/inspinia.js') }}"></script>
{{-- <script src="{{ asset('assets/backend/js/plugins/pace/pace.min.js') }}"></script> --}}

<!-- jQuery UI -->
<script src="{{ asset('assets/backend/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Jvectormap -->
<script src="{{ asset('assets/backend/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('assets/backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

<!-- Sparkline -->
<script src="{{ asset('assets/backend/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Sparkline demo data  -->
<script src="{{ asset('assets/backend/js/demo/sparkline-demo.js') }}"></script>

<!-- ChartJS-->
<script src="{{ asset('assets/backend/js/plugins/chartJs/Chart.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/backend/js/plugins/dataTables/datatables.min.js') }}"></script>

<!-- library tự custom -->
<script src="{{ asset('assets/backend/library/library.js') }}" ></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}


<!-- Page-Level Scripts -->


<!-- Sweet alert -->
<script>
    @if(Session::has('msg-success'))
    swal("Thành công", "{{ Session::get('msg-success') }}", 'success', {
        button:true,
        button: {
            text: "Xác nhận",
            value: true,
            visible: true,
            className: "btn-lg btn-primary" // Thay đổi màu sắc tại đây
        },
        timer: 5000,
        darkMode: true,

    });
    @endif

    @if(Session::has('msg-error'))
    swal("Thất bại", "{{ Session::get('msg-error') }}", 'warning', {
        button:true,
        button: {
            text: "Xác nhận",
            value: true,
            visible: true,
            className: "btn-lg btn-primary" // Thay đổi màu sắc tại đây
        },
        timer: 5000,
        darkMode: true,

    });
    @endif

    @if(Session::has('msg-info'))
    swal("Thành công", "{{ Session::get('msg-info') }}", 'info', {
        button:true,
        button: {
            text: "Xác nhận",
            value: true,
            visible: true,
            className: "btn-lg btn-primary" // Thay đổi màu sắc tại đây
        },
        timer: 5000,
        darkMode: true,

    });
    @endif
</script>
<script>
    $(document).ready(function() {
        $('#changePasswordBtn').click(function() {
            var formData = $('#changePasswordForm').serialize();
    
            $.ajax({
                url: '{{ route('admin.user.changepassword') }}', // Thay URL cho phù hợp nếu cần
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#changePasswordModal').modal('hide');
                        
                    // location.reload(); // Tải lại trang hiện tại
                    // Hiển thị thông báo thành công nếu cần
                    // alert('Đổi mật khẩu thành công.');
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    if (errors.current_password) {
                        $('#current-password-error').text(errors.current_password[0]);
                    }
                    if (errors.password) {
                        $('#password-error').text(errors.password[0]);
                    }
                }
            });
        });
    });
</script>


