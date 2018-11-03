<?php
require_once 'OCExtensionCollector.php';

try {
    $app = new OCExtensionCollector();

    // Путь к проекту, в котором нужно искать. По умолчанию - upload
    //$app->setOCPath('upload');

    // В какой папке хранить собраный плагин.
    // По умолчанию - береться значение строки их параметра в конструкторе или extension если значение не задано
    //$app->setOutputFolder('output');

    // Выполнять ли поиск файлов плагина по путям из доп. файла
    //$app->useSubFile(true);

    // Путь к доп. файлу
    //$app->setSubFile('sub-file.txt');

    // Запуск сборщика
    $app->collect();

} catch (Exception $e) {
    print $e->getMessage();
}

