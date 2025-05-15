// Динамическая загрузка партнеров
document.addEventListener('DOMContentLoaded', () => {
    fetch('api/partners.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('partnersContainer');
            data.forEach(partner => {
                container.innerHTML += `
                    <a href="${partner.url}" class="partner-card">
                        <img src="images/partners/${partner.logo}" alt="${partner.name}">
                    </a>
                `;
            });
        });
});

// Обработка формы входа
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('api/login.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            window.location.href = 'index.php';
        } else {
            showError(data.message);
        }
    });
});