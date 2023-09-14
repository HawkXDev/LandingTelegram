<?php
$questions = json_decode(file_get_contents('questions.json'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
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
        Узнайте, какие страны лучше всего подходят<br>для вашего следующего путешествия
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
                            <?php $name = 'answer-' . $i . '-' . ++$id; ?>
                            <input type="<?= $type ?>" name="<?= $name ?>" id="<?= $name ?>">
                            <label for="<?= $name ?>"><?= $option ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <button type="submit">Узнать</button>
    </form>
</div>

</body>
</html>
