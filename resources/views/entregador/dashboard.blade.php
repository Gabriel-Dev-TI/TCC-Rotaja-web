@extends('layouts.app')

@section('content')

					<h1 class="h3 mb-3">Painel de <strong>Entregas</strong></h1>

					<div class="row">
						<div class="col-12 col-lg-12 col-xxl-9 d-flex">
							<div class="card flex-fill">
								<div class="card-header">

									<h5 class="card-title mb-0">Entregas Disponíveis</h5>
									</div>
								<table class="table table-hover my-0">
									<thead>
										<tr>
											<th>Nome</th>
											<th class="d-none d-xl-table-cell">Data</th>
                                            <th class="d-none d-md-table-cell">Empresa</th>
											<th>Status</th>
                                            <th class="d-none d-xl-table-cell"></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Bolo</td>
											<td class="d-none d-xl-table-cell">01/01/2023</td>
											<td class="d-none d-xl-table-cell">Padaria</td>
											<td><span class="badge bg-warning">Em trânsito</span></td>
											<td><button class="btn btn-secondary">Aceito</button></td>
										</tr>
										<tr>
											<td>Carrinho</td>
											<td class="d-none d-xl-table-cell">01/01/2023</td>
											<td class="d-none d-xl-table-cell">Brinquedos Ltda</td>
											<td><span class="badge bg-warning">Pendente</span></td>
											<td><button class="btn btn-success">Aceitar</button></td>
										</tr>
										
									</tbody>
									</table>
									</div>
							</div>
					</div>

					<div class="row">
						<div class="col-xl-6 col-xxl-6 d-flex">
							<div class="w-100">
								<div class="row">
										<div class="card">
											<div class="card-body">
												<div class="row">
													<div class="col mt-0">
														<h5 class="card-title">Entregas</h5>
													</div>

													<div class="col-auto">
														<div class="stat text-primary">
															<i class="align-middle" data-feather="truck"></i>
														</div>
													</div>
												</div>
												<h1 class="mt-1 mb-3">2.382</h1>
												<div class="mb-0">
													<span class="text-danger">-3.65%</span>
													<span class="text-muted">Desde a semana passada</span>
												</div>
											</div>
										</div>
										<div class="card">
											<div class="card-body">
												<div class="row">
													<div class="col mt-0">
														<h5 class="card-title">Ganhos</h5>
													</div>

													<div class="col-auto">
														<div class="stat text-primary">
															<i class="align-middle" data-feather="dollar-sign"></i>
														</div>
													</div>
												</div>
												<h1 class="mt-1 mb-3">$21.300</h1>
												<div class="mb-0">
													<span class="text-success">6.65%</span>
													<span class="text-muted">Desde a semana passada</span>
												</div>
											</div>
										</div>
								</div>
							</div>
						</div>

						<div class="col-xl-6 col-xxl-7">
							<div class="card flex-fill w-100">
								<div class="card-header">

									<h5 class="card-title mb-0">Entregas feitas este Ano</h5>
								</div>
								<div class="card-body py-3">
									<div class="chart chart-sm">
										<canvas id="chartjs-dashboard-line"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>

					
@endsection