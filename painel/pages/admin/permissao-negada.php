<style>
    #warningModal .btn.btn-success
    {
        background: var(--green-color);
        border: none;
    }
</style>

<div class="modal fade" id="warningModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="warningModalLabel">403</h1>
            </div>
            <div class="modal-body">
                <p class="fs-6 fw-semibold">Você não tem permissão para acessar essa página!</p>
            </div>
            <div class="modal-footer fw-semibold px-4">
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="btn btn-danger d-flex align-items-center fw-semibold px-4 py-2 small">Voltar</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<!-- Assets -->
<script src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/js/form-steps.js"></script>
<script src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/js/main.js"></script>
<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function(){
        // Mostra o modal com id 'warningModal'
        $("#warningModal").modal('show');
    });
</script>