<?php
$logFilePath = 'telegram_log.txt';
$questions = json_decode(file_get_contents('questions.json'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $text = $_POST['name'] . "\n" . $_POST['phone'] . "\n";
    $q = [];

    unset($_POST['name']);
    unset($_POST['phone']);

    foreach ($_POST as $key => $value) {
        $updated_key = str_replace('answer-', '', $key);

        $numbers = explode('-', $updated_key);
        $number1 = intval($numbers[0]);
        $number2 = isset($numbers[1]) ? $numbers[1] : null;

        $item = $questions[$number1 - 1];
        $question = $item['question'];
        $options = $item['options'];
        $answer = $options[$value - 1];

        if (!in_array($question, $q)) {
            $q[] = $question;
            $text .= "\n" . $question . "\n";
        }
        $text .= $answer . "\n";
    }

    require_once 'Config.php';

    // Отправка текстового сообщения в Telegram
    $telegramToken = Config::$telegramToken;
    $telegramChatId = Config::$telegramChatId;

    // Формирование сообщения для отправки в Telegram
    $telegramText = "*** Новое сообщение с формы ***\n\n" . $text;

    // Отправка запроса к Telegram Bot API
    $telegramUrl = "https://api.telegram.org/bot{$telegramToken}/sendMessage";
    $telegramData = ['chat_id' => $telegramChatId, 'text' => $telegramText];

    $telegramOptions = ['http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($telegramData)]];

    $telegramContext = stream_context_create($telegramOptions);
    $telegramResponse = file_get_contents($telegramUrl, false, $telegramContext);

    // Получение текущей метки времени
    $timeStamp = "[" . date('Y-m-d H:i:s') . "] ";

    // Проверка статуса отправки
    if ($telegramResponse === false) {
        // Обработка ошибки при отправке
        $errorMessage = $timeStamp . 'Ошибка отправки сообщения в Telegram: ' . $http_response_header[0];
        $errorMessage .= PHP_EOL . 'Текст сообщения: ' . $text;
        file_put_contents($logFilePath, $errorMessage . PHP_EOL, FILE_APPEND);
    } else {
        // Обработка успешной отправки
        $successMessage = $timeStamp . 'Сообщение успешно отправлено в Telegram!';
        file_put_contents($logFilePath, $successMessage . PHP_EOL, FILE_APPEND);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Тест путешественника</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <p>Вы можете получить консультацию абсолютно бесплатно без обращения в офис</p>
    <h1>Пройдите тест за 1 минуту и узнайте, какие страны лучше всего подходят для вашего следующего путешествия!</h1>

    <div class="demo-text">Демонстрационный режим</div>

    <div class="additional-text">
        <p>Сразу после заполнения вы получите</p>
        100% точные рекомендации для вашего путешествия
    </div>

    <div class="additional-text-2">
        Узнайте, какие страны лучше всего подходят для вашего следующего путешествия
    </div>

    <form class="form" method="POST" target="">
        <?php
        for ($i = 1; $i <= count($questions); $i++) {
            $question = $questions[$i - 1];
            $type = $question['type'];
            $options = $question['options']; ?>
            <div class="question-block">
                <p><span class="question-number"><?= $i ?>. </span><?= $question['question'] ?></p>
                <div class="question-answers">
                    <?php
                    $id = 0;
                    foreach ($options as $option) { ?>
                        <div class="answer-option">
                            <?php
                            ++$id;
                            if ($type == 'radio') {
                                $name = 'answer-' . $i;
                                $id_val = 'answer-' . $i . '-' . $id;
                            } else {
                                $name = 'answer-' . $i . '-' . $id;
                                $id_val = $name;
                            }
                            ?>
                            <input type="<?= $type ?>" name="<?= $name ?>" id="<?= $id_val ?>" value="<?= $id ?>" <?php
                            if ($type == 'radio') {
                                echo 'required';
                            } ?>>
                            <label for="<?= $id_val ?>"><?= $option ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="additional-text-3">
            Заполните форму, чтобы получить консультацию
        </div>

        <div class="contacts">
            <div class="question-block">
                <div class="question-answers">
                    <label for="name">Имя*</label>
                    <input type="text" name="name" id="name" placeholder="Имя" required>
                    <label for="phone">Телефон*</label>
                    <input type="text" name="phone" id="phone" inputmode="tel" pattern="[+\d-]*"
                           oninput="this.value = this.value.replace(/[^+\d-]/g, '');"
                           placeholder="+7 ___ ___ __ __" required>
                    <button type="submit">Узнать</button>

                    <div class="additional-text-4">
                        Нажимая на кнопку, вы даете согласие на<br>
                        <a href="personal.html" target="_blank">обработку персональных данных</a>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function () {
        const value = this.value;
        const plusCount = (value.match(/\+/g) || []).length;
        if (plusCount > 1) {
            this.value = value.slice(0, -1);
        }
    });
</script>

</body>
</html>
