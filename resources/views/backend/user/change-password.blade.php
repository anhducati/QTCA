<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordModalLabel">Thay đổi mật khẩu</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="changePasswordForm">
            @csrf
            <div class="form-group">
              <label for="password">Mật khẩu mới</label>
              <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" class="form-control">
              @error('password')
              <div class="error-danger">* {{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="confirm-password">Xác nhận mật khẩu mới</label>
              <input type="password" name="confirm-password" id="confirm-password" placeholder="Nhập xác nhận mật khẩu" class="form-control">
              @error('confirm-password')
              <div class="error-danger">* {{ $message }}</div>
              @enderror
            </div>
            <button type="button" class="btn btn-primary" id="changePasswordBtn">Đổi mật khẩu</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <script>
  $(document).ready(function() {
      $('#changePasswordBtn').click(function() {
          var formData = $('#changePasswordForm').serialize();
  
          $.ajax({
              
              type: 'POST', // Đảm bảo sử dụng POST
              data: formData,
              success: function(response) {
                  alert(response.success);
                  $('#changePasswordModal').modal('hide');
                  $('#changePasswordForm')[0].reset();
              },
              error: function(response) {
                  if(response.responseJSON && response.responseJSON.error) {
                      alert(response.responseJSON.error);
                  } else {
                      alert('An error occurred.');
                  }
              }
          });
      });
  });
  </script>