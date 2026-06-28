@extends('layouts.app')
@section('title', 'Add Expense')
@section('page-title', 'Record an Expense')
@section('page-subtitle', 'Log what you spent — link it to a budget, project & task')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Budget selector with live balance --}}
                <div class="mb-4 p-3 rounded-3" style="background:#f8fafc;border:1px solid #e5e7eb">
                    <label class="form-label fw-semibold">From Which Budget? *</label>
                    <select name="budget_id" id="budgetSelect"
                            class="form-select @error('budget_id') is-invalid @enderror" required>
                        <option value="">Select the budget accounting gave you…</option>
                        @foreach(\App\Models\Budget::with('company')->where('status','active')->orderByDesc('received_date')->get() as $b)
                            <option value="{{ $b->id }}"
                                    data-amount="{{ $b->amount }}"
                                    data-spent="{{ $b->totalSpent() }}"
                                    data-company="{{ $b->company_id }}"
                                    data-currency="{{ $b->currency }}"
                                    {{ (old('budget_id', $selectedBudget?->id) == $b->id) ? 'selected' : '' }}>
                                {{ $b->title }} — {{ $b->company->name }}
                                ({{ currency_sym($b->currency) }} {{ number_format($b->remaining(), 2) }} remaining)
                            </option>
                        @endforeach
                    </select>
                    @error('budget_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    {{-- Live budget meter --}}
                    <div id="budgetMeter" class="mt-3" style="display:none">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Budget Usage</span>
                            <span id="meterLabel"></span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:6px">
                            <div id="meterBar" class="progress-bar" style="border-radius:6px"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:.75rem">
                            <span class="text-muted">Received: <strong id="meterTotal"></strong></span>
                            <span class="text-muted">Spent: <strong id="meterSpent" class="text-danger"></strong></span>
                            <span class="text-muted">Remaining: <strong id="meterRemaining" class="text-success"></strong></span>
                        </div>
                    </div>
                </div>

                {{-- ── Budget Split Panel (shown when expense > remaining) ─────── --}}
                <div id="splitPanel" style="display:none;background:#fff7ed;border-color:#fed7aa" class="mb-4 p-3 rounded-3 border">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:32px;height:32px;background:#fff7ed;border:1px solid #fb923c;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="bi bi-split" style="color:#ea580c;font-size:.95rem"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#9a3412;font-size:.9rem">Budget Split Required</div>
                            <div class="text-muted" style="font-size:.78rem">This expense exceeds the selected budget — choose where the overflow comes from.</div>
                        </div>
                    </div>

                    {{-- Split breakdown --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background:#f0fdf4;border:1px solid #bbf7d0">
                                <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px">From Primary Budget</div>
                                <div class="fw-bold" style="color:#166534;font-size:1.05rem" id="splitPrimaryAmt">—</div>
                                <div class="text-muted" style="font-size:.72rem" id="splitPrimaryName">—</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background:#fff7ed;border:1px solid #fed7aa">
                                <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px">Overflow Amount</div>
                                <div class="fw-bold" style="color:#9a3412;font-size:1.05rem" id="splitOverflowAmt">—</div>
                                <div class="text-muted" style="font-size:.72rem">needs another budget</div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter secondary budgets --}}
                    <div class="fw-semibold small mb-2" style="color:#7c2d12">Where should the overflow come from?</div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <select id="splitCompanyFilter" class="form-select form-select-sm">
                                <option value="">Filter by company…</option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select id="splitProjectFilter" class="form-select form-select-sm">
                                <option value="">Filter by project…</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <select name="split_budget_id" id="splitBudgetSelect"
                            class="form-select @error('split_budget_id') is-invalid @enderror">
                        <option value="">Select secondary budget…</option>
                    </select>
                    @error('split_budget_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                    {{-- Selected secondary budget meter --}}
                    <div id="splitMeter" class="mt-2" style="display:none">
                        <div class="d-flex justify-content-between" style="font-size:.72rem;color:#64748b">
                            <span>Secondary budget remaining: <strong id="splitMeterRemaining" class="text-success"></strong></span>
                            <span id="splitMeterStatus"></span>
                        </div>
                    </div>

                    {{-- No secondary budgets available — over-budget fallback --}}
                    <div id="overBudgetFallback" style="display:none" class="mt-3 p-3 rounded-3" style="background:#fef2f2;border:1px solid #fecaca">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger mt-1" style="flex-shrink:0"></i>
                            <div>
                                <div class="fw-semibold text-danger" style="font-size:.85rem">No other budgets available</div>
                                <div class="text-muted" style="font-size:.78rem">There are no active budgets with remaining funds to cover the overflow. You can still save this expense and the budget will go over its limit.</div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="allowOverBudget" name="allow_over_budget" value="1" {{ old('allow_over_budget') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-danger" for="allowOverBudget" style="font-size:.82rem">
                                        I understand — save this expense even though it exceeds the budget
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ─────────────────────────────────────────────────────────── --}}

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">What did you buy / pay for? *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="e.g. Google Play Console Annual Fee, Apple Developer Account, Domain Name…" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vendor / Service Provider</label>
                        <input type="text" name="vendor" class="form-control"
                               value="{{ old('vendor') }}"
                               placeholder="e.g. Google, Apple, GoDaddy, AWS…">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text" id="currencySymLabel">SR</span>
                            <input type="number" name="amount" id="amountInput" step="0.01" min="0.01"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}" placeholder="125.00" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Currency</label>
                        <select name="currency" id="currencySelect" class="form-select">
                            @foreach(['SAR','USD','EUR','GBP','AED','EGP','OMR','KWD','QAR','BHD'] as $cur)
                            <option value="{{ $cur }}" {{ old('currency','SAR') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company *</label>
                        <select name="company_id" id="companySelect" class="form-select @error('company_id') is-invalid @enderror" required>
                            <option value="">Select company…</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Related Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">Not project-specific</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} — {{ $p->company?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Related Task</label>
                        <select name="task_id" class="form-select">
                            <option value="">Not task-specific</option>
                            @foreach($tasks as $t)
                                <option value="{{ $t->id }}" {{ old('task_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->title }} ({{ $t->project?->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment section --}}
                    <div class="col-12 mt-2">
                        <div class="fw-semibold small text-muted border-bottom pb-2 mb-3">Payment Details</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Expense Date *</label>
                        <input type="date" name="expense_date"
                               class="form-control @error('expense_date') is-invalid @enderror"
                               value="{{ old('expense_date', now()->toDateString()) }}" required>
                        @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="company_card" {{ old('payment_method','company_card')=='company_card'?'selected':'' }}>Company Card</option>
                            <option value="bank_transfer" {{ old('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                            <option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>Cash</option>
                            <option value="personal_reimbursable" {{ old('payment_method')=='personal_reimbursable'?'selected':'' }}>Personal (to be reimbursed)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="paid" {{ old('status','paid')=='paid'?'selected':'' }}>Paid</option>
                            <option value="pending" {{ old('status')=='pending'?'selected':'' }}>Pending</option>
                            <option value="reimbursed" {{ old('status')=='reimbursed'?'selected':'' }}>Reimbursed</option>
                        </select>
                    </div>

                    {{-- Invoice / Receipt Upload --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Invoice / Receipt File</label>
                        <div class="upload-zone" id="uploadZone"
                             style="border:2px dashed #d1d5db;border-radius:10px;padding:28px;text-align:center;cursor:pointer;transition:.2s">
                            <i class="bi bi-cloud-upload fs-2 text-muted d-block mb-2"></i>
                            <div class="fw-semibold text-muted mb-1">Click or drag &amp; drop your invoice / receipt here</div>
                            <div class="small text-muted">PDF, JPG, PNG — max 10 MB</div>
                            <input type="file" name="invoice_file" id="invoiceFile"
                                   accept=".pdf,.jpg,.jpeg,.png,.gif,.webp"
                                   style="display:none">
                        </div>
                        <div id="filePreview" class="mt-2" style="display:none">
                            <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0">
                                <i class="bi bi-file-earmark-check-fill text-success fs-5"></i>
                                <span id="fileName" class="small fw-semibold text-success flex-grow-1"></span>
                                <button type="button" id="removeFile" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        @error('invoice_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Any additional notes, receipt info, approval reference…">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Expense
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Quick tips --}}
    <div class="col-lg-4">
        <div class="chart-card" style="border-left:4px solid #059669">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill text-success fs-5"></i>
                <span class="fw-semibold">Expense Tips</span>
            </div>
            <ul class="small text-muted ps-3" style="line-height:2.2">
                <li>Upload the <strong>invoice or receipt</strong> as PDF or photo — accounting will need it</li>
                <li>If you paid from your personal card, set method to <strong>"Personal (Reimbursable)"</strong></li>
                <li>Link to the task that <em>required</em> the expense (e.g. "Open Google Play Console")</li>
                <li>The system will warn you if the expense exceeds the remaining budget</li>
                <li>You can print the budget sheet later as a full expense report</li>
            </ul>
        </div>

        @if(isset($selectedBudget))
        <div class="chart-card mt-3">
            <div class="fw-semibold small mb-2">Selected Budget</div>
            <div class="fw-bold">{{ $selectedBudget->title }}</div>
            <div class="text-muted small mb-2">{{ $selectedBudget->company->name }}</div>
            <dl class="row small mb-0">
                <dt class="col-6">Received</dt>
                <dd class="col-6 text-success fw-semibold">{{ fmt_money($selectedBudget->amount, $selectedBudget->currency) }}</dd>
                <dt class="col-6">Spent</dt>
                <dd class="col-6 text-danger fw-semibold">{{ fmt_money($selectedBudget->totalSpent(), $selectedBudget->currency) }}</dd>
                <dt class="col-6">Remaining</dt>
                <dd class="col-6 fw-bold" style="color:#059669">{{ fmt_money($selectedBudget->remaining(), $selectedBudget->currency) }}</dd>
            </dl>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const budgetSelect     = document.getElementById('budgetSelect');
const companySelect    = document.getElementById('companySelect');
const currencySelect   = document.getElementById('currencySelect');
const currencySymLabel = document.getElementById('currencySymLabel');
const meterDiv         = document.getElementById('budgetMeter');
const amountInput      = document.getElementById('amountInput');
const splitPanel       = document.getElementById('splitPanel');
const splitBudgetSel   = document.getElementById('splitBudgetSelect');
const splitCompanyFil  = document.getElementById('splitCompanyFilter');
const splitProjectFil  = document.getElementById('splitProjectFilter');

const symMap = {SAR:'SR',USD:'$',EUR:'€',GBP:'£',AED:'AED',EGP:'EGP',OMR:'OMR',KWD:'KWD',QAR:'QAR',BHD:'BHD'};
function sym(code) { return symMap[code] || code; }
function fmt(n, currency) { return sym(currency || activeCurrency()) + ' ' + parseFloat(n).toFixed(2); }
function activeCurrency() { return currencySelect.value || 'SAR'; }

// All active budgets passed from PHP for the split selector
const allBudgets = @json($budgetsJson);

function primaryData() {
    const opt = budgetSelect.options[budgetSelect.selectedIndex];
    if (!opt || !opt.dataset.amount) return null;
    const total     = parseFloat(opt.dataset.amount);
    const spent     = parseFloat(opt.dataset.spent);
    const remaining = parseFloat((total - spent).toFixed(2));
    return { total, spent, remaining, currency: opt.dataset.currency || 'SAR', company: opt.dataset.company, text: opt.text };
}

function updateMeter() {
    const p = primaryData();
    if (!p) { meterDiv.style.display = 'none'; return; }

    const s        = sym(p.currency);
    const amount   = parseFloat(amountInput.value || 0);
    const newSpent = p.spent + amount;
    const remaining = p.total - newSpent;
    const pct  = Math.min(Math.round(newSpent / p.total * 100), 100);
    const color = pct >= 100 ? '#dc2626' : pct >= 90 ? '#dc2626' : pct >= 70 ? '#d97706' : '#059669';

    meterDiv.style.display = 'block';
    document.getElementById('meterBar').style.width = pct + '%';
    document.getElementById('meterBar').style.background = color;
    document.getElementById('meterLabel').textContent = pct + '% used';
    document.getElementById('meterTotal').textContent    = s + ' ' + p.total.toFixed(2);
    document.getElementById('meterSpent').textContent    = s + ' ' + newSpent.toFixed(2);
    document.getElementById('meterRemaining').textContent = s + ' ' + remaining.toFixed(2);
    document.getElementById('meterRemaining').style.color = remaining < 0 ? '#dc2626' : '#059669';

    if (p.currency) currencySelect.value = p.currency;
    currencySymLabel.textContent = sym(currencySelect.value);
    if (p.company) companySelect.value = p.company;

    updateSplitPanel(p, amount);
}

function updateSplitPanel(p, amount) {
    const overflow = parseFloat((amount - p.remaining).toFixed(2));
    // Only show split panel when budget had remaining balance and this expense exceeds it.
    // If budget is already at or below 0, just let the expense pile on — it tracks what the company owes.
    if (overflow > 0 && p.remaining > 0 && amount > 0) {
        splitPanel.style.display = 'block';
        document.getElementById('splitPrimaryAmt').textContent  = fmt(p.remaining, p.currency);
        document.getElementById('splitPrimaryName').textContent = budgetSelect.options[budgetSelect.selectedIndex]?.text?.split(' (')[0] || '—';
        document.getElementById('splitOverflowAmt').textContent = fmt(overflow, p.currency);
        renderSplitBudgets(p.currency);
    } else {
        splitPanel.style.display = 'none';
        splitBudgetSel.value = '';
    }
}

function renderSplitBudgets() {
    const primaryId  = parseInt(budgetSelect.value);
    const filterCo   = splitCompanyFil.value;
    const filterPr   = splitProjectFil.value;
    const fallbackEl = document.getElementById('overBudgetFallback');

    const filtered = allBudgets.filter(b => {
        if (b.id === primaryId)                    return false;
        if (b.remaining <= 0)                      return false;
        if (filterCo && b.company_id != filterCo)  return false;
        if (filterPr && b.project_id != filterPr)  return false;
        return true;
    });

    const prev = splitBudgetSel.value;
    splitBudgetSel.innerHTML = '<option value="">Select secondary budget…</option>';
    filtered.forEach(b => {
        const o  = document.createElement('option');
        o.value  = b.id;
        o.textContent = b.title + ' — ' + b.company_name + ' (' + sym(b.currency) + ' ' + b.remaining.toFixed(2) + ' remaining)';
        o.dataset.remaining = b.remaining;
        o.dataset.currency  = b.currency;
        splitBudgetSel.appendChild(o);
    });

    if (prev) splitBudgetSel.value = prev;

    // Show/hide the no-budgets fallback
    const hasOptions = filtered.length > 0;
    splitBudgetSel.style.display  = hasOptions ? '' : 'none';
    splitCompanyFil.parentElement.parentElement.style.display = hasOptions ? '' : 'none';
    fallbackEl.style.display = hasOptions ? 'none' : 'block';

    // If fallback is shown, clear the split selection so controller skips split logic
    if (!hasOptions) {
        splitBudgetSel.value = '';
        document.getElementById('splitMeter').style.display = 'none';
    } else {
        updateSplitMeter();
        // Uncheck over-budget if budgets are now available
        const cb = document.getElementById('allowOverBudget');
        if (cb) cb.checked = false;
    }
}

function updateSplitMeter() {
    const opt = splitBudgetSel.options[splitBudgetSel.selectedIndex];
    const meter = document.getElementById('splitMeter');
    if (!opt || !opt.dataset.remaining) { meter.style.display = 'none'; return; }

    const p        = primaryData();
    const amount   = parseFloat(amountInput.value || 0);
    const overflow = p ? parseFloat((amount - p.remaining).toFixed(2)) : 0;
    const secRemaining = parseFloat(opt.dataset.remaining);
    const enough   = secRemaining >= overflow;

    meter.style.display = 'block';
    document.getElementById('splitMeterRemaining').textContent = fmt(secRemaining, opt.dataset.currency);
    document.getElementById('splitMeterRemaining').style.color = enough ? '#166534' : '#dc2626';
    document.getElementById('splitMeterStatus').textContent    = enough
        ? '✓ Sufficient for overflow'
        : '✗ Not enough — needs ' + fmt(overflow, opt.dataset.currency);
    document.getElementById('splitMeterStatus').style.color = enough ? '#166534' : '#dc2626';
}

currencySelect.addEventListener('change', () => { currencySymLabel.textContent = sym(currencySelect.value); });
budgetSelect.addEventListener('change', updateMeter);
amountInput.addEventListener('input', updateMeter);
splitCompanyFil.addEventListener('change', renderSplitBudgets);
splitProjectFil.addEventListener('change', renderSplitBudgets);
splitBudgetSel.addEventListener('change', updateSplitMeter);

currencySymLabel.textContent = sym(currencySelect.value);
updateMeter();

// ── File upload zone ──────────────────────────────────────────────────────
const zone       = document.getElementById('uploadZone');
const fileInput  = document.getElementById('invoiceFile');
const preview    = document.getElementById('filePreview');
const fileNameEl = document.getElementById('fileName');

zone.addEventListener('click', () => fileInput.click());
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='#4f46e5'; zone.style.background='#eef2ff'; });
zone.addEventListener('dragleave', () => { zone.style.borderColor='#d1d5db'; zone.style.background=''; });
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.style.borderColor='#d1d5db'; zone.style.background='';
    if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; showFile(e.dataTransfer.files[0]); }
});
fileInput.addEventListener('change', () => { if (fileInput.files.length) showFile(fileInput.files[0]); });

function showFile(f) {
    fileNameEl.textContent = f.name + ' (' + (f.size/1024).toFixed(0) + ' KB)';
    preview.style.display = 'block';
    zone.style.display    = 'none';
}

document.getElementById('removeFile').addEventListener('click', () => {
    fileInput.value       = '';
    preview.style.display = 'none';
    zone.style.display    = 'block';
});
</script>
@endpush
