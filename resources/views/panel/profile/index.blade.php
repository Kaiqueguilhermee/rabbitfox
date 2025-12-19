@extends('layouts.web')

@push('styles')

@endpush

@section('content')
    <div class="container-fluid">
        @include('includes.navbar_top')
        @include('includes.navbar_left')

        <div class="page__content">

            <div class="profile-wrap mt-5">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="profile-card p-4 rounded-3 shadow-sm text-center">
                            <div class="profile-avatar mb-3">
                                @php $avatar = auth()->user()->avatar ?? null; @endphp
                                @if($avatar)
                                    <img src="{{ asset('storage/'.$avatar) }}" alt="Avatar" class="rounded-circle" width="120" height="120">
                                @else
                                    <div class="avatar-placeholder rounded-circle d-inline-flex align-items-center justify-content-center">{{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}</div>
                                @endif
                            </div>
                            <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                            <p class="text-muted small mb-3">Membro desde {{ auth()->user()->created_at->format('M Y') }}</p>

                            <div class="d-grid gap-2">
                                <a href="{{ route('panel.profile.editAvatar') }}" class="btn btn-outline-secondary btn-sm">Alterar avatar</a>
                                <a href="{{ route('panel.profile.security') }}" class="btn btn-outline-secondary btn-sm">Segurança da conta</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card rounded-3 shadow-sm">
                            <div class="card-body p-4">
                                <h4 class="mb-3">Minha Conta</h4>
                                <form action="{{ route('panel.profile.store') }}" method="post">
                                    @method('post')
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small text-muted">Nome completo</label>
                                            <input type="text" name="name" placeholder="Nome Completo" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ auth()->user()->name ?? old('name') }}">
                                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Telefone</label>
                                            <input type="text" name="phone" placeholder="(00) 00000-0000" class="form-control sp_celphones @error('phone') is-invalid @enderror" value="{{ auth()->user()->phone ?? old('phone') }}">
                                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">CPF</label>
                                            <input type="text" name="cpf" placeholder="000.000.000-00" class="form-control cpf @error('cpf') is-invalid @enderror" value="{{ auth()->user()->cpf ?? old('cpf') }}">
                                            @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label small text-muted">E-mail</label>
                                            <input type="email" name="email" readonly class="form-control" value="{{ auth()->user()->email ?? old('email') }}">
                                        </div>

                                        <div class="col-12 mt-2">
                                            <hr>
                                            <h6 class="mb-2">Alterar senha</h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Senha atual</label>
                                            <input type="password" name="old_password" placeholder="Senha antiga" class="form-control @error('old_password') is-invalid @enderror">
                                            @error('old_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Nova senha</label>
                                            <input type="password" name="new_password" placeholder="Nova senha" class="form-control @error('new_password') is-invalid @enderror">
                                            @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-success btn-lg">Salvar alterações</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('/assets/js/jquery.mask.min.js') }}"></script>
    <script>
        $('.cpf').mask('000.000.000-00', {reverse: true});

        var SPMaskBehavior = function (val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            },
            spOptions = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            };

        $('.sp_celphones').mask(SPMaskBehavior, spOptions);

    </script>
@endpush
