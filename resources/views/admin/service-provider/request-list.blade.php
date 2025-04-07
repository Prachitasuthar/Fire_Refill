@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">
            <h2 class="mb-4 text-center">Request List</h2>
            <table class="table table-bordered" id="provider-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Business Name</th>
                        <th>Status</th>
                        <th>Email Verified</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Provide Rejection Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <textarea id="rejectionReason" class="form-control" placeholder="Enter reason..."></textarea>
                    <input type="hidden" id="rejectProviderId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="submitRejection">Submit</button>
                </div>
            </div>
        </div>
    </div>


    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var table = $('#provider-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.providers') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'business_name',
                        name: 'business_name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email_verified',
                        name: 'email_verified',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Status change
            $(document).on('change', '.status-change', function() {
                var providerId = $(this).data('id');
                var status = $(this).val();

                if (status === 'rejected') {
                    $('#rejectProviderId').val(providerId);
                    $('#rejectionModal').modal('show');
                } else {
                    updateStatus(providerId, status);
                }
            });

            // Submit rejection reason
            $('#submitRejection').on('click', function() {
                var providerId = $('#rejectProviderId').val();
                var reason = $('#rejectionReason').val().trim();

                if (reason === '') {
                    Swal.fire('Error', 'Rejection reason is required!', 'error');
                    return;
                }

                $('#rejectionModal').modal('hide');
                updateStatus(providerId, 'rejected', reason);
            });

            function updateStatus(providerId, status, reason = null) {
                $.ajax({
                    url: "{{ route('admin.providers.updateStatus') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: providerId,
                        status: status,
                        reason: reason
                    },
                    success: function(response) {
                        Swal.fire('Success', response.success, 'success');
                        table.ajax.reload();
                    }
                });
            }
        });
    </script>
@endsection
