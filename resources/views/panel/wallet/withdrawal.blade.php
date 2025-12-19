@extends('layouts.web')

@push('styles')

@endpush

@section('content')
    <div class="container-fluid">
        @include('includes.navbar_top')
        @include('includes.navbar_left')

        <div class="page__content">
            <br>

            @include('includes.wallet_card')

            <div class="wallet-transactions mt-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Histórico de Saques</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="tx-list">
                            @if(count($withdrawals))
                                @foreach($withdrawals as $withdrawal)
                                    <div class="tx-item d-flex align-items-center justify-content-between">
                                        <div class="tx-left d-flex align-items-start">
                                            <div class="tx-id me-3 text-muted small">#{{ $withdrawal->id }}</div>
                                            <div>
                                                <div class="tx-type fw-semibold">{{ $withdrawal->type }}</div>
                                                <div class="tx-meta small text-muted">
                                                    <span>
                                                        @if(!empty($withdrawal->proof))
                                                            <a href="{{ url('storage/'. $withdrawal->proof) }}" download class="text-muted">Comprovante</a>
                                                        @endif
                                                    </span>
                                                    <span class="mx-2">•</span>
                                                    <span>{{ $withdrawal->dateHumanReadable }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tx-right text-end">
                                            <div class="tx-amount fw-bold">{{ \Helper::amountFormatDecimal($withdrawal->amount) }}</div>
                                            <div class="tx-status small mt-1">
                                                @if($withdrawal->status == 0)
                                                    <span class="badge bg-transparent tx-badge-pending">Pendente</span>
                                                @else
                                                    <span class="badge bg-transparent tx-badge-success">Confirmado</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="p-3">
                                    {{ $withdrawals->links() }}
                                </div>
                            @else
                                <div class="p-4 text-center text-muted small">Nenhum saque encontrado.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')

@endpush
