@extends('layouts.app')
@section('title','View Request')

@section('content')

    <x-page-header
        title="Request Details"
        subtitle="View certificate request information"
    >
        <x-slot:action>
            <a href="{{ route('certificates_request.index') }}" class="btn btn-light">
                Back
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="row g-4">

        <div class="col-lg-8">
            <x-card>

                <h6 class="mb-3">Request Information</h6>

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="form-label">Resident</label>
                        <div class="form-control bg-light">
                            {{ $certificate->resident
                                ? $certificate->resident->FirstName.' '.$certificate->resident->LastName
                                : '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Document Type</label>
                        <div class="form-control bg-light">
                            {{ $certificate->certificateType->name ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Control No</label>
                        <div class="form-control bg-light fw-bold fs-5 d-flex justify-content-between align-items-center">
                            <span>{{ $certificate->ControlNo ?? '-' }}</span>

                            @if($certificate->ControlNo)
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="navigator.clipboard.writeText('{{ $certificate->ControlNo }}')">
                                    Copy
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Purpose</label>
                        <div class="form-control bg-light" style="min-height:80px;">
                            {{ $certificate->purpose }}
                        </div>
                    </div>

                </div>

                <hr>

                <h6 class="mb-3">Cashiering</h6>

                @if(!$certificate->certificateRecord)

                    @if($certificate->remark === 'Approved')

                        <form id="issueForm" class="row g-3">

                            <input type="hidden" name="request_id" value="{{ encrypt($certificate->id) }}">

                            <div class="col-12">
                                <label class="form-label">OR Number</label>
                                <input type="text" name="or_number" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Fee</label>
                                <input type="number"
                                       id="fee"
                                       name="fee"
                                       class="form-control fw-bold fs-5"
                                       value="{{ $certificate->certificateType->fee }}"
                                       readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Amount Tendered</label>
                                <input type="number"
                                       step="0.01"
                                       id="amount_paid"
                                       name="amount_paid"
                                       class="form-control fs-5"
                                       required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Change</label>
                                <input type="text"
                                       id="change"
                                       class="form-control fw-bold fs-5 text-success"
                                       readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="Cash" selected>Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>

                            <div class="col-12 d-grid">
                                <button type="submit" class="btn btn-success btn-lg btn-issue">
                                    Confirm Payment & Enable Printing
                                </button>
                            </div>

                        </form>

                    @else
                        <div class="alert alert-warning">
                            Request must be approved before cashiering.
                        </div>
                    @endif

                @else

                    <div class="alert alert-success mb-3">
                        Payment completed
                    </div>

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label">OR Number</label>
                            <div class="form-control bg-light">
                                {{ $certificate->certificateRecord->or_number }}
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label">Paid</label>
                            <div class="form-control bg-light">
                                ₱ {{ number_format($certificate->certificateRecord->amount_paid,2) }}
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label">Fee</label>
                            <div class="form-control bg-light">
                                ₱ {{ number_format($certificate->certificateRecord->Fee,2) }}
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Change</label>
                            <div class="form-control bg-light text-success fw-bold">
                                ₱ {{ number_format($certificate->certificateRecord->amount_paid - $certificate->certificateRecord->Fee,2) }}
                            </div>
                        </div>

                    </div>

                @endif

            </x-card>
        </div>

        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">

                <x-card class="position-sticky" style="top:20px;">
                    <h6 class="mb-3">Actions</h6>

                    <div class="d-grid gap-2">

                        @if($certificate->remark === 'Pending')
                            <button class="btn btn-success btn-approve"
                                    data-id="{{ encrypt($certificate->id) }}">
                                Approve Request
                            </button>
                        @endif

                        @if($certificate->remark === 'Approved' && $certificate->certificateRecord)
                            <a target="_blank" href="{{ route('certificate-types.print',$certificate->ControlNo) }}"
                               class="btn btn-dark">
                                Print Certificate
                            </a>
                        @endif

                    </div>

                </x-card>

                <x-card>
                    <h6 class="mb-3">Status</h6>

                    @php
                        $color = match($certificate->remark){
                            'Approved' => 'success',
                            'Rejected' => 'danger',
                            default => 'warning'
                        };
                    @endphp

                    <div class="mb-3">
                    <span class="badge bg-{{ $color }} fs-6 px-3 py-2">
                        {{ $certificate->remark }}
                    </span>
                    </div>

                    @if($certificate->ControlNo)
                        <div class="text-center mb-3">
                            {!! QrCode::size(140)->generate(route('cert.verify',$certificate->ControlNo)) !!}
                            <small class="text-muted d-block mt-2">{{ $certificate->ControlNo }}</small>
                        </div>
                    @endif

                    <div class="mb-2">
                        <small class="text-muted d-block">Requested At</small>
                        <div>
                            {{ $certificate->requested_at?->format('M d, Y h:i A') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <small class="text-muted d-block">Created At</small>
                        <div>
                            {{ $certificate->created_at?->format('M d, Y h:i A') ?? '-' }}
                        </div>
                    </div>

                </x-card>

            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', ()=>{

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content

            const request = async (url, id, btn)=>{
                try{
                    btn.disabled = true
                    const original = btn.innerText
                    btn.innerText = 'Processing...'

                    const res = await fetch(url,{
                        method:'POST',
                        headers:{
                            'X-CSRF-TOKEN': csrf,
                            'Content-Type':'application/json'
                        },
                        body: JSON.stringify({id})
                    })

                    if(!res.ok) throw new Error()

                    location.reload()
                }catch(e){
                    alert('Something went wrong')
                    btn.disabled = false
                    btn.innerText = original
                }
            }

            document.querySelectorAll('.btn-approve').forEach(btn=>{
                btn.addEventListener('click', async ()=>{

                    const ok = await iosConfirm({
                        title: 'Approve Request',
                        message: 'Are you sure you want to approve this request?'
                    })

                    if(!ok) return

                    request('/certificates_request/approve', btn.dataset.id, btn)
                })
            })

            const fee = document.getElementById('fee')
            const paid = document.getElementById('amount_paid')
            const change = document.getElementById('change')

            const computeChange = ()=>{
                const f = Number(fee?.value || 0)
                const p = Number(paid?.value || 0)
                const c = p - f

                change.value = c >= 0
                    ? '₱ ' + c.toFixed(2)
                    : 'Insufficient'
            }

            paid?.addEventListener('input', computeChange)

            const form = document.getElementById('issueForm')

            form?.addEventListener('submit', async function(e){
                e.preventDefault()

                const btn = document.querySelector('.btn-issue')
                const original = btn.innerText

                const ok = await iosConfirm({
                    title: 'Confirm Payment',
                    message: 'Proceed with this payment and enable printing?'
                })

                if(!ok) return

                try{
                    btn.disabled = true
                    btn.innerText = 'Saving...'

                    const res = await fetch('/certificates_request/issue',{
                        method:'POST',
                        headers:{
                            'X-CSRF-TOKEN': csrf
                        },
                        body: new FormData(this)
                    })

                    if(!res.ok) throw new Error()

                    location.reload()
                }catch(e){
                    alert('Failed to save payment')
                    btn.disabled = false
                    btn.innerText = original
                }
            })

        })
    </script>
@endpush
