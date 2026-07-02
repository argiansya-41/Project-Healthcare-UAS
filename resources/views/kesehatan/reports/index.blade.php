@extends('layouts.app')

@section('header-title', 'Laporan Kasus Penyakit')

@section('content')
    @if(session('import_errors'))
        <div class="alert alert-danger" style="display: block; margin-bottom: 24px; padding: 20px; border-radius: 16px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <i class="ri-error-warning-fill" style="font-size: 24px; color: var(--danger);"></i>
                <strong style="font-size: 15px;">Terdapat kesalahan data saat impor file CSV:</strong>
            </div>
            <div style="max-height: 200px; overflow-y: auto; padding-left: 36px;">
                <ul style="margin: 0; padding: 0 0 0 16px; font-size: 13px; line-height: 1.6;">
                    @foreach(session('import_errors') as $err)
                        <li style="margin-bottom: 4px;">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
                <h3 style="font-size: 18px; font-weight: 700;">Daftar Kasus Penyakit Tercatat</h3>
            </div>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('kesehatan.reports.export', request()->query()) }}" class="btn btn-secondary" style="background-color: rgba(13, 148, 136, 0.1); color: var(--accent-color); border: 1px solid rgba(13, 148, 136, 0.2);">
                    <i class="ri-download-2-line"></i> Ekspor CSV
                </a>
                <button type="button" class="btn btn-secondary" onclick="openImportModal()">
                    <i class="ri-upload-2-line"></i> Impor CSV
                </button>
                <a href="{{ route('kesehatan.reports.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Input Laporan Baru
                </a>
            </div>
        </div>

        <!-- Filters Form -->
        <form action="{{ route('kesehatan.reports.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama pasien atau NIK..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="disease_type_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Penyakit</option>
                    @foreach($diseaseTypes as $type)
                        <option value="{{ $type->id }}" {{ request('disease_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <select name="severity" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Keparahan</option>
                    <option value="ringan" {{ request('severity') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                    <option value="sedang" {{ request('severity') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="berat" {{ request('severity') == 'berat' ? 'selected' : '' }}>Berat</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <select name="status" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex-grow: 1; justify-content: center;"><i class="ri-search-2-line"></i> Cari</button>
                @if(request()->anyFilled(['search', 'disease_type_id', 'severity', 'status']))
                    <a href="{{ route('kesehatan.reports.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
                @endif
            </div>
        </form>

        <!-- Reports Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal Lapor</th>
                        <th>Identitas Pasien</th>
                        <th>Penyakit</th>
                        <th>Tingkat Keparahan</th>
                        <th>Status Verifikasi</th>
                        <th>Rekomendasi Medis</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $rep)
                        <tr>
                            <td>{{ $rep->report_date->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $rep->patient_name }}</strong>
                                <br><small style="color: var(--text-secondary)">{{ $rep->patient_age }} Thn, {{ $rep->patient_gender }} | NIK: {{ $rep->patient_nik }}</small>
                                @if($rep->village)
                                    <br><small style="color: var(--accent-color); font-weight: 500;"><i class="ri-map-pin-2-line"></i> {{ $rep->village->name }}</small>
                                @endif
                            </td>
                            <td><strong>{{ $rep->diseaseType->name }}</strong> ({{ $rep->diseaseType->code }})</td>
                            <td>
                                @if($rep->severity === 'ringan')
                                    <span class="badge badge-info">Ringan</span>
                                @elseif($rep->severity === 'sedang')
                                    <span class="badge badge-warning">Sedang</span>
                                @else
                                    <span class="badge badge-danger">Berat</span>
                                @endif
                            </td>
                            <td>
                                @if($rep->status === 'verified')
                                    <span class="badge badge-success">Terverifikasi</span>
                                @elseif($rep->status === 'rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($rep->treatment_recommendation)
                                    <span style="color: var(--success); font-weight: 500;" title="{{ $rep->treatment_recommendation }}"><i class="ri-checkbox-circle-fill"></i> Selesai</span>
                                @else
                                    <span style="color: var(--text-secondary); font-style: italic;">Belum Ada</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('kesehatan.reports.show', $rep->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;"><i class="ri-eye-line"></i> Detail</a>
                                
                                @if(auth()->user()->isAdmin() || auth()->user()->role === 'petugas_medis')
                                    <form action="{{ route('kesehatan.reports.destroy', $rep->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan kasus ini?')">
                                            <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">Laporan kasus tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $reports->links() }}
        </div>
    </div>
@endsection

<!-- Modal Impor CSV -->
<div id="importModal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <!-- Backdrop -->
    <div style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);" onclick="closeImportModal()"></div>
    
    <!-- Modal Card -->
    <div class="card" style="position: relative; z-index: 10; width: 100%; max-width: 500px; background: rgba(255, 255, 255, 0.95); border: 1px solid var(--card-border); border-radius: 24px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(13, 148, 136, 0.1); color: var(--accent-color); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="ri-upload-2-line"></i>
                </div>
                <h3 style="font-size: 18px; font-weight: 700;">Impor Kasus Penyakit</h3>
            </div>
            <button type="button" style="background: none; border: none; font-size: 24px; color: var(--text-secondary); cursor: pointer;" onclick="closeImportModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
            Unggah berkas data kasus penyakit dalam format CSV. Pastikan kolom data sesuai dengan template yang disediakan agar data dapat diproses dengan benar.
        </p>

        <!-- Download Template Section -->
        <div style="background: #f8fafc; border: 1px dashed var(--card-border); border-radius: 16px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-primary);">Template CSV Laporan</span>
                <span style="font-size: 11px; color: var(--text-secondary);">Gunakan template standar ini</span>
            </div>
            <a href="{{ route('kesehatan.reports.template') }}" class="btn btn-secondary btn-sm" style="background: #ffffff; color: var(--text-primary); border: 1px solid var(--card-border);">
                <i class="ri-download-line"></i> Unduh
            </a>
        </div>

        <form action="{{ route('kesehatan.reports.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="csvFile" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); margin-bottom: 8px;">Pilih File CSV</label>
                <input type="file" id="csvFile" name="file" accept=".csv" required class="form-control" style="padding: 10px 14px;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeImportModal()">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-upload-2-line"></i> Impor Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
    <script>
        function openImportModal() {
            const modal = document.getElementById('importModal');
            modal.style.display = 'flex';
            // Simple animation
            setTimeout(() => {
                modal.querySelector('.card').style.transform = 'scale(1)';
            }, 50);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImportModal();
            }
        });

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            const card = modal.querySelector('.card');
            if (card) {
                card.style.transform = 'scale(0.9)';
            }
            setTimeout(() => {
                modal.style.display = 'none';
            }, 150);
        }
    </script>
@endsection

