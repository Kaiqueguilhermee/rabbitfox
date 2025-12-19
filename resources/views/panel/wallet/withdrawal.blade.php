@extends('layouts.web')

@push('styles')

@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Fallback tab handler that forces pane visibility (works without Bootstrap JS)
            var tabsRoot = document.getElementById('walletTabs');
            var container = document.querySelector('.wallet-tabs .tab-content');
            if(!tabsRoot || !container) return;

            var panes = Array.from(container.querySelectorAll('.tab-pane'));

            function hideAllPanes(){
                panes.forEach(function(p){
                    p.classList.remove('show','active');
                    p.setAttribute('aria-hidden','true');
                    p.style.display = 'none';
                });
            }

            // initialize: hide all then show the one marked active (if any)
            hideAllPanes();
            var initial = container.querySelector('.tab-pane.active') || container.querySelector('.tab-pane.show') || panes[0];
            if(initial){ initial.classList.add('show','active'); initial.setAttribute('aria-hidden','false'); initial.style.display = ''; }

            tabsRoot.querySelectorAll('.nav-link').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var targetSelector = btn.getAttribute('data-bs-target') || btn.getAttribute('href');
                    if(!targetSelector) return;

                    // Deactivate other buttons
                    tabsRoot.querySelectorAll('.nav-link').forEach(function(b){ b.classList.remove('active'); b.setAttribute('aria-selected','false'); });
                    btn.classList.add('active'); btn.setAttribute('aria-selected','true');

                    // Hide other panes and show the requested one
                    hideAllPanes();
                    var target = document.querySelector(targetSelector);
                    if(target){
                        target.classList.add('show','active');
                        target.setAttribute('aria-hidden','false');
                        target.style.display = '';
                    }
                    // scroll into view on small screens
                    if(window.innerWidth < 768 && target) target.scrollIntoView({behavior: 'smooth', block: 'start'});
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        @include('includes.navbar_top')
        @include('includes.navbar_left')

        <div class="page__content">
            <br>

            <div class="wallet-tabs">
                <ul class="nav nav-tabs mb-3" id="walletTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-saldo" data-bs-toggle="tab" data-bs-target="#tabContent-saldo" type="button" role="tab" aria-controls="tabContent-saldo" aria-selected="true">Saldo</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-transacoes" data-bs-toggle="tab" data-bs-target="#tabContent-transacoes" type="button" role="tab" aria-controls="tabContent-transacoes" aria-selected="false">Transações</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-resgatar" data-bs-toggle="tab" data-bs-target="#tabContent-resgatar" type="button" role="tab" aria-controls="tabContent-resgatar" aria-selected="false">Resgatar Bônus</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabContent-saldo" role="tabpanel" aria-labelledby="tab-saldo">
                        @include('includes.wallet_card')
                    </div>

                    <div class="tab-pane fade" id="tabContent-transacoes" role="tabpanel" aria-labelledby="tab-transacoes">
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

                    <div class="tab-pane fade" id="tabContent-resgatar" role="tabpanel" aria-labelledby="tab-resgatar">
                        <div class="card">
                            <div class="card-body p-4 text-center text-muted">
                                <h5 class="mb-2">Resgate de Bônus</h5>
                                <p class="mb-3">Funcionalidade em breve — aqui você poderá resgatar bônus disponíveis.</p>
                                <button class="btn btn-outline-secondary" disabled>Em breve</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')

@endpush
