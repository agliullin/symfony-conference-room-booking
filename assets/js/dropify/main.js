import 'dropify';

$('.dropify').dropify({
    messages: {
        'default': 'Перетащите файл сюда или нажмите',
        'replace': 'Перетащите или нажмите, чтобы заменить',
        'remove': 'Удалить',
        'error': 'Что-то пошло не так.'
    },
    error: {
        'fileSize': 'Размер файла слишком большой (максимум 2Mб).'
    }
});
