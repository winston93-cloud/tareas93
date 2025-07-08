// Control dinámico de submenús
document.querySelectorAll('.menu-item').forEach((item) => {
    item.addEventListener('mouseenter', () => {
        const dropdown = item.querySelector('.dropdown');
        if (dropdown) {
            dropdown.style.display = 'block';
        }
    });

    item.addEventListener('mouseleave', () => {
        const dropdown = item.querySelector('.dropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    });
});
