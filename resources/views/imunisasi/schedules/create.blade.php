@extends('layouts.app')

@section('header-title', 'Buat Jadwal Imunisasi')

@section('content')
    <div class="card" style="max-width: 750px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('imunisasi.schedules.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Rencana Vaksinasi</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('imunisasi.schedules.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="child_id">Nama Anak / Bayi</label>
                    <select id="child_id" name="child_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Penerima Vaksin</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ (request('child_id') == $child->id || old('child_id') == $child->id) ? 'selected' : '' }}>
                                {{ $child->name }} ({{ $child->getAgeMonths() }} Bulan | Orang Tua: {{ $child->parent->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="vaccine_id">Jenis Vaksin</label>
                    <select id="vaccine_id" name="vaccine_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Vaksin</option>
                        @foreach($vaccines as $v)
                            <option value="{{ $v->id }}" {{ old('vaccine_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} (Target: {{ $v->target_age_months }} Bln)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status Imunisasi Awal</label>
                    <select id="status" name="status" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required onchange="toggleCompletedFields()">
                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Dijadwalkan (Belum Suntik)</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai (Sudah Suntik Hari Ini)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="scheduled_date">Tanggal Rencana Imunisasi</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', now()->toDateString()) }}" required>
                </div>

                <!-- Fields if completed -->
                <div class="form-group completed-field" style="display: none;">
                    <label for="administered_date">Tanggal Pelaksanaan Riil</label>
                    <input type="date" id="administered_date" name="administered_date" class="form-control" value="{{ old('administered_date', now()->toDateString()) }}">
                </div>

                <div class="form-group completed-field" style="display: none;">
                    <label for="batch_number">Nomor Batch Vaksin</label>
                    <input type="text" id="batch_number" name="batch_number" class="form-control" placeholder="Contoh: B-COV-998" value="{{ old('batch_number') }}">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="notes">Catatan Tambahan (Kondisi Anak/Reaksi)</label>
                    <textarea id="notes" name="notes" class="form-control" placeholder="Catatan opsional..." rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="grid-column: span 2; justify-content: center; padding: 14px; margin-top: 10px;">
                    <i class="ri-save-line"></i> Simpan Jadwal Imunisasi
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
