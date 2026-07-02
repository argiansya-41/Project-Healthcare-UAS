@extends('layouts.app')

@section('header-title', 'Update Catatan Imunisasi')

@section('content')
    <div class="card" style="max-width: 750px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('imunisasi.schedules.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Update Status Imunisasi Anak</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('imunisasi.schedules.update', $record->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label>Informasi Pelaksanaan:</label>
                    <div style="background-color: #f8fafc; border: 1px solid var(--card-border); padding: 16px; border-radius: 12px;">
                        <p style="margin-bottom: 6px;"><strong>Anak:</strong> {{ $record->child->name }} ({{ $record->child->getAgeMonths() }} Bulan)</p>
                        <p style="margin-bottom: 6px;"><strong>Jenis Vaksin:</strong> {{ $record->vaccine->name }} (Code: {{ $record->vaccine->code }})</p>
                        <p><strong>Orang Tua:</strong> {{ $record->child->parent->name }} (HP: {{ $record->child->parent->phone_number }})</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status Imunisasi</label>
                    <select id="status" name="status" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required onchange="toggleCompletedFields()">
                        <option value="scheduled" {{ old('status', $record->status) == 'scheduled' ? 'selected' : '' }}>Dijadwalkan (Scheduled)</option>
                        <option value="completed" {{ old('status', $record->status) == 'completed' ? 'selected' : '' }}>Selesai Pelaksanaan (Completed)</option>
                        <option value="missed" {{ old('status', $record->status) == 'missed' ? 'selected' : '' }}>Terlewat / Batal (Missed)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="scheduled_date">Tanggal Rencana Awal</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', $record->scheduled_date->toDateString()) }}" required>
                </div>

                <!-- Fields if completed -->
                <div class="form-group completed-field" style="display: none;">
                    <label for="administered_date">Tanggal Realisasi Vaksinasi</label>
                    <input type="date" id="administered_date" name="administered_date" class="form-control" value="{{ old('administered_date', $record->administered_date ? $record->administered_date->toDateString() : now()->toDateString()) }}">
                </div>

                <div class="form-group completed-field" style="display: none;">
                    <label for="batch_number">Nomor Batch Vaksin</label>
                    <input type="text" id="batch_number" name="batch_number" class="form-control" placeholder="Contoh: B-BCG-998" value="{{ old('batch_number', $record->batch_number) }}">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="notes">Catatan Riwayat (Kondisi/Reaksi Panas/Bengkak)</label>
                    <textarea id="notes" name="notes" class="form-control" placeholder="Catatan opsional..." rows="3">{{ old('notes', $record->notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="grid-column: span 2; justify-content: center; padding: 14px; margin-top: 10px;">
                    <i class="ri-save-line"></i> Simpan Catatan Imunisasi
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    function toggleCompletedFields() {
        const status = document.getElementById('status').value;
        const fields = document.querySelectorAll('.completed-field');
        fields.forEach(f => {
            f.style.display = status === 'completed' ? 'flex' : 'none';
        });
    }
    document.addEventListener("DOMContentLoaded", toggleCompletedFields);
</script>
@endsection
