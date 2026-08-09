@extends('layouts.guest')

@section('content')

<div class="row justify-content-center">

    <div class="col-12 col-lg-10 col-xl-8">

        <div class="text-center mt-5 mb-4">

            <h1 class="h2">
                Cadastro
            </h1>

            <p class="lead mb-0">
                Cadastre-se para começar a realizar entregas.
            </p>

        </div>


        <div class="card">

            <div class="card-body">

                <div class="m-sm-3">

                    <form
                        method="POST"
                        action="{{ route('registro') }}"
                    >

                        @csrf


                        <div class="row">


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Nome Completo
                                </label>

                                <input
                                    class="form-control form-control-lg"
                                    type="text"
                                    name="nome"
                                    value="{{ old('nome') }}"
                                    placeholder="Insira seu nome"
                                    required
                                >

                                @error('nome')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input
                                    class="form-control form-control-lg"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Insira seu email"
                                    required
                                >

                                @error('email')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Telefone
                                </label>

                                <input
                                    class="form-control form-control-lg"
                                    type="tel"
                                    name="telefone"
                                    value="{{ old('telefone') }}"
                                    placeholder="Insira seu telefone"
                                    required
                                >

                                @error('telefone')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tipo de conta
                                </label>

                                <select
                                    class="form-select form-select-lg"
                                    name="tipo"
                                    id="tipoConta"
                                    required
                                >

                                    <option value="">
                                        Selecione um perfil
                                    </option>

                                    <option
                                        value="empresa"
                                        {{ old('tipo', $tipo) === 'empresa' ? 'selected' : '' }}
                                    >
                                        Empresa
                                    </option>

                                    <option
                                        value="entregador"
                                        {{ old('tipo', $tipo) === 'entregador' ? 'selected' : '' }}
                                    >
                                        Entregador
                                    </option>

                                </select>

                                @error('tipo')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        <div
                            id="camposEmpresa"
                            style="display: none;"
                        >

                            <hr class="my-4">

                            <h5 class="mb-3">
                                Dados da empresa
                            </h5>


                            <div class="row">

                                {{-- CNPJ --}}

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        CNPJ
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="cnpj"
                                        value="{{ old('cnpj') }}"
                                        placeholder="Insira o CNPJ"
                                    >

                                    @error('cnpj')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>


                            <h5 class="mb-3 mt-3">
                                Endereço da empresa
                            </h5>


                            <div class="row">

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        CEP
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="cep"
                                        value="{{ old('cep') }}"
                                        placeholder="Insira o CEP"
                                    >

                                    @error('cep')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>



                                <div class="col-md-8 mb-3">

                                    <label class="form-label">
                                        Logradouro
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="logradouro"
                                        value="{{ old('logradouro') }}"
                                        placeholder="Rua, avenida..."
                                    >

                                    @error('logradouro')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>



                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Número
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="numero"
                                        value="{{ old('numero') }}"
                                        placeholder="Número"
                                    >

                                    @error('numero')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>



                                <div class="col-md-8 mb-3">

                                    <label class="form-label">
                                        Bairro
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="bairro"
                                        value="{{ old('bairro') }}"
                                        placeholder="Insira o bairro"
                                    >

                                    @error('bairro')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                <div class="col-md-8 mb-3">

                                    <label class="form-label">
                                        Cidade
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="cidade"
                                        value="{{ old('cidade') }}"
                                        placeholder="Insira a cidade"
                                    >

                                    @error('cidade')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Estado
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="estado"
                                        value="{{ old('estado') }}"
                                        placeholder="UF"
                                        maxlength="2"
                                    >

                                    @error('estado')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                <div class="col-12 mb-3">

                                    <label class="form-label">
                                        Complemento
                                        <span class="text-muted">
                                            (opcional)
                                        </span>
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="complemento"
                                        value="{{ old('complemento') }}"
                                        placeholder="Apartamento, sala, referência..."
                                    >

                                    @error('complemento')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                        </div>


                        <div
                            id="camposEntregador"
                            style="display: none;"
                        >

                            <hr class="my-4">

                            <h5 class="mb-3">
                                Dados do entregador
                            </h5>


                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        CPF
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="cpf"
                                        value="{{ old('cpf') }}"
                                        placeholder="Insira o CPF"
                                    >

                                    @error('cpf')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Placa do veículo
                                    </label>

                                    <input
                                        class="form-control form-control-lg"
                                        type="text"
                                        name="placa"
                                        value="{{ old('placa') }}"
                                        placeholder="Insira a placa"
                                    >

                                    @error('placa')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Tipo de veículo
                                    </label>

                                    <select
                                        class="form-select form-select-lg"
                                        name="tipo_veiculo"
                                    >

                                        <option value="">
                                            Selecione o veículo
                                        </option>

                                        <option
                                            value="carro"
                                            {{ old('tipo_veiculo') === 'carro' ? 'selected' : '' }}
                                        >
                                            Carro
                                        </option>

                                        <option
                                            value="moto"
                                            {{ old('tipo_veiculo') === 'moto' ? 'selected' : '' }}
                                        >
                                            Moto
                                        </option>

                                        <option
                                            value="bike"
                                            {{ old('tipo_veiculo') === 'bike' ? 'selected' : '' }}
                                        >
                                            Bike
                                        </option>

                                        <option
                                            value="van"
                                            {{ old('tipo_veiculo') === 'van' ? 'selected' : '' }}
                                        >
                                            Van
                                        </option>

                                        <option
                                            value="caminhao"
                                            {{ old('tipo_veiculo') === 'caminhao' ? 'selected' : '' }}
                                        >
                                            Caminhão
                                        </option>

                                        <option
                                            value="outro"
                                            {{ old('tipo_veiculo') === 'outro' ? 'selected' : '' }}
                                        >
                                            Outro
                                        </option>

                                    </select>

                                    @error('tipo_veiculo')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Senha
                                </label>

                                <input
                                    class="form-control form-control-lg"
                                    type="password"
                                    name="senha"
                                    placeholder="Insira sua senha"
                                    required
                                >

                                @error('senha')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Confirmar senha
                                </label>

                                <input
                                    class="form-control form-control-lg"
                                    type="password"
                                    name="senha_confirmation"
                                    placeholder="Confirme sua senha"
                                    required
                                >

                            </div>

                        </div>

                        <div class="d-grid gap-2 mt-4">

                            <button
                                type="submit"
                                class="btn btn-lg btn-primary"
                            >
                                Cadastrar
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <div class="text-center mt-3 mb-4">

            Já tem uma conta?

            <a href="{{ route('login') }}">
                Entrar
            </a>

        </div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const tipoConta = document.getElementById('tipoConta');

    const camposEmpresa =
        document.getElementById('camposEmpresa');

    const camposEntregador =
        document.getElementById('camposEntregador');


    function atualizarCampos() {

        camposEmpresa.style.display = 'none';

        camposEntregador.style.display = 'none';


        if (tipoConta.value === 'empresa') {
            camposEmpresa.style.display = 'block';
        }


        if (tipoConta.value === 'entregador') {
            camposEntregador.style.display = 'block';
        }

    }


    tipoConta.addEventListener(
        'change',
        atualizarCampos
    );


    atualizarCampos();

});

</script>

@endsection