@extends('admin.layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Kategori</span>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar/Icon</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Varian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories ?? [] as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            @if($category->image)
                                <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" width="50" height="50" class="rounded">
                            @elseif($category->icon)
                                <div style="width: 50px; height: 50px; background-color: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary-color);">
                                    <i class="{{ $category->icon }}" style="font-size: 24px;"></i>
                                </div>
                            @else
                                <img src="https://via.placeholder.com/50?text=No+Image" alt="{{ $category->name }}" width="50" height="50" class="rounded">
                            @endif
                        </td>
                        <td>{{ $category->name }}</td>
                        <td>{{ Str::limit($category->description, 50) }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($category->has_variants)
                                <span class="badge bg-primary">
                                    <i class="fas fa-mug-hot me-1"></i><i class="fas fa-cube ms-1"></i> Panas/Dingin
                                </span>
                            @else
                                <span class="badge bg-secondary">Tidak Ada</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-tags fa-3x mb-3 text-muted"></i>
                            <p>Belum ada kategori</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
