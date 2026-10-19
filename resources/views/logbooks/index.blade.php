@extends('layouts.app')

@section('title', 'SIMMM - Logbook Kegiatan')

@section('content')
@php 
    $user = auth()->user();
    $isAdmin = $user->role === 'admin'; 
    $isSelesai = !$isAdmin && ($user->mahasiswa?->status === 'selesai');
@endphp

<x-topbar :title="$isAdmin ? 'Logbook dan Monitoring Mahasiswa' : 'Logbook Kegiatan Harian'" :subtitle="$isAdmin ? 'Pantau dan tinjau catatan logbook seluruh mahasiswa magang.' : ($isSelesai ? 'Riwayat lengkap catatan kegiatan dan pencapaian selama magang.' : 'Catatan harian aktivitas dan pencapaian selama magang di Bank Syariah Indonesia Area Yogyakarta.')">
    <x-slot:actions>
        @if($isSelesai)
            <span class="badge badge-ok" style="background:#e7f3ed; color:var(--green); font-weight:700;">Magang Selesai</span>
        @elseif(!$isAdmin)
            <button type="button" class="btn btn-solid" onclick="openLogbookModal()">+ Tambah Logbook</button>
        @endif
    </x-slot:actions>
</x-topbar>

<div id="flash-container">
    @if (session('success'))
        <div class="flash">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif
</div>

