@props([
    'modalId' => 'deleteConfirmModal',
    'nameElId' => 'deleteContentTitle',
    'title' => 'Silme Onayı',
    'message' => 'Bu öğeyi silmek istediğinizden emin misiniz?',
    'buttonText' => 'Evet, Sil',
    'size' => 'modal-sm',
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $size }}">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="status-modal-icon danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h5 class="cl-modal-heading">{{ $title }}</h5>
                <p class="cl-modal-body-text">{{ $message }}</p>
                <p class="cl-modal-content-name" id="{{ $nameElId }}"></p>
                {{ $slot }}
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-teal btn-danger-gradient">
                            <i class="bi bi-trash me-1"></i>{{ $buttonText }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
