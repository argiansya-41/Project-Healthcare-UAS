@extends('layouts.app')

@section('header-title', 'Persetujuan Restock Obat')

@section('content')
    <div class="card">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Daftar Pengajuan Restock Obat Apotek</h3>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Obat</th>
                        <th>Jumlah Pengajuan</th>
                        <th>Diajukan Oleh</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td><code>#{{ $req->id }}</code></td>
                            <td>
                                <strong>{{ $req->medicine->name }}</strong>
                                <br><small style="color: var(--text-secondary)">Stok Saat Ini: {{ $req->medicine->stock }} {{ $req->medicine->unit->abbreviation }}</small>
                            </td>
                            <td><strong>{{ $req->quantity }} {{ $req->medicine->unit->abbreviation }}</strong></td>
                            <td>{{ $req->user->name }}</td>
                            <td>{{ $req->created_at->format('d/m/Y H:i') }} WIB</td>
                            <td>
                                @if($req->status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                    <br><small style="color: var(--text-secondary)">oleh {{ $req->approvedBy->name }}</small>
                                @elseif($req->status === 'rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                    <br><small style="color: var(--text-secondary)">oleh {{ $req->approvedBy->name }}</small>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($req->status === 'pending')
                                    <form action="{{ route('admin.restock.process', ['id' => $req->id, 'action' => 'approve']) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="ri-check-line"></i> Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.restock.process', ['id' => $req->id, 'action' => 'reject']) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ri-close-line"></i> Tolak</button>
                                    </form>
                                @else
                                    <span style="color: var(--text-secondary); font-size: 13px; font-style: italic;">Selesai diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">Belum ada pengajuan restock obat.</td>
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
