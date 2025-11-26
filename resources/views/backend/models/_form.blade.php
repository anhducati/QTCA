{{-- backend/models/_form.blade.php --}}
<div class="form-group">
    <label for="name">Tên dòng xe <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" class="form-control"
           value="{{ old('name', $model->name ?? '') }}" required>
</div>

<div class="form-group">
    <label for="brand_id">Hãng xe</label>
    <select name="brand_id" id="brand_id" class="form-control">
        <option value="">-- Chọn hãng xe --</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->id }}"
                {{ (int)old('brand_id', $model->brand_id ?? 0) === $brand->id ? 'selected' : '' }}>
                {{ $brand->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="description">Mô tả</label>
    <textarea name="description" id="description" class="form-control" rows="3">
        {{ old('description', $model->description ?? '') }}
    </textarea>
</div>
