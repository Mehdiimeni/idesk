document.addEventListener('DOMContentLoaded', function () {

    const cookieManager = {
        set: (name, value, days = 180) => {
            const expiryDate = new Date();
            expiryDate.setTime(expiryDate.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
        },
        get: (name) => {
            const match = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]+)'));
            return match ? decodeURIComponent(match[1]) : null;
        }
    };

    function normalizeLoadValue(value) {
        if (!value || value === 1 || value === '0') return 1;
        const num = parseInt(value, 10);
        return isNaN(num) ? 1 : num;
    }

    function getUrlParams() {
        return new URLSearchParams(window.location.search);
    }

    function getCurrentLoadValue() {
        const params = getUrlParams();
        const load = params.get('load');
        if (load) return normalizeLoadValue(load);

        const cookieLoad = cookieManager.get('ticket_show_limit');
        return cookieLoad ? normalizeLoadValue(cookieLoad) : 10000;
    }

    function getCurrentReferredValue() {
        const params = getUrlParams();
        const ref = params.get('referred');
        return (ref === '1' || ref === '0') ? ref : null;
    }

    function buildTicketsUrl(loadValue) {
        const params = new URLSearchParams();
        const referred = getCurrentReferredValue();

        if (referred !== null) params.set('referred', referred);
        if (loadValue !== 1) params.set('load', loadValue);

        return './tickets' + (params.toString() ? `?${params}` : '');
    }

    function updateDropdownButtonText(value) {
        const btn = document.getElementById('dropdownMenuButton');
        if (!btn) return;

        btn.textContent = value === 1
            ? 'نمایش همه'
            : `${value} مورد آخر`;
    }

    function setActiveItem(value) {
        document
            .querySelectorAll('.ticket-load-dropdown .dropdown-item')
            .forEach(item => {
                item.classList.toggle(
                    'active',
                    String(item.dataset.load) === String(value)
                );
            });
    }

    function syncTableLengthCookie(loadValue) {
        let tableLength = loadValue === 1 || loadValue >= 100 ? 50 : 10;
        cookieManager.set('datatable_length', tableLength, 180);
    }

    const currentLoadValue = getCurrentLoadValue();

    updateDropdownButtonText(currentLoadValue);
    setActiveItem(currentLoadValue);
    syncTableLengthCookie(currentLoadValue);
    cookieManager.set('ticket_show_limit', currentLoadValue, 180);

    // ✅ فقط منوی Tickets
    document
        .querySelectorAll('.ticket-load-dropdown .dropdown-item')
        .forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();

                const selectedValue = normalizeLoadValue(this.dataset.load);

                cookieManager.set('ticket_show_limit', selectedValue, 180);
                updateDropdownButtonText(selectedValue);
                setActiveItem(selectedValue);
                syncTableLengthCookie(selectedValue);

                window.location.href = buildTicketsUrl(selectedValue);
            });
        });

});
