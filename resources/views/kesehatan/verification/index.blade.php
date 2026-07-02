@extends('layouts.app')

@section('header-title', 'Verifikasi Kasus Penyakit')

@section('content')
    <div class="card">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Daftar Kasus Masuk Menunggu Verifikasi</h3>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal Lapor</th>
                        <th>Nama Pasien</th>
                        <th>NIK</th>
                        <th>Penyakit</th>
                        <th>Keparahan</th>
                        <th style="width: 35%;">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $rep)
                        <tr>
                            <td><code>#{{ $rep->id }}</code></td>
                            <td>{{ $rep->report_date->format('d/m/Y') }}</td>
                            <td><strong>{{ $rep->patient_name }}</strong> ({{ $rep->patient_age }} Thn)</td>
                            <td>{{ $rep->patient_nik }}</td>
                            <td><span style="color: var(--danger); font-weight: 600;">{{ $rep->diseaseType->name }}</span></td>
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
                                <form action="" id="form-verify-{{ $rep->id }}" method="POST" style="display: flex; flex-direction: column; gap: 8px;">
                                    @csrf
                                    <input type="text" name="verification_notes" id="notes-{{ $rep->id }}" class="form-control" style="padding: 8px 12px; font-size: 13px;" placeholder="Catatan hasil verifikasi..." required>
                                    
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" onclick="submitVerification({{ $rep->id }}, 'verify')" class="btn btn-primary btn-sm" style="flex-grow: 1; justify-content: center;"><i class="ri-checkbox-circle-line"></i> Setujui</button>
                                        <button type="button" onclick="submitVerification({{ $rep->id }}, 'reject')" class="btn btn-danger btn-sm" style="flex-grow: 1; justify-content: center;"><i class="ri-close-circle-line"></i> Tolak</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">Tidak ada kasus penyakit menunggu verifikasi.</td>
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

@section('scripts')
<script>
    function submitVerification(id, action) {
        const notes = document.getElementById('notes-' + id).value;
        if (!notes.trim()) {
            alert('Catatan verifikasi wajib diisi!');
            return;
        }

        const form = document.getElementById('form-verify-' + id);
        form.action = "{{ url('kesehatan/verification') }}/" + id + "/" + action;
        form.submit();
    }
</script>
@endsection
