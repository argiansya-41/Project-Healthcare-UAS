@extends('layouts.app')

@section('header-title', 'Riwayat Aktivitas Sistem')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0;">Log Audit Trail Keamanan & Aktivitas</h3>
            @if($logs->total() > 0)
                <form action="{{ route('admin.logs.clearAll') }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan semua log aktivitas sistem?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-7-line"></i> Bersihkan Semua
                    </button>
                </form>
            @endif
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.logs') }}" method="GET" autocomplete="off" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari deskripsi log, IP Address, atau nama user..." value="{{ request()->query('search') }}" autocomplete="off">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <select name="action" class="form-control" autocomplete="off" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="" {{ request()->query('action') === null || request()->query('action') === '' ? 'selected' : '' }}>Semua Tipe Aksi</option>
                    <option value="login" {{ request()->query('action') === 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request()->query('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="register" {{ request()->query('action') === 'register' ? 'selected' : '' }}>Registrasi Warga</option>
                    <option value="create_user" {{ request()->query('action') === 'create_user' ? 'selected' : '' }}>Tambah User</option>
                    <option value="update_user" {{ request()->query('action') === 'update_user' ? 'selected' : '' }}>Edit User</option>
                    <option value="delete_user" {{ request()->query('action') === 'delete_user' ? 'selected' : '' }}>Hapus User</option>
                    <option value="create_medicine" {{ request()->query('action') === 'create_medicine' ? 'selected' : '' }}>Tambah Obat</option>
                    <option value="medicine_transaction" {{ request()->query('action') === 'medicine_transaction' ? 'selected' : '' }}>Transaksi Stok</option>
                    <option value="request_restock" {{ request()->query('action') === 'request_restock' ? 'selected' : '' }}>Pengajuan Restock</option>
                    <option value="approve_restock" {{ request()->query('action') === 'approve_restock' ? 'selected' : '' }}>Approval Restock</option>
                    <option value="create_disease_report" {{ request()->query('action') === 'create_disease_report' ? 'selected' : '' }}>Lapor Penyakit</option>
                    <option value="verify_disease_report" {{ request()->query('action') === 'verify_disease_report' ? 'selected' : '' }}>Verifikasi Penyakit</option>
                    <option value="add_treatment_recommendation" {{ request()->query('action') === 'add_treatment_recommendation' ? 'selected' : '' }}>Rekomendasi Dokter</option>
                    <option value="create_child" {{ request()->query('action') === 'create_child' ? 'selected' : '' }}>Registrasi Anak</option>
                    <option value="schedule_immunization" {{ request()->query('action') === 'schedule_immunization' ? 'selected' : '' }}>Jadwal Imunisasi</option>
                    <option value="send_reminder" {{ request()->query('action') === 'send_reminder' ? 'selected' : '' }}>Kirim Reminder</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex-grow: 1; justify-content: center;"><i class="ri-search-2-line"></i> Cari</button>
                @if(request()->anyFilled(['search', 'action']))
                    <a href="{{ route('admin.logs') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
                @endif
            </div>
        </form>

        <!-- Logs Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Waktu</th>
                        <th style="width: 18%;">Pengguna</th>
                        <th style="width: 18%;">Tipe</th>
                        <th style="width: 28%;">Deskripsi Aktivitas</th>
                        <th style="width: 13%;">IP & UA</th>
                        <th style="text-align: right; width: 8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><strong>{{ $log->created_at->format('d M Y') }}</strong><br><small style="color: var(--text-secondary)">{{ $log->created_at->format('H:i:s') }} WIB</small></td>
                            <td>
                                @if($log->user)
                                    <strong>{{ $log->user->name }}</strong>
                                    <br><small style="color: var(--text-secondary)">{{ ucfirst($log->user->role) }}</small>
                                @else
                                    <span style="color: var(--text-secondary)">Sistem / Guest</span>
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ $log->action }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td>
                                <code>{{ $log->ip_address }}</code>
                                <br><small style="color: var(--text-secondary); display: inline-block; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </small>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <form action="{{ route('admin.logs.destroy', $log->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" title="Hapus Log" onclick="return confirm('Apakah Anda yakin ingin menghapus log aktivitas ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 32px;">Log aktivitas tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
