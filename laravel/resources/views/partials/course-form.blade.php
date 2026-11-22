<form method="post" action="{{ $action }}" class="row g-2 align-items-end mb-4">
    @csrf
    <div class="col-sm-4">
        <label class="form-label" for="symbol">Валютная пара</label>
        <input type="text" name="symbol" id="symbol" value="{{ old('symbol') }}" placeholder="например, BNBUSDT"
               class="form-control @error('symbol') is-invalid @enderror" required maxlength="20">
        @error('symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-sm-4">
        <label class="form-label" for="price">Курс</label>
        <input type="text" name="price" id="price" value="{{ old('price') }}" placeholder="0.00000000"
               class="form-control @error('price') is-invalid @enderror" required inputmode="decimal">
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-sm-4">
        <button type="submit" class="btn btn-primary">{{ $button }}</button>
    </div>
</form>
