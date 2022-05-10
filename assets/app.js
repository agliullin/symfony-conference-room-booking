/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

const $ = require('jquery');
global.$ = global.jQuery = $;

// You can specify which plugins you need
import 'bootstrap';
import 'daterangepicker';
import Swal from 'sweetalert2';

// start the Stimulus application
import './bootstrap';

$('#datetimepicker input').daterangepicker({
    timePicker: true,
    timePicker24Hour: true,
    timePickerIncrement: 5,
    locale: {
        format: 'DD.MM.YYYY HH:mm',
        applyLabel: "Ок",
        cancelLabel: "Отмена",
        fromLabel: "От",
        toLabel: "До",
        customRangeLabel: "Произвольный",
        daysOfWeek: [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        monthNames: [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
    }
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('#alert-modal').modal('show');

    $('.delete-confirm').on('click', function (event) {
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Вы уверены?',
            text: 'Внимание, данное действие необратимо.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e82646',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Удалить',
            cancelButtonText: 'Отмена',
            focusConfirm: false,
            focusCancel: false,
        }).then((result) => {
            if (result.value) {
                form.submit();
            }
        })
    });

})