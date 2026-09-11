{{--
    Boost Post Feed Strip Styles & Navigation Handler
    - Clean, sleek ribbon on feed post cards
    - Navigates to the dedicated /post/timeline/{id}/boost page
--}}

@once
<style>
    .pk-boost-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 14px;
        margin: 8px 16px 12px;
        border-radius: 10px;
        background: #F8FAFC;
        border: 1px solid #EDE9FE;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none !important;
    }

    .pk-boost-strip:hover {
        background: #FAF5FF;
        border-color: #DDD6FE;
    }

    .pk-boost-strip-left {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        color: #334155;
    }

    .pk-boost-strip-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #6D28D9;
        background: #EDE9FE;
        padding: 2px 7px;
        border-radius: 999px;
    }

    .pk-boost-strip-btn {
        font-size: 0.74rem;
        font-weight: 700;
        color: #6D28D9;
        background: #FFFFFF;
        border: 1px solid #DDD6FE;
        padding: 4px 10px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .pk-boost-strip:hover .pk-boost-strip-btn {
        background: #6D28D9;
        color: #FFFFFF;
        border-color: #6D28D9;
    }
</style>

<script>
    // Global fallback for pk-boost-open event -> routes to dedicated page
    window.addEventListener('pk-boost-open', function (e) {
        if (e.detail && e.detail.postId) {
            if (window.Livewire && window.Livewire.navigate) {
                window.Livewire.navigate('/post/timeline/' + e.detail.postId + '/boost');
            } else {
                window.location.href = '/post/timeline/' + e.detail.postId + '/boost';
            }
        }
    });
</script>
@endonce
