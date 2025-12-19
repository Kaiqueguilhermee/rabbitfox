<div class="accordion" id="accordionPanelsStayOpenExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                Qual é o valor mínimo para depósito?
            </button>
        </h2>
        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingOne">
            <div class="accordion-body">
                Na nossa plataforma, o valor mínimo de depósito é de <strong>R$ {{ number_format(config('setting')['min_deposit'], 2, ',', '.') }}</strong>.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                Qual é o valor mínimo para saque?
            </button>
        </h2>
        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
            <div class="accordion-body">
                Na nossa plataforma, o valor mínimo de saque é de <strong>R$ {{ number_format(config('setting')['min_withdrawal'], 2, ',', '.') }}</strong>, e o valor máximo é de <strong>R$ {{ number_format(config('setting')['max_withdrawal'], 2, ',', '.') }}</strong>.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                Quais são as regras do programa de CPA (Custo por Aquisição)?
            </button>
        </h2>
        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
            <div class="accordion-body">
                <p>No nosso programa de CPA (Custo por Aquisição), oferecemos comissões para afiliados que indicam novos jogadores para nossa plataforma.</p>
                <br>
                <p><i class="fa-solid fa-check mr-2"></i> As comissões variam de acordo com o perfil do afiliado e são configuradas individualmente.</p>
                <p><i class="fa-solid fa-check mr-2"></i> Para participar do programa de afiliados, entre em contato com nosso suporte através do painel.</p>
            </div>
        </div>
    </div>
</div>