<div class="card" id="logbook-card">
    <div class="card-header">
        <div>
            <h3>{{ $isAdmin ? 'Semua Logbook Mahasiswa' : 'Daftar Logbook Kegiatan' }}</h3>
            <div class="card-sub">{{ $isAdmin ? 'Tinjau dan ubah status logbook yang diajukan mahasiswa.' : ($isSelesai ? 'Histori aktivitas resmi yang telah dilaporkan selama magang.' : 'Histori aktivitas yang telah dilaporkan.') }}</div>
        </div>
        @if($isSelesai)
            <div style="font-size:12px; color:var(--muted); font-style:italic;">Mode arsip, tidak dapat menambah/mengubah data</div>
        @endif
    </div>

    <div id="logbook-table-container">
        @if($logbooks->isEmpty())
            <div class="empty-state" id="logbook-empty-state">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <h3>Belum Ada Logbook</h3>
                <p>{{ $isAdmin ? 'Belum ada mahasiswa yang mengajukan logbook.' : ($isSelesai ? 'Tidak ada catatan logbook dalam riwayat magang.' : 'Klik tombol "+ Tambah Logbook" di atas untuk mulai mencatat kegiatan harian Anda.') }}</p>
            </div>
        @else
            <div class="table-responsive">
                <table id="logbook-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul Kegiatan</th>
                            <th>Aktivitas &amp; Hasil</th>
                            <th>Status</th>
                            <th>Mahasiswa</th>
                            @if($isAdmin || !$isSelesai)<th>Aksi</th>@endif
                        </tr>
                    </thead>
                    <tbody id="logbook-tbody">
                        @foreach($logbooks as $logbook)
                            <tr data-id="{{ $logbook->id }}">
                                <td>{{ \Illuminate\Support\Carbon::parse($logbook->tanggal)->format('d-m-Y') }}</td>
                                <td><strong>{{ $logbook->judul }}</strong></td>
                                <td>
                                    <div>{{ \Illuminate\Support\Str::limit($logbook->aktivitas, 85) }}</div>
                                    @if($logbook->hasil)
                                        <div style="color:var(--muted); font-size:12.5px; margin-top:2px;">Hasil: {{ \Illuminate\Support\Str::limit($logbook->hasil, 60) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($logbook->status) === 'disetujui')
                                        <span class="badge badge-ok">Disetujui</span>
                                    @elseif(strtolower($logbook->status) === 'diajukan')
                                        <span class="badge badge-wait">Diajukan</span>
                                    @elseif(strtolower($logbook->status) === 'revisi')
                                        <span class="badge badge-danger">Revisi</span>
                                    @else
                                        <span class="badge" style="background:var(--line); color:var(--muted);">{{ strtoupper($logbook->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $logbook->user->name ?? '-' }}</td>
                                @if($isAdmin || !$isSelesai)
                                <td>
                                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                        @if($isAdmin)
                                            {{-- Admin/Mentor: ubah status saja, tidak bisa edit isi logbook --}}
                                            @if(strtolower($logbook->status) === 'diajukan')
                                                <form action="{{ route('logbooks.update', $logbook) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="disetujui">
                                                    <input type="hidden" name="tanggal" value="{{ $logbook->tanggal }}">
                                                    <input type="hidden" name="judul" value="{{ $logbook->judul }}">
                                                    <input type="hidden" name="aktivitas" value="{{ $logbook->aktivitas }}">
                                                    <input type="hidden" name="hasil" value="{{ $logbook->hasil }}">
                                                    <button type="submit" class="btn btn-solid btn-sm" title="Setujui logbook ini">✓ Setujui</button>
                                                </form>
                                                <form action="{{ route('logbooks.update', $logbook) }}" method="POST" style="display:inline;" data-confirm="Tandai logbook ini perlu direvisi oleh mahasiswa?" data-title="Konfirmasi Minta Revisi" data-button="Minta Revisi" data-variant="warning">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="revisi">
                                                    <input type="hidden" name="tanggal" value="{{ $logbook->tanggal }}">
                                                    <input type="hidden" name="judul" value="{{ $logbook->judul }}">
                                                    <input type="hidden" name="aktivitas" value="{{ $logbook->aktivitas }}">
                                                    <input type="hidden" name="hasil" value="{{ $logbook->hasil }}">
                                                    <button type="submit" class="btn btn-warning btn-sm" title="Minta revisi">Revisi</button>
                                                </form>
                                            @elseif(strtolower($logbook->status) === 'disetujui')
                                                <span style="font-size:12px; color:var(--green); font-weight:600;">Sudah disetujui</span>
                                            @elseif(strtolower($logbook->status) === 'revisi')
                                                <span style="font-size:12px; color:var(--amber); font-weight:600;">Menunggu revisi</span>
                                            @else
                                                <span style="font-size:12px; color:var(--muted);">Draft</span>
                                            @endif
                                        @else
                                            {{-- Mahasiswa aktif: edit dan hapus logbook milik sendiri --}}
                                            <a class="btn btn-ghost btn-sm" href="{{ route('logbooks.edit', $logbook) }}">Edit</a>
                                            <form action="{{ route('logbooks.destroy', $logbook) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus catatan logbook ini?" data-title="Hapus Logbook">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logbooks->hasPages())
                <div style="margin-top:20px; display:flex; justify-content:center;">
                    {{ $logbooks->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@if(!$isAdmin && !$isSelesai)
{{-- Modal Tambah Logbook --}}
<div id="logbook-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:580px;" role="dialog" aria-modal="true" aria-labelledby="logbook-modal-title">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--line); padding-bottom:14px;">
            <div id="logbook-modal-title" class="modal-title" style="margin:0; font-size:18px; font-weight:700;">Tambah Logbook Kegiatan</div>
            <button type="button" onclick="closeLogbookModal()" aria-label="Tutup modal" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
        </div>

        <div id="logbook-error-box" style="display:none; background:var(--danger-bg); color:var(--danger); border:1px solid #efc0b9; padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:13px; line-height:1.4;">
            <div id="logbook-error-message"></div>
        </div>

        <form id="logbook-form" action="{{ route('logbooks.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">Tanggal Kegiatan <span style="color:var(--danger);">*</span></label>
                <input type="date" name="tanggal" id="logbook-tanggal" value="{{ now()->format('Y-m-d') }}" required style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif;">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">Judul Kegiatan <span style="color:var(--danger);">*</span></label>
                <input type="text" name="judul" id="logbook-judul" placeholder="Contoh: Rekonsiliasi Dokumen Pembiayaan Griya" required maxlength="150" style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif;">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">Uraian Aktivitas <span style="color:var(--danger);">*</span></label>
                <textarea name="aktivitas" id="logbook-aktivitas" placeholder="Jelaskan secara rinci tugas atau kegiatan yang dilakukan hari ini..." required style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif; min-height:85px; resize:vertical;"></textarea>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">Hasil / Output Kegiatan <span style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                <textarea name="hasil" id="logbook-hasil" placeholder="Hasil nyata yang dicapai (misal: 15 berkas terverifikasi)" style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif; min-height:65px; resize:vertical;"></textarea>
            </div>

            <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:16px;">
                <button type="button" class="btn btn-ghost" onclick="closeLogbookModal()">Batal</button>
                <button type="submit" id="logbook-submit-btn" class="btn btn-solid">
                    <span id="logbook-btn-label">Simpan Logbook</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    const logbookModal = document.getElementById('logbook-modal');
    const logbookForm = document.getElementById('logbook-form');
    const logbookErrorBox = document.getElementById('logbook-error-box');
    const logbookErrorMessage = document.getElementById('logbook-error-message');
    const logbookSubmitBtn = document.getElementById('logbook-submit-btn');
    const logbookBtnLabel = document.getElementById('logbook-btn-label');

    function openLogbookModal() {
        if (!logbookModal) return;
        hideLogbookError();
        logbookModal.classList.add('active');
        logbookModal.setAttribute('aria-hidden', 'false');
    }

    function closeLogbookModal() {
        if (!logbookModal) return;
        logbookModal.classList.remove('active');
        logbookModal.setAttribute('aria-hidden', 'true');
        hideLogbookError();
        if (logbookForm) logbookForm.reset();
        const dateInput = document.getElementById('logbook-tanggal');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
        }
    }

    function showLogbookError(msg) {
        if (!logbookErrorBox || !logbookErrorMessage) return;
        logbookErrorMessage.innerHTML = msg;
        logbookErrorBox.style.display = 'block';
    }

    function hideLogbookError() {
        if (!logbookErrorBox) return;
        logbookErrorBox.style.display = 'none';
        if (logbookErrorMessage) logbookErrorMessage.innerHTML = '';
    }

    function showToastNotification(message) {
        const container = document.getElementById('flash-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'flash';
        toast.innerHTML = `
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>${message}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        `;
        container.prepend(toast);
        setTimeout(() => {
            if (toast && toast.parentElement) toast.remove();
        }, 6000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Close on clicking overlay outside the modal-box
        if (logbookModal) {
            logbookModal.addEventListener('click', (e) => {
                if (e.target === logbookModal) {
                    closeLogbookModal();
                }
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                if (logbookModal && logbookModal.classList.contains('active')) {
                    closeLogbookModal();
                }
            }
        });

        // Form submission with AJAX
        if (logbookForm) {
            logbookForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideLogbookError();

                const tanggalVal = document.getElementById('logbook-tanggal')?.value;
                const judulVal = document.getElementById('logbook-judul')?.value?.trim();
                const aktivitasVal = document.getElementById('logbook-aktivitas')?.value?.trim();
                const hasilVal = document.getElementById('logbook-hasil')?.value?.trim();

                if (!tanggalVal || !judulVal || !aktivitasVal) {
                    showLogbookError('Tanggal, judul kegiatan, dan uraian aktivitas wajib diisi.');
                    return;
                }

                if (logbookSubmitBtn) {
                    logbookSubmitBtn.disabled = true;
                    if (logbookBtnLabel) logbookBtnLabel.textContent = 'Menyimpan...';
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

                    const response = await fetch(logbookForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            tanggal: tanggalVal,
                            judul: judulVal,
                            aktivitas: aktivitasVal,
                            hasil: hasilVal || null,
                            _token: csrfToken
                        })
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        let errorMsg = result.message || 'Gagal menyimpan logbook.';
                        if (result.errors) {
                            const errList = Object.values(result.errors).flat().join('<br>');
                            errorMsg += `<div style="margin-top:4px;">${errList}</div>`;
                        }
                        showLogbookError(errorMsg);
                        return;
                    }

                    // Success: insert row to table dynamically
                    const d = result.data;
                    let tbody = document.getElementById('logbook-tbody');
                    const emptyState = document.getElementById('logbook-empty-state');

                    if (emptyState) {
                        const tableContainer = document.getElementById('logbook-table-container');
                        tableContainer.innerHTML = `
                            <table id="logbook-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Judul Kegiatan</th>
                                        <th>Aktivitas & Hasil</th>
                                        <th>Status</th>
                                        <th>Mahasiswa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="logbook-tbody"></tbody>
                            </table>
                        `;
                        tbody = document.getElementById('logbook-tbody');
                    }

                    const hasilHtml = d.hasil ? `<div style="color:var(--muted); font-size:12.5px; margin-top:2px;">Hasil: ${d.hasil}</div>` : '';

                    const newRow = document.createElement('tr');
                    newRow.setAttribute('data-id', d.id);
                    newRow.style.animation = 'fadeInUp 0.4s ease';
                    newRow.innerHTML = `
                        <td>${d.tanggal_formatted}</td>
                        <td><strong>${d.judul}</strong></td>
                        <td>
                            <div>${d.aktivitas}</div>
                            ${hasilHtml}
                        </td>
                        <td><span class="badge badge-wait">${d.status_upper}</span></td>
                        <td>${d.user_name}</td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <a class="btn btn-ghost btn-sm" href="${d.edit_url}">Edit</a>
                                <form action="${d.destroy_url}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus catatan logbook ini?" data-title="Hapus Logbook">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    `;

                    tbody.prepend(newRow);

                    // Close modal, show toast
                    closeLogbookModal();
                    showToastNotification(result.message || 'Logbook berhasil diajukan.');

                } catch (err) {
                    showLogbookError('Terjadi kesalahan jaringan atau server. Silakan coba kembali.');
                } finally {
                    if (logbookSubmitBtn) {
                        logbookSubmitBtn.disabled = false;
                        if (logbookBtnLabel) logbookBtnLabel.textContent = 'Simpan Logbook';
                    }
                }
            });
        }

        // Auto-open modal if URL has ?action=create
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'create') {
            openLogbookModal();
        }
    });
</script>
@endsection
