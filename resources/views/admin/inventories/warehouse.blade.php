@extends('admin.layouts.admin')

@section('title', 'Warehouse')

@section('content')
<div class="page-content">

               <!-- Start Container Fluid -->
               <div class="container-xxl">

                    <div class="row">
                         <div class="col-md-6 col-xl-3">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                             <div>
                                                  <h4 class="card-title mb-2 d-flex align-items-center gap-2">Total Product Items </h4>
                                                  <p class="text-muted fw-medium fs-22 mb-0">3521 <span class="fs-12">(Items)</span></p>
                                             </div>
                                             <div>
                                                  <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                       <iconify-icon icon="solar:box-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         <div class="col-md-6 col-xl-3">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                             <div>
                                                  <h4 class="card-title mb-2 d-flex align-items-center gap-2">In Stock Product </h4>
                                                  <p class="text-muted fw-medium fs-22 mb-0">1311 <span class="fs-12">(Items)</span></p>
                                             </div>
                                             <div>
                                                  <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                       <iconify-icon icon="solar:reorder-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>

                         <div class="col-md-6 col-xl-3">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                             <div>
                                                  <h4 class="card-title mb-2 d-flex align-items-center gap-2">Out Of Stock Product </h4>
                                                  <p class="text-muted fw-medium fs-22 mb-0">231 <span class="fs-12">(Items)</span></p>
                                             </div>
                                             <div>
                                                  <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                       <iconify-icon icon="solar:bag-cross-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>

                         <div class="col-md-6 col-xl-3">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                             <div>
                                                  <h4 class="card-title mb-2 d-flex align-items-center gap-2">Total Visited Customer</h4>
                                                  <p class="text-muted fw-medium fs-22 mb-0">2334 <span class="badge text-danger bg-danger-subtle fs-12"><i class="bx bx-down-arrow-alt"></i>4.5%</span> <span class="fs-12">(Last Week)</span></p>
                                             </div>
                                             <div>
                                                  <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                       <iconify-icon icon="solar:users-group-two-rounded-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>

                    </div>

                    <div class="row">
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <div>
                                             <h4 class="card-title">All Warehouse List</h4>
                                        </div>
                                        <div class="dropdown">
                                             <a href="inventory-warehouse.html#" class="dropdown-toggle btn btn-sm btn-outline-light rounded" data-bs-toggle="dropdown" aria-expanded="false">
                                                  This Month
                                             </a>
                                             <div class="dropdown-menu dropdown-menu-end">
                                                  <!-- item-->
                                                  <a href="inventory-warehouse.html#!" class="dropdown-item">Download</a>
                                                  <!-- item-->
                                                  <a href="inventory-warehouse.html#!" class="dropdown-item">Export</a>
                                                  <!-- item-->
                                                  <a href="inventory-warehouse.html#!" class="dropdown-item">Import</a>
                                             </div>
                                        </div>
                                   </div>
                                   <div>
                                        <div class="table-responsive">
                                             <table class="table align-middle mb-0 table-hover table-centered">
                                                  <thead class="bg-light-subtle">
                                                       <tr>
                                                            <th style="width: 20px;">
                                                                 <div class="form-check">
                                                                      <input type="checkbox" class="form-check-input" id="customCheck1">
                                                                      <label class="form-check-label" for="customCheck1"></label>
                                                                 </div>
                                                            </th>
                                                            <th>Warehouse ID</th>
                                                            <th>Warehouse Name</th>
                                                            <th>Location</th>
                                                            <th>Manager</th>
                                                            <th>Contact Number</th>
                                                            <th>Stock Available </th>
                                                            <th>Stock Shipping</th>
                                                            <th>Warehouse Revenue</th>
                                                            <th>Action</th>
                                                       </tr>
                                                  </thead>
                                                  <tbody>
                                                       <tr>
                                                            <td>
                                                                 <div class="form-check">
                                                                      <input type="checkbox" class="form-check-input" id="customCheck2">
                                                                      <label class="form-check-label" for="customCheck2"></label>
                                                                 </div>
                                                            </td>
                                                            <td>#WH-010</td>
                                                            <td>Northeast Depot</td>
                                                            <td>707 Distribution Rd, MA</td>
                                                            <td>Patricia Clark</td>
                                                            <td>+1 (555) 012-3456</td>
                                                            <td>5499</td>
                                                            <td>3433</td>
                                                            <td>$43,765</td>
                                                            <td>
                                                                 <div class="d-flex gap-2">
                                                                      <a href="inventory-warehouse.html#!" class="btn btn-light btn-sm"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                                                      <a href="inventory-warehouse.html#!" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                                                      <a href="inventory-warehouse.html#!" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                                                 </div>
                                                            </td>
                                                       </tr>

                                                  </tbody>
                                             </table>
                                        </div>
                                        <!-- end table-responsive -->
                                   </div>
                                   <div class="card-footer border-top">
                                        <nav aria-label="Page navigation example">
                                             <ul class="pagination justify-content-end mb-0">
                                                  <li class="page-item"><a class="page-link" href="javascript:void(0);">Previous</a></li>
                                                  <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                                                  <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                                                  <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                                                  <li class="page-item"><a class="page-link" href="javascript:void(0);">Next</a></li>
                                             </ul>
                                        </nav>
                                   </div>
                              </div>
                         </div>

                    </div>

               </div>
               </div>
@endsection
