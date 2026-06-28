@extends('layouts.app')
@section('title', __('ui.pg_edit_expense'))
@section('page-title', __('ui.pg_edit_expense'))

@section('content')
<div class="chart-card" style="max-width:800px">
    <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_budget') }} *</label>
                <select name="budget_id" class="form-select" required>
                    @foreach($budgets as $b)
                        <option value="{{ $b->id }}" {{ old('budget_id',$expense->budget_id)==$b->id?'selected':'' }}>
                            {{ $b->title }} — {{ $b->company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_company') }} *</label>
                <select name="company_id" class="form-select" required>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ old('company_id',$expense->company_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_title') }} *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title',$expense->title) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_vendor') }}</label>
                <input type="text" name="vendor" class="form-control" value="{{ old('vendor',$expense->vendor) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">{{ __('ui.lbl_amount') }} *</label>
                <div class="input-group">
                    <span class="input-group-text">{{ currency_sym($expense->currency) }}</span>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control" value="{{ old('amount',$expense->amount) }}" required>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">{{ __('ui.lbl_currency') }}</label>
                <select name="currency" class="form-select">
                    @foreach(['SAR','USD','EUR','GBP','AED','EGP','OMR','KWD','QAR','BHD'] as $cur)
                    <option value="{{ $cur }}" {{ old('currency',$expense->currency)==$cur?'selected':'' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_project') }}</label>
                <select name="project_id" class="form-select">
                    <option value="">{{ __('ui.ph_none') }}</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id',$expense->project_id)==$p->id?'selected':'' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_task') }}</label>
                <select name="task_id" class="form-select">
                    <option value="">{{ __('ui.ph_none') }}</option>
                    @foreach($tasks as $t)
                        <option value="{{ $t->id }}" {{ old('task_id',$expense->task_id)==$t->id?'selected':'' }}>{{ $t->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_expense_date') }} *</label>
                <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date',$expense->expense_date->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_payment_method') }}</label>
                <select name="payment_method" class="form-select">
                    @foreach(['company_card'=>'Company Card','bank_transfer'=>'Bank Transfer','cash'=>'Cash','personal_reimbursable'=>'Personal (Reimbursable)'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('payment_method',$expense->payment_method)==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_status') }}</label>
                <select name="status" class="form-select">
                    @foreach(['paid','pending','reimbursed'] as $s)
                    <option value="{{ $s }}" {{ old('status',$expense->status)==$s?'selected':'' }}>{{ __('ui.status_'.$s) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Invoice file --}}
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_invoice') }}</label>

                @if($expense->invoice_file)
                <div class="mb-2 p-3 rounded-3 d-flex align-items-center gap-3" style="background:#f0fdf4;border:1px solid #bbf7d0">
                    @if($expense->isImage())
                        <img src="{{ $expense->invoiceUrl() }}" alt="Invoice" style="height:56px;border-radius:6px;object-fit:cover">
                    @else
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $expense->invoice_file_name }}</div>
                        <a href="{{ $expense->invoiceUrl() }}" target="_blank" class="small text-primary">View file</a>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="remove_file" value="1" id="removeFile">
                        <label class="form-check-label small text-danger" for="removeFile">Remove file</label>
                    </div>
                </div>
                <div class="small text-muted mb-2">Upload a new file below to replace the current one, or leave empty to keep it.</div>
                @endif

                <div class="upload-zone" id="uploadZone"
                     style="border:2px dashed #d1d5db;border-radius:10px;padding:20px;text-align:center;cursor:pointer">
                    <i class="bi bi-cloud-upload fs-3 text-muted d-block mb-1"></i>
                    <div class="small text-muted">Click or drag &amp; drop — PDF, JPG, PNG (max 10 MB)</div>
                    <input type="file" name="invoice_file" id="invoiceFile"
                           accept=".pdf,.jpg,.jpeg,.png,.gif,.webp" style="display:none">
                </div>
                <div id="filePreview" class="mt-2" style="display:none">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <i class="bi bi-file-earmark-check-fill text-success fs-5"></i>
                        <span id="fileName" class="small fw-semibold text-success flex-grow-1"></span>
                        <button type="button" id="clearFile" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                @error('invoice_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_notes') }}</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes',$expense->notes) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('ui.btn_update_expense') }}</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">{{ __('ui.btn_cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const zone = document.getElementById('uploadZone');
const fileInput = document.getElementById('invoiceFile');
const preview = document.getElementById('filePreview');
const fileNameEl = document.getElementById('fileName');

zone.addEventListener('click', () => fileInput.click());
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='#4f46e5'; });
zone.addEventListener('dragleave', () => zone.style.borderColor='#d1d5db');
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.style.borderColor='#d1d5db';
    if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; showFile(e.dataTransfer.files[0]); }
});
fileInput.addEventListener('change', () => { if (fileInput.files.length) showFile(fileInput.files[0]); });

function showFile(f) {
    fileNameEl.textContent = f.name + ' (' + (f.size/1024).toFixed(0) + ' KB)';
    preview.style.display = 'block';
    zone.style.display = 'none';
}

document.getElementById('clearFile')?.addEventListener('click', () => {
    fileInput.value = ''; preview.style.display = 'none'; zone.style.display = 'block';
});
</script>
@endpush
