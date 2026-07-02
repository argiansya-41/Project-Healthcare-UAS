@extends('layouts.app')

@section('header-title', 'Pengajuan Restock Obat')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Riwayat Pengajuan Restock Obat Saya</h3>
            <a href="{{ route('apotek.restock-requests.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Buat Pengajuan Baru
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Pengajuan</th>
                        <th>Obat</th>
                        <th>Jumlah Pengajuan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status Approval</th>
                        <th>Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td><code>#{{ $req->id }}</code></td>
                            <td>
                                <strong>{{ $req->medicine->name }}</strong>
                                <br><small style="color: var(--text-secondary)">{{ $req->medicine->code }}</small>
                            </td>
                            <td><strong>{{ $req->quantity }} {{ $req->medicine->unit->abbreviation }}</strong></td>
                            <td>{{ $req->created_at->format('d/m/Y H:i') }} WIB</td>
                            <td>
                                @if($req->status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($req->status === 'rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($req->approved_by)
                                    {{ $req->approvedBy->name }}
                                    <br><small style="color: var(--text-secondary)">{{ $req->approved_at->format('d/m/Y H:i') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 32px;">Belum mengajukan restock obat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
