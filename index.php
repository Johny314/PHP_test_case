<!doctype html>
<html lang="en" data-bs-theme="dark" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Главная</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body class="d-flex flex-column h-100">
<div class="container">
    <?php include "inc/header.html"; ?>

    <div><h2>Главная страница</h2></div>

    <div>
        <div class="alert alert-success success-message" role="alert" style="display: none;">
            <p>Ваше сообщение успешно отправлено!</p>
            <p class="countdown-message">Возврат к вводу через 5 секунд...</p>
        </div>

        <form class="row g-3 needs-validation" novalidate>
            <div class="col-md-6">
                <label for="user_name" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Ваше имя пользователя" required>
                <div class="invalid-feedback">
                    Введите имя пользователя, содержащее только цифры и буквы латинского алфавита
                </div>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                    <input type="email" class="form-control" id="email"  name="email" aria-describedby="inputGroupPrepend" placeholder="Ваш E-mail" required>
                    <div class="invalid-feedback">
                        Введите корректный E-mail адрес
                    </div>
                </div>
            </div>
            <div class="form-floating">
                <textarea class="form-control" placeholder="Leave a comment here" id="message_text"  name="message_text" style="height: 300px" required></textarea>
                <label for="message_text">Текст сообщения</label>
                <div class="invalid-feedback">
                   Пожалуйста, заполните это поле
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" id="add_message" type="submit">Отправить</button>
            </div>
        </form>
    </div>

    <?php include "inc/footer.html"; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const btn = document.getElementById('1');
        btn.classList.add('active');
        const form = document.querySelector('.needs-validation');
        const successMessage = document.querySelector('.success-message');
        const countdownMessage = document.querySelector('.countdown-message');
        form.addEventListener('submit', function(event) {
            let button = document.getElementById('add_message');
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...';
            button.disabled = true;
            event.preventDefault();
            form.classList.add('was-validated');

            if (form.checkValidity()) {
                const formData = new FormData(form);
                const emailInput = form.querySelector('#email');

                formData.append('event', 'add_message');
                fetch('handler.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Ошибка сети');
                        }
                        return response.json();

                    })
                    .then(data => {
                        if (data.success) {
                            // Обработка успешного ответа от сервера
                            button.innerHTML = 'Отправить';
                            // Вывод уведомления об успешной отправке
                            successMessage.style.display = 'block';
                            // Скрытие уведомления через несколько секунд
                            let secondsLeft = 5; // Изменяемое значение времени задержки
                            countdownMessage.textContent = `Возврат к вводу через ${secondsLeft} секунд`;
                            // Обратный отсчет времени
                            const countdownTimer = setInterval(() => {
                                secondsLeft--;
                                countdownMessage.textContent = `Возврат к вводу через ${secondsLeft} секунд`;
                                if (secondsLeft <= 0) {
                                    clearInterval(countdownTimer);
                                    successMessage.style.display = 'none';
                                    countdownMessage.style.display = 'none';
                                    emailInput.classList.remove('is-invalid');
                                    form.classList.remove('was-validated');
                                    button.disabled = false;
                                    form.reset()
                                    form.classList.add('needs-validation');
                                }
                            }, 1000); // Обновление каждую секунду
                            secondsLeft = 5;
                            countdownMessage.style.display = 'block';

                            console.log(data.message);
                        } else {
                            console.log(data.message);
                        }
                    })
                    .catch(error => console.log('Error:', error));
            }
            else {
                button.innerHTML = 'Отправить';
                button.disabled = false;
            }
        });
    });

    function validateEmail(email) {
        const re = /\S+@\S+\.\S+/;
        return re.test(email);
    }
</script>
</body>
</html>
