@extends('layouts.app')

@section('header-title', 'Reminder Imunisasi')

@section('content')
    <div class="card">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Daftar Reminder Pengingat Imunisasi Anak</h3>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jadwal Vaksin</th>
                        <th>Vaksin</th>
                        <th>Anak</th>
                        <th>Orang Tua (Parent)</th>
                        <th>Tanggal Kirim</th>
                        <th>Status Kirim</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reminders as $rem)
                        <tr>
                            <td><code>#{{ $rem->id }}</code></td>
                            <td>{{ $rem->record->scheduled_date->format('d/m/Y') }}</td>
                            <td><span class="badge badge-info">{{ $rem->record->vaccine->name }}</span></td>
                            <td><strong>{{ $rem->record->child->name }}</strong><br><small style="color: var(--text-secondary)">{{ $rem->record->child->getAgeMonths() }} Bulan</small></td>
                            <td>{{ $rem->parent->name }}<br><small style="color: var(--text-secondary)">HP: {{ $rem->parent->phone_number }}</small></td>
                            <td>{{ $rem->send_date->format('d/m/Y') }}</td>
                            <td>
                                @if($rem->status === 'sent')
                                    <span class="badge badge-success"><i class="ri-check-line"></i> Terkirim</span>
                                @elseif($rem->status === 'failed')
                                    <span class="badge badge-danger">Gagal</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($rem->status !== 'sent')
                                    <form action="{{ route('imunisasi.reminders.send', $rem->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="ri-send-plane-line"></i> Kirim Sekarang</button>
                                    </form>
                                @else
                                    <span style="color: var(--text-secondary); font-size: 13px; font-style: italic;"><i class="ri-checkbox-circle-line" style="color: var(--success);"></i> Terkirim via {{ ucfirst($rem->channel) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 32px;">Reminder tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $reminders->links() }}
        </div>
    </div>
@endsection
