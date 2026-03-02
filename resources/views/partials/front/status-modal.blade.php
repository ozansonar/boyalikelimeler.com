{{-- Global Status Modal — success / error --}}
@if(session('success') || session('error'))
    @php
        $isSuccess = session()->has('success');
        $modalType = $isSuccess ? 'success' : 'danger';
        $modalTitle = $isSuccess ? 'Başarılı' : 'Hata';
        $modalIcon = $isSuccess ? 'fa-circle-check' : 'fa-circle-xmark';
        $modalMessage = $isSuccess ? session('success') : session('error');
    @endphp

    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content status-modal">
                <div class="modal-header status-modal__header status-modal__header--{{ $modalType }} border-0">
                    <h5 class="modal-title status-modal__title" id="statusModalLabel">
                        <i class="fa-solid {{ $modalIcon }} me-2"></i>{{ $modalTitle }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body status-modal__body">
                    <p class="mb-0">{{ $modalMessage }}</p>
                </div>
                <div class="modal-footer status-modal__footer border-0">
                    <button type="button" class="btn status-modal__btn status-modal__btn--{{ $modalType }}" data-bs-dismiss="modal">
                        Tamam
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
