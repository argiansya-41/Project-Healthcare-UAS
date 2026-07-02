@extends('layouts.app')

@section('header-title', 'Data Supplier')

@section('content')
<div class="card">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold">Daftar Supplier</h3>
        <a href="{{ route('apotek.suppliers.create') }}" class="btn btn-primary">
            <i class="ri-add-circle-line"></i> Tambah Supplier
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kontak</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_name }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>{{ $supplier->address }}</td>
                        <td class="text-center">
                            <a href="{{ route('apotek.suppliers.edit', $supplier) }}" class="btn btn-sm btn-secondary mr-2">
                                <i class="ri-edit-2-line"></i> Edit
                            </a>
                            <form action="{{ route('apotek.suppliers.destroy', $supplier) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus supplier ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="ri-delete-bin-6-line"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500">Tidak ada supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
