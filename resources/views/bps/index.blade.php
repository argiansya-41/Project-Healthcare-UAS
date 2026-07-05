@extends('layouts.app')

@section('header-title', 'Statistik Kesehatan Nasional BPS')

@section('styles')
<style>
    .bps-header-card {
        background: linear-gradient(135deg, rgba(15, 118, 110, 0.12) 0%, rgba(8, 145, 178, 0.05) 100%);
        border: 1px solid rgba(15, 118, 110, 0.15);
        border-radius: 24px;
        padding: 28px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .bps-header-text h3 {
        font-size: 20px;
        font-weight: 800;
        color: var(--accent-color);
        margin-bottom: 6px;
    }

    .bps-header-text p {
        color: var(--text-secondary);
        font-size: 13.5px;
        line-height: 1.5;
    }

    .bps-update-badge {
        background-color: rgba(15, 118, 110, 0.08);
        border: 1px solid rgba(15, 118, 110, 0.12);
        color: var(--accent-color);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bps-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 28px;
    }

    .bps-chart-card {
        grid-column: span 2;
    }

    .bps-sidebar-card {
        grid-column: span 1;
    }

    @media (max-width: 991px) {
        .bps-grid-3 {
            grid-template-columns: 1fr;
        }
        .bps-chart-card,
        .bps-sidebar-card {
            grid-column: span 1 !important;
        }
    }

    .bps-card-interactive {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .chart-container {
        position: relative;
        width: 100%;
        margin-top: 12px;
    }

    .bps-national-badge {
        font-size: 11px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, var(--accent-color) 0%, #0891b2 100%);
        padding: 4px 10px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-bottom: 8px;
    }

    .national-row {
        background-color: rgba(15, 118, 110, 0.05) !important;
        font-weight: 700;
    }
    
    .national-row td {
        border-top: 2px solid rgba(15, 118, 110, 0.15) !important;
        border-bottom: 2px solid rgba(15, 118, 110, 0.15) !important;
        color: var(--accent-color) !important;
    }

    .national-row:hover td {
        background-color: rgba(15, 118, 110, 0.08) !important;
    }

    .bps-source-box {
        margin-top: 28px;
        background-color: rgba(255, 255, 255, 0.5);
        border: 1px solid var(--card-border);
        border-radius: 20px;
        padding: 20px;
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .bps-source-box strong {
        color: var(--text-primary);
    }

    /* Custom scroll for search table */
    .table-scrollable {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid var(--card-border);
        border-radius: 20px;
    }

    .table-scrollable table {
        margin-top: 0 !important;
    }

    .table-scrollable th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8fafc;
        box-shadow: 0 1px 0 var(--card-border);
    }

    .metric-value-box {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .metric-value-box span.unit {
        font-size: 14px;
        color: var(--text-secondary);
        font-weight: 500;
    }
</style>
@endsection

@section('content')

@if($error)
    <div class="alert alert-danger" style="margin-bottom: 28px;">
        <i class="ri-error-warning-fill" style="font-size: 24px;"></i>
        <div>
            <strong>Peringatan!</strong> {{ $error }}
        </div>
    </div>
@endif

<!-- BPS Info Header -->
<div class="bps-header-card">
    <div class="bps-header-text">
        <h3>{{ $judul_tabel }}</h3>
        <p>Visualisasi data resmi integrasi interoperabilitas SIMDASI Badan Pusat Statistik (BPS) Republik Indonesia.</p>
    </div>
    <div class="bps-update-badge">
        <i class="ri-time-line"></i>
        <span>Update BPS: {{ $table_updated ?? '-' }}</span>
    </div>
</div>

@php
    // Find national data for summary cards
    $national = null;
    foreach ($data as $row) {
        if ($row['label'] === 'Indonesia' || $row['kode_wilayah'] == '0000000') {
            $national = $row;
            break;
        }
    }
@endphp

@if($national)
<!-- National Summary Metric Cards -->
<div class="stats-grid">
    <!-- TBC Case Detection -->
    <div class="card stat-card">
        <div class="stat-info">
            <span class="bps-national-badge">Nasional</span>
            <h4>Penemuan TBC (CDR)</h4>
            <div class="metric-value-box">
                <p>{{ $national['variables']['uaikde6heaivlwdqabcf']['value'] ?? '-' }}</p>
                <span class="unit">%</span>
            </div>
        </div>
        <div class="stat-icon teal">
            <i class="ri-pulse-line"></i>
        </div>
    </div>

    <!-- TBC Treatment Success -->
    <div class="card stat-card">
        <div class="stat-info">
            <span class="bps-national-badge">Nasional</span>
            <h4>Sukses Pengobatan TBC</h4>
            <div class="metric-value-box">
                <p>{{ $national['variables']['xa3wrsnhbr4nmsqs4kri']['value'] ?? '-' }}</p>
                <span class="unit">%</span>
            </div>
        </div>
        <div class="stat-icon blue">
            <i class="ri-shield-check-line"></i>
        </div>
    </div>

    <!-- AIDS New Cases -->
    <div class="card stat-card">
        <div class="stat-info">
            <span class="bps-national-badge">Nasional</span>
            <h4>Kasus Baru AIDS</h4>
            <div class="metric-value-box">
                <p>{{ $national['variables']['fmumuesaff']['value'] ?? '-' }}</p>
                <span class="unit">jiwa</span>
            </div>
        </div>
        <div class="stat-icon red">
            <i class="ri-virus-fill"></i>
        </div>
    </div>

    <!-- DBD DHF Incidence -->
    <div class="card stat-card">
        <div class="stat-info">
            <span class="bps-national-badge">Nasional</span>
            <h4>Angka Kesakitan DBD</h4>
            <div class="metric-value-box">
                <p>{{ $national['variables']['9n8me3mg1beg4pv1jckl']['value'] ?? '-' }}</p>
                <span class="unit">/100k pddk</span>
            </div>
        </div>
        <div class="stat-icon orange">
            <i class="ri-temp-hot-line"></i>
        </div>
    </div>
</div>
@endif

<div class="bps-grid-3">
    <!-- Interactive Chart -->
    <div class="card bps-card-interactive bps-chart-card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 8px;">
            <div>
                <h3 style="font-size: 17px; font-weight: 700;">Grafik Perbandingan Provinsi</h3>
                <p style="font-size: 12px; color: var(--text-secondary);">Bandingkan tingkat kasus penyakit antar provinsi di Indonesia.</p>
            </div>
            
            <div class="form-group" style="margin-bottom: 0; min-width: 250px;">
                <select id="indicatorSelector" class="form-control" style="padding: 8px 16px; border-radius: 12px;">
                    @foreach($kolom as $key => $col)
                        <option value="{{ $key }}">{{ $col['metadata_indikator'] ?? $col['nama_variabel'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="bpsChart" style="max-height: 420px; width: 100%;"></canvas>
        </div>
    </div>

    <!-- Table Sidebar / Search and quick stats -->
    <div class="card bps-sidebar-card" style="display: flex; flex-direction: column; gap: 16px;">
        <h3 style="font-size: 17px; font-weight: 700;">Wilayah & Indikator</h3>
        <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">Cari provinsi secara instan untuk melihat performa indikator kesehatannya.</p>

        <div class="form-group" style="margin-bottom: 8px;">
            <div style="position: relative;">
                <i class="ri-search-line" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); font-size: 16px;"></i>
                <input type="text" id="provSearch" class="form-control" placeholder="Cari Provinsi..." style="padding-left: 44px; border-radius: 14px;">
            </div>
        </div>

        <div style="flex-grow: 1; display: flex; flex-direction: column; gap: 12px;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); border-bottom: 1px solid var(--card-border); padding-bottom: 6px;">
                Tinjauan Ringkas Variabel BPS
            </div>
            <div id="variableInfoContainer" style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; display: flex; flex-direction: column; gap: 12px; overflow-y: auto; max-height: 250px; padding-right: 4px;">
                <!-- Info of selected variable will be inserted here dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Detailed Data Table -->
<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        <h3 style="font-size: 17px; font-weight: 700;">Tabel Data Lengkap Provinsi</h3>
        <span style="font-size: 12px; color: var(--text-secondary);">Klik kepala tabel (header) untuk mengurutkan data berdasarkan indikator.</span>
    </div>

    <div class="table-responsive table-scrollable">
        <table class="table" id="bpsDataTable">
            <thead>
                <tr>
                    <th style="cursor: pointer;" onclick="sortTable(0)">Provinsi <i class="ri-arrow-up-down-line" style="font-size: 11px; margin-left: 4px; opacity: 0.6;"></i></th>
                    @php $colIndex = 1; @endphp
                    @foreach($kolom as $key => $col)
                        <th style="cursor: pointer; text-align: right;" onclick="sortTable({{ $colIndex }})" data-indicator-key="{{ $key }}">
                            {{ $col['metadata_indikator'] ?? $col['nama_variabel'] }}
                            <i class="ri-arrow-up-down-line" style="font-size: 11px; margin-left: 4px; opacity: 0.6;"></i>
                        </th>
                        @php $colIndex++; @endphp
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    @php
                        $isNational = ($row['label'] === 'Indonesia' || $row['kode_wilayah'] == '0000000');
                    @endphp
                    <tr class="{{ $isNational ? 'national-row' : '' }}" data-province-name="{{ strtolower($row['label']) }}">
                        <td>
                            @if($isNational)
                                <i class="ri-map-fill" style="margin-right: 6px;"></i>
                            @else
                                <i class="ri-map-pin-line" style="margin-right: 6px; opacity: 0.6;"></i>
                            @endif
                            <strong>{{ $row['label'] }}</strong>
                        </td>
                        @foreach($kolom as $key => $col)
                            <td style="text-align: right;" data-value="{{ $row['variables'][$key]['value'] ?? '' }}">
                                {{ $row['variables'][$key]['value'] ?? '-' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Source & Notes -->
<div class="bps-source-box">
    <div style="margin-bottom: 8px;">
        <strong>Catatan BPS:</strong>
        <div style="margin-top: 4px;">{!! $catatan !!}</div>
    </div>
    <div>
        <strong>Sumber Data:</strong>
        <div style="margin-top: 4px;">{!! $sumber !!}</div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Raw BPS data from PHP
    const bpsData = {!! json_encode($data) !!};
    const columns = {!! json_encode($kolom) !!};
    
    let chartInstance = null;

    // Helper: Parse Indonesian-formatted numbers to JS floats
    function parseBpsValue(valStr) {
        if (!valStr || valStr === '–' || valStr === '-' || valStr.trim() === '') return null;
        let clean = valStr.replace(/\./g, '').replace(/,/g, '.');
        let num = parseFloat(clean);
        return isNaN(num) ? null : num;
    }

    // Initialize/update chart based on selected indicator key
    function updateChart(indicatorKey) {
        const selectedColumn = columns[indicatorKey];
        const indicatorLabel = selectedColumn ? (selectedColumn.metadata_indikator || selectedColumn.nama_variabel) : 'Statistik';
        
        // Filter out "Indonesia" (national average/total) from the comparison chart
        const provincesOnly = bpsData.filter(row => row.label !== 'Indonesia' && row.kode_wilayah != '0000000');
        
        // Map data to chart format
        const chartData = provincesOnly.map(row => {
            const rawVal = row.variables[indicatorKey] ? row.variables[indicatorKey].value : null;
            return {
                province: row.label,
                value: parseBpsValue(rawVal),
                displayValue: rawVal || '-'
            };
        });

        // Sort descending so the highest is on the left
        chartData.sort((a, b) => {
            if (a.value === null) return 1;
            if (b.value === null) return -1;
            return b.value - a.value;
        });

        const labels = chartData.map(d => d.province);
        const values = chartData.map(d => d.value);

        // Update variable info sidebar
        updateVariableInfo(indicatorKey);

        const ctx = document.getElementById('bpsChart').getContext('2d');

        // Choose chart gradient colors based on indicator type
        let gradientColorStart = '#0f766e'; // teal
        let gradientColorEnd = '#0891b2'; // light-blue
        
        if (indicatorKey === 'fmumuesaff' || indicatorKey === 'czcjnafyzmbswvdud21x') {
            gradientColorStart = '#e11d48'; // rose
            gradientColorEnd = '#f43f5e';
        } else if (indicatorKey === '9n8me3mg1beg4pv1jckl') {
            gradientColorStart = '#ea580c'; // orange
            gradientColorEnd = '#f97316';
        } else if (indicatorKey === 'xa3wrsnhbr4nmsqs4kri') {
            gradientColorStart = '#059669'; // emerald
            gradientColorEnd = '#10b981';
        }

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, gradientColorStart);
        gradient.addColorStop(1, gradientColorEnd);

        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: indicatorLabel,
                    data: values,
                    backgroundColor: gradient,
                    borderColor: gradientColorStart,
                    borderWidth: 1,
                    borderRadius: 8,
                    hoverBackgroundColor: gradientColorEnd
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        titleFont: {
                            family: 'Plus Jakarta Sans',
                            size: 13,
                            weight: '700'
                        },
                        bodyFont: {
                            family: 'Plus Jakarta Sans',
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                return `Value: ${chartData[index].displayValue}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 10,
                                weight: '600'
                            },
                            color: '#64748b',
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)'
                        },
                        ticks: {
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11
                            },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    }

    // Dynamic variable definition updates
    function updateVariableInfo(key) {
        const col = columns[key];
        const container = document.getElementById('variableInfoContainer');
        if (!col) {
            container.innerHTML = '<p>Variabel tidak ditemukan.</p>';
            return;
        }

        let konsep = col.metadata_konsep_definisi || 'Definisi konsep tidak tersedia.';
        // Decode HTML entities
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = konsep;
        konsep = tempDiv.innerHTML;

        container.innerHTML = `
            <div style="background: rgba(15, 118, 110, 0.05); padding: 12px; border-radius: 12px; border-left: 4px solid var(--accent-color);">
                <strong style="color: var(--accent-color); display: block; margin-bottom: 4px; font-size: 13px;">${col.nama_variabel}</strong>
                <span style="font-size: 11px; display: inline-block; padding: 2px 6px; background-color: var(--card-border); border-radius: 4px; font-weight: 600;">
                    Tipe: ${col.tipe} (Desimal: ${col.angka_desimal_dibelakang_koma})
                </span>
            </div>
            <div>
                <strong>Konsep & Definisi:</strong>
                <div style="font-size: 12.5px; margin-top: 4px; color: var(--text-primary);">${konsep}</div>
            </div>
        `;
    }

    // Search bar filter for provinces table
    const provSearchInput = document.getElementById('provSearch');
    provSearchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        const rows = document.querySelectorAll('#bpsDataTable tbody tr');
        
        rows.forEach(row => {
            const provName = row.getAttribute('data-province-name') || '';
            if (provName.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Dropdown change handler
    document.getElementById('indicatorSelector').addEventListener('change', function() {
        updateChart(this.value);
    });

    // Initial load
    document.addEventListener('DOMContentLoaded', function() {
        const firstKey = Object.keys(columns)[0];
        if (firstKey) {
            updateChart(firstKey);
        }
    });

    // Sort table columns
    let sortDirections = {};
    function sortTable(colIndex) {
        const table = document.getElementById('bpsDataTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Toggle sort direction
        const currentDir = sortDirections[colIndex] || 'asc';
        const nextDir = currentDir === 'asc' ? 'desc' : 'asc';
        sortDirections[colIndex] = nextDir;

        // Reset arrow icons
        const headers = table.querySelectorAll('thead th');
        headers.forEach((th, idx) => {
            const icon = th.querySelector('i');
            if (icon) {
                if (idx === colIndex) {
                    icon.className = nextDir === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line';
                    icon.style.opacity = '1';
                } else {
                    icon.className = 'ri-arrow-up-down-line';
                    icon.style.opacity = '0.6';
                }
            }
        });

        // Always keep the 'Indonesia' row either at the very top or do not include in typical sort
        // Let's sort all rows, but put national-row at the top/bottom depending on priority,
        // or just let it sort but keep it highlighted. Actually, sorting the national row is fine,
        // but keeping it highlighted is important. Let's do standard sort.
        rows.sort((a, b) => {
            // Keep national-row first if sorting by text, or just sort normal
            const cellA = a.cells[colIndex];
            const cellB = b.cells[colIndex];

            let valA = cellA.innerText.trim();
            let valB = cellB.innerText.trim();

            // Try to parse values if it's numeric column (index > 0)
            if (colIndex > 0) {
                const numA = parseBpsValue(cellA.getAttribute('data-value') || valA);
                const numB = parseBpsValue(cellB.getAttribute('data-value') || valB);

                if (numA === null && numB === null) return 0;
                if (numA === null) return nextDir === 'asc' ? 1 : -1;
                if (numB === null) return nextDir === 'asc' ? -1 : 1;

                return nextDir === 'asc' ? numA - numB : numB - numA;
            } else {
                // String comparison
                return nextDir === 'asc' 
                    ? valA.localeCompare(valB) 
                    : valB.localeCompare(valA);
            }
        });

        // Append sorted rows
        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endsection
