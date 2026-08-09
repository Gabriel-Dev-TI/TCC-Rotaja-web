@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Histórico</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover my-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="d-none d-xl-table-cell">Data</th>
                    <th class="d-none d-md-table-cell">Empresa</th>
                    <th>Status</th>
                    <th class="d-none d-xl-table-cell">Entregador</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Project Apollo</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa X</td>
                    <td>
                        <span class="badge bg-success">Finalizado</span>
                    </td>
                    <td class="d-none d-xl-table-cell">Vanessa Tucker</td>
                </tr>

                <tr>
                    <td>Project Fireball</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa Y</td>
                    <td>
                        <span class="badge bg-danger">Cancelado</span>
                    </td>
                    <td class="d-none d-xl-table-cell">William Harris</td>
                </tr>

                <tr>
                    <td>Project Hades</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa Z</td>
                    <td>
                        <span class="badge bg-success">Finalizado</span>
                    </td>
                    <td class="d-none d-xl-table-cell">Sharon Lessman</td>
                </tr>

                <tr>
                    <td>Project Nitro</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa X</td>
                    <td>
                        <span class="badge bg-warning">Em Andamento</span>
                    </td>
                    <td class="d-none d-xl-table-cell">Vanessa Tucker</td>
                </tr>

                <tr>
                    <td>Project Phoenix</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa Y</td>
                    <td>
                        <span class="badge bg-success">Finalizado</span>
                    </td>
                    <td class="d-none d-xl-table-cell">William Harris</td>
                </tr>

                <tr>
                    <td>Project X</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa Z</td>
                    <td>
                        <span class="badge bg-success">Finalizado</span>
                    </td>
                    <td class="d-none d-xl-table-cell">Sharon Lessman</td>
                </tr>

                <tr>
                    <td>Project Romeo</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa X</td>
                    <td>
                        <span class="badge bg-success">Finalizado</span>
                    </td>
                    <td class="d-none d-xl-table-cell">Christina Mason</td>
                </tr>

                <tr>
                    <td>Project Wombat</td>
                    <td class="d-none d-xl-table-cell">01/01/2023</td>
                    <td class="d-none d-md-table-cell">Empresa Y</td>
                    <td>
                        <span class="badge bg-warning">Em Andamento</span>
                    </td>
                    <td class="d-none d-xl-table-cell">William Harris</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection