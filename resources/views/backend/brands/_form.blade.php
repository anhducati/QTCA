{{-- resources/views/backend/brands/_form.blade.php --}}
<div class="row">
    <div class="col-lg-8">
        <div class="form-group">
            <label for="name">Tên hãng xe <span class="text-danger">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name', $brand->name ?? '') }}"
                   required>
        </div>

        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description"
                      id="description"
                      class="form-control"
                      rows="3">{{ old('description', $brand->description ?? '') }}</textarea>
        </div>
    </div>
</div>
