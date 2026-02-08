<div class="modal fade" id="createSavingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Target Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('savings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Nama Target</label>
                        <input type="text" name="name" class="form-control form-control-lg bg-light border-0" placeholder="Contoh: Umroh, Laptop Baru" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Target Jumlah (Rp)</label>
                        <input type="number" name="target_amount" class="form-control form-control-lg bg-light border-0" placeholder="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Pilih Ikon</label>
                        <div class="d-flex gap-2 justify-content-center p-3 bg-light rounded-3 overflow-auto">
                            <!-- Radio buttons customized as icons -->
                            @foreach(['💰', '🏠', '🚗', '✈️', '🕋', '📱', '🎓', '💍'] as $emoji)
                                <input type="radio" class="btn-check" name="icon" id="icon_{{ $loop->index }}" value="{{ $emoji }}" {{ $loop->first ? 'checked' : '' }}>
                                <label class="btn btn-outline-light text-dark border-0 fs-2" for="icon_{{ $loop->index }}">{{ $emoji }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Warna Kemajuan</label>
                        <div class="d-flex gap-2 justify-content-center">
                             @foreach(['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6610f2', '#fd7e14'] as $color)
                                <input type="radio" class="btn-check" name="color" id="color_{{ $loop->index }}" value="{{ $color }}" {{ $loop->first ? 'checked' : '' }}>
                                <label class="btn rounded-circle shadow-sm" for="color_{{ $loop->index }}" 
                                    style="width: 30px; height: 30px; background-color: {{ $color }}; border: 2px solid transparent;"></label>
                                <style>
                                    #color_{{ $loop->index }}:checked + label {
                                        border-color: #333 !important;
                                        transform: scale(1.1);
                                    }
                                </style>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Target</button>
                </div>
            </form>
        </div>
    </div>
</div>
