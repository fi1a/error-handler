<?php
declare(strict_types=1);

use Fi1a\ErrorHandler\InspectorInterface;

/** @var InspectorInterface $inspector */
/** @var array<string, array<string, string>> $info */
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlentities($inspector->getMessage())?></title>
</head>
<body>
    <div class="header">
        <div class="name"><?= htmlentities($inspector->getName())?> <span class="code">(<?= htmlentities($inspector->getCode())?>)</span></div>
        <h1 class="message"><?= htmlentities($inspector->getMessage())?></h1>
    </div>
    <?php
    $previous = $inspector->getPrevious();
    ?>
    <?php if ($previous) {?>
        <div class="previous">
            <h2>Предыдущие исключения</h2>
            <ol>
                <?php
                while ($previous) {
                    ?>
                    <li>
                        <span class="message"><?= htmlentities($previous->getMessage())?></span>
                        &mdash;
                        <span class="name"><?= htmlentities($previous->getName())?></span>
                        <span class="code">(<?= htmlentities($previous->getCode())?>)</span>
                    </li>
                    <?php
                    $previous = $previous->getPrevious();
                }
                ?>
            </ol>
        </div>
    <?php } ?>
    <div class="backtrace">
        <?php
        $backtrace = $inspector->getBacktrace();
        $number = count($backtrace);
        ?>
        <?php foreach ($backtrace as $item) { ?>
            <?php
            $number--;
            ?>
            <div class="backtrace-item<?php if ($number === count($backtrace) - 1) { ?> active<?php } ?>">
                <div class="backtrace-header">
                    <div class="number"><?= $number?></div>
                    <div class="name"><?= htmlentities($item['name'])?></div>
                </div>
                <?php if ($item['file']) { ?>
                    <div class="file"><?= htmlentities($item['file'])?>:<span class="line"><?= htmlentities((string) $item['line'])?></span></div>
                <?php } ?>
                <pre class="code line-numbers"
                     data-line="<?= htmlentities((string) $item['line'])?>"
                     data-start="<?= htmlentities((string) $item['start'])?>"
                ><code class="language-php"><?= htmlentities($item['code'])?></code></pre>
            </div>
        <?php } ?>
    </div>
    <div class="details">
        <h2>Информация об окружении</h2>
        <?php foreach ($info as $name => $data) { ?>
            <div class="block">
                <h3><?= htmlentities($name)?></h3>
                <?php if (!count($data)) { ?>
                    <span class="empty">пусто</span>
                <?php
                    continue;
                }
                ?>
                <ul>
                    <?php foreach ($data as $key => $value) { ?>
                        <li class="line"><span class="key"><?= htmlentities($key)?></span>: <?= htmlentities($value)?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>
    <style>
        <?= file_get_contents(__DIR__ . '/css/style.min.css')?>
    </style>
    <script type="application/javascript">
        <?= file_get_contents(__DIR__ . '/js/script.min.js')?>
    </script>
</body>
</html>