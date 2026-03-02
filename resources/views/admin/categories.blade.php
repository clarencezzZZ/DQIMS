@extends('layouts.app')

@section('title', 'Category Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-tags text-primary"></i> Category Management</h2>
                    <p class="text-muted mb-0">Manage service categories and queue prefixes</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-circle"></i> Add Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @forelse($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header" style="background-color: {{ $category->color }}; color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-tag"></i> {{ $category->name }}</h5>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" {{ $category->is_active ? 'checked' : '' }} disabled>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-secondary">{{ $category->section }}</span>
                            <span class="badge bg-light text-dark ms-2">{{ $category->code }}</span>
                        </div>
                        <p class="card-text text-muted">{{ $category->description ?? 'No description' }}</p>
                        
                        <hr>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Inquiries</h6>
                                <h4>{{ $category->inquiries_count ?? 0 }}</h4>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Staff</h6>
                                <h4>{{ $category->assigned_users_count ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-grid gap-2">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                <i class="bi bi-pencil"></i> Edit Category
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                <i class="bi bi-trash"></i> Delete Category
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-tags" style="font-size: 3rem;"></i>
                    <p class="mt-3">No categories found</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                <div class="alert alert-danger m-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Code <span class="text-danger">*</span></label>
                        <input type="text" name="section" class="form-control @error('section') is-invalid @enderror" placeholder="e.g., ACS, OOSS" required maxlength="20" value="{{ old('section') }}">
                        <div class="form-text">Short section identifier</div>
                        @error('section')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <input type="text" name="section_name" class="form-control @error('section_name') is-invalid @enderror" placeholder="e.g., Administrative and Client Services" required value="{{ old('section_name') }}">
                        <div class="form-text">Full section name</div>
                        @error('section_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g., CANCELATION OF PREVIOUSLY APPROVED SURVEY PLANS(DAR)" required value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="e.g., SECSIME NO.R4A-L_SMD-03" required maxlength="50" value="{{ old('code') }}">
                        <div class="form-text">Full category identifier code</div>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lobby</label>
                        <select name="lobby" class="form-select @error('lobby') is-invalid @enderror">
                            <option value="">Select Lobby</option>
                            <option value="lobby1" {{ old('lobby') == 'lobby1' ? 'selected' : '' }}>Lobby 1</option>
                            <option value="lobby2" {{ old('lobby') == 'lobby2' ? 'selected' : '' }}>Lobby 2</option>
                        </select>
                        @error('lobby')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Brief description of the category...">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color <span class="text-danger">*</span></label>
                        <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror" value="{{ old('color', '#28a745') }}" required>
                        @error('color')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modals -->
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: {{ $category->color }}; color: white;">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Code <span class="text-danger">*</span></label>
                        <input type="text" name="section" class="form-control" value="{{ $category->section }}" required maxlength="20">
                        <div class="form-text">Short section identifier</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <input type="text" name="section_name" class="form-control" placeholder="e.g., Administrative and Client Services" required>
                        <div class="form-text">Full section name</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ $category->code }}" required maxlength="50">
                        <div class="form-text">Full category identifier code</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lobby</label>
                        <select name="lobby" class="form-select">
                            <option value="">Select Lobby</option>
                            <option value="lobby1" {{ old('lobby', $category->lobby) == 'lobby1' ? 'selected' : '' }}>Lobby 1</option>
                            <option value="lobby2" {{ old('lobby', $category->lobby) == 'lobby2' ? 'selected' : '' }}>Lobby 2</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color <span class="text-danger">*</span></label>
                        <input type="color" name="color" class="form-control form-control-color" value="{{ $category->color }}" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active{{ $category->id }}" value="1" {{ $category->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active{{ $category->id }}">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Delete Category Modals -->
@foreach($categories as $category)
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the category <strong>{{ $category->name }}</strong>?</p>
                <p class="text-danger">This action cannot be undone and will permanently delete this category.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This category cannot be deleted if it has associated inquiries, assessments, or assigned users.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection


