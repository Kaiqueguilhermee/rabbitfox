@extends('layouts.web')

@push('styles')

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
                        <button class="nav-link active" id="wallet-tab-saldo" data-bs-target="#walletPane-saldo" type="button" role="tab" aria-controls="walletPane-saldo" aria-selected="true">Saldo</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="wallet-tab-transacoes" data-bs-target="#walletPane-transacoes" type="button" role="tab" aria-controls="walletPane-transacoes" aria-selected="false">Transações</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="walletPane-saldo" role="tabpanel" aria-labelledby="wallet-tab-saldo">
                        @include('includes.wallet_card')
                    </div>

                    <div class="tab-pane fade" id="walletPane-transacoes" role="tabpanel" aria-labelledby="wallet-tab-transacoes">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Histórico de Transações</h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Jogo</th>
                                                <th scope="col">Tipo</th>
                                                <th scope="col">Valor</th>
                                                <th scope="col">Data</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($orders))
                                                @foreach($orders as $order)
                                                    <tr>
                                                        <th scope="row">{{ $order->id }}</th>
                                                        <td>{{ $order->game }}</td>
                                                        <td>{{ $order->type }}</td>
                                                        <td>{{ \Helper::amountFormatDecimal($order->amount) }}</td>
                                                        <td>{{ $order->dateHumanReadable }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="flex items-center justify-center text-center py-4" colspan="5">
                                                        <h4 class=" mb-0">NENHUMA INFORMAÇÃO A EXIBIR</h4>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-5">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div style="padding: 0 20px;">
                                                {{ $orders->links() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var tabsContainer = document.getElementById('walletTabs');
    if(!tabsContainer) return;
    var tabButtons = tabsContainer.querySelectorAll('[data-bs-target]');

    tabButtons.forEach(function(btn){
        btn.addEventListener('click', function(e){
            var targetSelector = btn.getAttribute('data-bs-target');
            if(!targetSelector) return;

            // If Bootstrap's tab behavior is present AND this button is annotated with data-bs-toggle="tab",
            // let Bootstrap handle it. Otherwise run our fallback.
            var bootstrapHandles = (typeof bootstrap !== 'undefined' && bootstrap.Tab);
            var hasToggle = btn.getAttribute('data-bs-toggle') === 'tab';
            if(bootstrapHandles && hasToggle) return;

            // deactivate nav links
            tabsContainer.querySelectorAll('.nav-link').forEach(function(n){
                n.classList.remove('active');
                n.setAttribute('aria-selected','false');
            });

            // hide panes
            var panes = document.querySelectorAll('.tab-content .tab-pane');
            panes.forEach(function(p){ p.classList.remove('show','active'); });

            // activate clicked
            btn.classList.add('active');
            btn.setAttribute('aria-selected','true');

            var pane = document.querySelector(targetSelector);
            if(pane){
                pane.classList.add('show','active');
                var firstFocusable = pane.querySelector('a,button,input,select,textarea');
                if(firstFocusable) firstFocusable.focus({preventScroll:true});
            }
        });
    });
});
</script>
@endpush
