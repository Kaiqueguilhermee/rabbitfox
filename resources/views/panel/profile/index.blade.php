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
                                @php
                                    $avatar = auth()->user()->avatar ?? null;
                                    $avatarUrl = $avatar ? \Storage::url($avatar) : 'https://cdn.7games.bet/content/images/avatars/v2/7.webp';
                                @endphp
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle" width="120" height="120">
                            </div>
                            <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                            <p class="text-muted small mb-3">Membro desde {{ auth()->user()->created_at->format('M Y') }}</p>

                            <div class="d-grid gap-2">
                                <form id="avatarUploadForm" enctype="multipart/form-data">
                                    @csrf
                                    <label class="btn btn-sm btn-outline-primary mb-2" for="avatarInput">Alterar avatar</label>
                                    <input id="avatarInput" name="avatar" type="file" accept="image/*" class="d-none">
                                </form>
                                <button id="openEditTab" type="button" class="btn btn-outline-secondary btn-sm">Alterar dados</button>
                            </div>
                            <div class="mt-2 small text-muted">Formato permitido: JPG/PNG. Máx 2MB.</div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card rounded-3 shadow-sm">
                            <div class="card-body p-0">
                                <ul class="nav nav-tabs px-3 pt-3" id="profileTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="profile-tab-info" data-bs-target="#profilePane-info" type="button" role="tab" aria-controls="profilePane-info" aria-selected="true">Informações</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="profile-tab-edit" data-bs-target="#profilePane-edit" type="button" role="tab" aria-controls="profilePane-edit" aria-selected="false">Editar</button>
                                    </li>
                                </ul>

                                <div class="tab-content p-4">
                                    <div class="tab-pane fade show active" id="profilePane-info" role="tabpanel" aria-labelledby="profile-tab-info">
                                        <h5 class="mb-3">Informações</h5>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="text-muted small">Nome</div>
                                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-muted small">E-mail</div>
                                                <div class="fw-semibold">{{ auth()->user()->email }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-muted small">Telefone</div>
                                                <div class="fw-semibold">{{ auth()->user()->phone }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-muted small">CPF</div>
                                                <div class="fw-semibold">{{ auth()->user()->cpf }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="profilePane-edit" role="tabpanel" aria-labelledby="profile-tab-edit">
                                        <h5 class="mb-3">Editar perfil</h5>
                                        <div class="card-body p-0">
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
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var input = document.getElementById('avatarInput');
            var form = document.getElementById('avatarUploadForm');
            var avatarImg = document.querySelector('.profile-avatar img');
            var avatarPlaceholder = document.querySelector('.avatar-placeholder');

            if(!input || !form) return;

            input.addEventListener('change', function(){
                var file = input.files[0];
                if(!file) return;
                var formData = new FormData();
                formData.append('avatar', file);

                var token = document.querySelector('meta[name="csrf-token"]');

                fetch("{{ route('panel.profile.avatar.upload') }}", {
                    method: 'POST',
                    headers: token ? {'X-CSRF-TOKEN': token.getAttribute('content')} : {},
                    body: formData
                }).then(function(res){ return res.json(); })
                .then(function(json){
                    if(json.status && json.avatar_url){
                        // update preview
                        if(avatarImg){ avatarImg.src = json.avatar_url; avatarImg.style.display = ''; }
                        if(avatarPlaceholder) avatarPlaceholder.style.display = 'none';
                    } else {
                        alert('Erro ao enviar avatar');
                    }
                }).catch(function(){ alert('Erro de rede ao enviar avatar'); });
            });

            // click handler for the label button
            var label = document.querySelector('label[for="avatarInput"]');
            if(label){ label.addEventListener('click', function(){ input.click(); }); }
            // profile tabs handler
            function setupProfileTabs(){
                var tabsRoot = document.getElementById('profileTabs');
                if(!tabsRoot) return;
                var container = document.querySelector('.tab-content');
                var panes = container ? Array.from(container.querySelectorAll('.tab-pane')) : [];

                function hideAll(){ panes.forEach(p => { p.classList.remove('show','active'); p.style.display='none'; p.setAttribute('aria-hidden','true'); }); }

                hideAll();
                var first = panes[0];
                if(first){ first.classList.add('show','active'); first.style.display=''; first.setAttribute('aria-hidden','false'); }

                tabsRoot.querySelectorAll('.nav-link').forEach(function(btn){
                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        var target = document.querySelector(btn.getAttribute('data-bs-target'));
                        if(!target) return;
                        tabsRoot.querySelectorAll('.nav-link').forEach(b=>b.classList.remove('active'));
                        btn.classList.add('active');
                        hideAll();
                        target.classList.add('show','active'); target.style.display=''; target.setAttribute('aria-hidden','false');
                    });
                });

                // openEditTab button
                var openEdit = document.getElementById('openEditTab');
                if(openEdit){ openEdit.addEventListener('click', function(){ var editBtn = document.getElementById('profile-tab-edit'); if(editBtn) editBtn.click(); }); }
            }

            setupProfileTabs();
        });
    </script>
@endpush
