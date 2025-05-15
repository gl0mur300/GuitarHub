// Проверка доступности имени пользователя
document.getElementById('username').addEventListener('blur', function() {
    const username = this.value;
    if(username.length < 4) return;

    fetch(`api/check_username.php?username=${username}`)
        .then(response => response.json())
        .then(data => {
            if(data.available) {
                this.classList.remove('error');
            } else {
                this.classList.add('error');
                showError('Имя пользователя занято');
            }
        });
});