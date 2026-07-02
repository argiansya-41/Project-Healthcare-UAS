@extends('layouts.app')

@section('header-title', 'Keluhan Imunisasi Pasca Suntik (KIPI)')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Riwayat Imunisasi & Pelaporan KIPI</h3>
                <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Laporkan keluhan pasca imunisasi anak Anda untuk mendapatkan tanggapan medis dari dokter.</p>
            </div>
        </div>

        @if(session('success'))
            <div style="padding: 16px; background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; color: var(--success); font-size: 14px; margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                <i class="ri-checkbox-circle-fill" style="font-size: 18px;"></i>
                {{ session('success') }}
            </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @forelse($complaints as $record)
                <div style="padding: 20px; border: 1px solid var(--card-border); border-radius: 16px; background-color: #ffffff; display: flex; flex-direction: column; gap: 16px; transition: all 0.2s ease;" class="complaint-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <strong style="font-size: 16px; color: var(--text-primary);">{{ $record->child->name }}</strong>
                            <span style="font-size: 13px; color: var(--text-secondary); margin-left: 8px;">({{ $record->vaccine->name }})</span>
                            <div style="margin-top: 4px; display: flex; gap: 16px; font-size: 12px; color: var(--text-secondary);">
                                <span><i class="ri-calendar-event-line"></i> Suntik: {{ $record->administered_date ? $record->administered_date->format('d F Y') : '-' }}</span>
                                <span><i class="ri-user-heart-line"></i> Usia: {{ $record->child->getAgeMonths() }} bulan</span>
                            </div>
                        </div>
                        
                        <div>
                            @if(is_null($record->vaccine_complaint))
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openComplaintModal({{ $record->id }})" style="padding: 8px 14px; background-color: rgba(239, 68, 68, 0.05); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.15);">
                                    <i class="ri-heart-pulse-line"></i> Lapor Keluhan
                                </button>
                            @elseif(is_null($record->doctor_response))
                                <span class="badge badge-warning" style="font-size: 11px; cursor: pointer; padding: 6px 12px;" onclick="openComplaintModal({{ $record->id }}, '{{ addslashes($record->vaccine_complaint) }}')">
                                    <i class="ri-time-line"></i> Menunggu Respon Dokter
                                </span>
                            @else
                                <span class="badge badge-success" style="font-size: 11px; padding: 6px 12px;">
                                    <i class="ri-checkbox-circle-line"></i> Ditanggapi Dokter
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!is_null($record->vaccine_complaint))
                        <div style="background-color: #f8fafc; border: 1px solid var(--card-border); padding: 14px 16px; border-radius: 12px; font-size: 13px;">
                            <strong style="color: var(--text-secondary); display: block; margin-bottom: 4px;">Keluhan yang Dilaporkan:</strong>
                            <p style="margin: 0; line-height: 1.5; color: var(--text-primary);">{{ $record->vaccine_complaint }}</p>
                        </div>
                    @endif

                    @if(!is_null($record->doctor_response))
                        <div style="background-color: rgba(16, 185, 129, 0.03); border: 1px solid rgba(16, 185, 129, 0.12); padding: 14px 16px; border-radius: 12px; font-size: 13px; color: #065f46;">
                            <strong style="display: block; margin-bottom: 4px;"><i class="ri-shield-user-fill"></i> Tanggapan Medis Dokter:</strong>
                            <p style="margin: 0; line-height: 1.5;">{{ $record->doctor_response }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div style="text-align: center; padding: 48px 24px; color: var(--text-secondary); border: 1px dashed var(--card-border); border-radius: 20px;">
                    <i class="ri-heart-pulse-line" style="font-size: 48px; color: var(--text-secondary); opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                    Belum ada riwayat imunisasi anak yang diselesaikan.
                </div>
            @endforelse
        </div>

        <div style="margin-top: 24px;">
            {{ $complaints->links() }}
        </div>
    </div>

    <!-- Modal Keluhan KIPI -->
    <div id="complaintModal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
        <!-- Backdrop -->
        <div style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);" onclick="closeComplaintModal()"></div>
        
        <!-- Modal Card -->
        <div class="card" style="position: relative; z-index: 10; width: 100%; max-width: 500px; background: rgba(255, 255, 255, 0.95); border: 1px solid var(--card-border); border-radius: 24px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="ri-heart-pulse-line"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 700;">Laporkan Keluhan Vaksin</h3>
                </div>
                <button type="button" style="background: none; border: none; font-size: 24px; color: var(--text-secondary); cursor: pointer;" onclick="closeComplaintModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                Laporkan efek samping atau keluhan pasca imunisasi (KIPI) yang dialami anak Anda (seperti demam, bengkak, rewel, dll.). Dokter puskesmas akan segera meninjau dan memberikan tanggapan medis.
            </p>

            <form id="complaintForm" method="POST" action="">
                @csrf
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="complaintText" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); margin-bottom: 8px;">Deskripsi Keluhan Anak</label>
                    <textarea id="complaintText" name="vaccine_complaint" class="form-control" style="padding: 12px 16px; font-size: 14px;" placeholder="Contoh: Anak demam tinggi sejak tadi malam, area suntikan kemerahan..." rows="4" required></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeComplaintModal()">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-send-plane-line"></i> Kirim Keluhan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openComplaintModal(recordId, currentComplaint = '') {
            const modal = document.getElementById('complaintModal');
            const form = document.getElementById('complaintForm');
            const textarea = document.getElementById('complaintText');
            
            form.action = `/warga/immunization-records/${recordId}/complaint`;
            textarea.value = currentComplaint;

            modal.style.display = 'flex';
            setTimeout(() => {
                modal.querySelector('.card').style.transform = 'scale(1)';
            }, 50);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeComplaintModal();
            }
        });

        function closeComplaintModal() {
            const modal = document.getElementById('complaintModal');
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
