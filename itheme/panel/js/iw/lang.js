const dropdownItems = document.querySelectorAll('.lang-set');

dropdownItems.forEach(item => {
    item.addEventListener('click', () => {
        const selectedLanguage = item.getAttribute('data-lang');
        document.cookie = `admin_language=${encodeURIComponent(selectedLanguage)}; expires=${new Date(Date.now() + 150 * 24 * 60 * 60 * 1000).toUTCString()}; path=/`;
        location.reload();
    });
});


