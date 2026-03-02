@php
    $isEdit = isset($menu);
@endphp

<div class="card-dark mb-4">
    <div class="card-header-custom">
        <h6 class="mb-0"><i class="bi bi-list-nested me-2 text-teal"></i>Menü Bilgileri</h6>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="menuName">
                    Menü Adı <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="menuName" name="name"
                       value="{{ old('name', $menu->name ?? '') }}"
                       placeholder="Örn: Header Menü, Footer Keşfet...">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="menuLocation">
                    Konum Kodu <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control @error('location') is-invalid @enderror"
                       id="menuLocation" name="location"
                       value="{{ old('location', $menu->location ?? '') }}"
                       placeholder="Örn: header, footer_discover, footer_corporate..."
                       {{ $isEdit ? 'readonly' : '' }}>
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Tema içinde bu kodu kullanarak menüyü çağırabilirsiniz. Oluşturulduktan sonra değiştirilemez.</div>
            </div>

            <div class="col-12">
                <label class="form-label" for="menuDesc">Açıklama</label>
                <input type="text" class="form-control @error('description') is-invalid @enderror"
                       id="menuDesc" name="description"
                       value="{{ old('description', $menu->description ?? '') }}"
                       placeholder="Bu menünün ne işe yaradığını kısaca açıklayın...">
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="menuStatus">Durum</label>
                <select class="form-select" id="menuStatus" name="is_active">
                    <option value="1" {{ old('is_active', $menu->is_active ?? true) == true ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $menu->is_active ?? true) == false ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('admin.menus.index') }}" class="btn-glass">
        <i class="bi bi-x-lg me-1"></i>Vazgeç
    </a>
    <button type="submit" class="btn-teal">
        <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Güncelle' : 'Oluştur' }}
    </button>
</div>
