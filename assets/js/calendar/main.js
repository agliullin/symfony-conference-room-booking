import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import ruLocale from '@fullcalendar/core/locales/ru';

document.addEventListener("DOMContentLoaded", () => {
    var calendarEl = document.getElementById("calendar-holder");

    var eventsUrl = calendarEl.dataset.eventsUrl;
    var filterRoom = calendarEl.dataset.filterRoom;
    var filterOwner = calendarEl.dataset.filterOwner;
    var filterMember = calendarEl.dataset.filterMember;

    var calendar = new Calendar(calendarEl, {
        locales: [ ruLocale ],
        locale: 'ru',
        initialView: 'dayGridMonth',
        displayEventTime: true,
        editable: false,
        eventSources: [
            {
                url: eventsUrl,
                method: "POST",
                extraParams: {
                    filters: JSON.stringify({
                        "room": filterRoom,
                        "owner": filterOwner,
                        "member": filterMember,
                    })
                },
                failure: () => {
                    // alert("There was an error while fetching FullCalendar!");
                },
            },
        ],
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: 'dayGridMonth,timeGridWeek,listDay'
        },
        views: {
            dayGridMonth: {
                titleFormat: { year: 'numeric', month: 'long', day: 'numeric' }
            },
            timeGridWeek: {
                titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
                allDaySlot: false,
                nowIndicator: true,
            }
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin],
        timeZone: "UTC",
        navLinks: true,
    });
    calendar.render();
});