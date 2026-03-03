{{-- ============================================================
     GLOBAL STATUS MODAL — success / danger / warning / info
     Always in DOM — filled dynamically by BkModal (JS)
     ============================================================ --}}

{{-- Pass session flash data to JS --}}
@if(session('success') || session('warning') || session('error') || $errors->any())
    <script id="bkFlashData" type="application/json">
        @php
            $flashData = ['type' => null, 'message' => null];

            if ($errors->any()) {
                $flashData['type'] = 'danger';
                $flashData['message'] = $errors->all();
            } elseif (session()->has('success')) {
                $flashData['type'] = 'success';
                $flashData['message'] = session('success');
            } elseif (session()->has('warning')) {
                $flashData['type'] = 'warning';
                $flashData['message'] = session('warning');
            } elseif (session()->has('error')) {
                $flashData['type'] = 'danger';
                $flashData['message'] = session('error');
            }
        @endphp
        {!! json_encode($flashData, JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

{{-- Modal Structure --}}
<div class="gmodal-overlay" id="globalModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="gmodalTitle">
    <div class="gmodal" role="document">
        <button type="button" class="gmodal__close" id="gmodalClose" aria-label="Kapat">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="gmodal__icon-wrap" id="gmodalIconWrap">
            <div class="gmodal__icon-ring" id="gmodalIconRing">
                <i class="fa-solid fa-circle-check" id="gmodalIcon"></i>
            </div>
            <div class="gmodal__icon-particles" id="gmodalParticles"></div>
        </div>

        <h4 class="gmodal__title" id="gmodalTitle"></h4>
        <div class="gmodal__message" id="gmodalMessage"></div>

        <button type="button" class="gmodal__btn" id="gmodalBtn">Tamam</button>
    </div>
</div>
