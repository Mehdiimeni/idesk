!function(l) {
    "use strict";

    function e() {
        this.$body = l("body"),
        this.$modal = new bootstrap.Modal(document.getElementById("event-modal"), { backdrop: "static" }),
        this.$calendar = l("#calendar"),
        this.$formEvent = l("#form-event"),
        this.$btnNewEvent = l("#btn-new-event"),
        this.$btnDeleteEvent = l("#btn-delete-event"),
        this.$btnSaveEvent = l("#btn-save-event"),
        this.$modalTitle = l("#modal-title"),
        this.$calendarObj = null,
        this.$selectedEvent = null,
        this.$newEventData = null
    }

    e.prototype.onEventClick = function(e) {
        this.$formEvent[0].reset(),
        this.$formEvent.removeClass("was-validated"),
        this.$newEventData = null,
        this.$btnDeleteEvent.show(),
        this.$modalTitle.text("Edit Event"),
        this.$modal.show(),
        this.$selectedEvent = e.event,
        l("#event-id").val(this.$selectedEvent.id),
        l("#event-title").val(this.$selectedEvent.title),
        l("#event-category").val(this.$selectedEvent.classNames[0]),
        l("#start-date").val(this.$selectedEvent.start.toISOString().slice(0, 16)),
        l("#end-date").val(this.$selectedEvent.end ? this.$selectedEvent.end.toISOString().slice(0, 16) : '')
    };

    e.prototype.onSelect = function(e) {
        this.$formEvent[0].reset(),
        this.$formEvent.removeClass("was-validated"),
        this.$selectedEvent = null,
        this.$newEventData = e,
        this.$btnDeleteEvent.hide(),
        this.$modalTitle.text("Add New Event"),
        this.$modal.show(),
        l("#event-id").val(''), // Clear the event ID
        l("#event-title").val(''),
        l("#event-category").val('bg-danger'),
        l("#start-date").val(e.startStr),
        l("#end-date").val(e.endStr),
        this.$calendarObj.unselect()
    };

    e.prototype.init = function() {
        var a = this;
        a.$calendarObj = new FullCalendar.Calendar(a.$calendar[0], {
            slotDuration: "00:15:00",
            slotMinTime: "08:00:00",
            slotMaxTime: "19:00:00",
            themeSystem: "bootstrap",
            bootstrapFontAwesome: false,
            buttonText: {
                today: "Today",
                month: "Month",
                week: "Week",
                day: "Day",
                list: "List",
                prev: "Prev",
                next: "Next"
            },
            initialView: "dayGridMonth",
            handleWindowResize: true,
            height: l(window).height() - 200,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth"
            },
            editable: true,
            droppable: true,
            selectable: true,
            dateClick: function(e) {
                a.onSelect(e)
            },
            eventClick: function(e) {
                a.onEventClick(e)
            }
        }),
        a.$calendarObj.render(),

        // Load events from the database
        l.ajax({
            url: '../icore/json/fetch_events.php',
            type: 'GET',
            dataType: 'json', 
            success: function(events) {
                console.log(events);
                a.$calendarObj.addEventSource(events);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching events:", status, error);
            }
        }),

        a.$btnNewEvent.on("click", function(e) {
            a.onSelect({ date: new Date(), allDay: true })
        }),

        a.$formEvent.on("submit", function(e) {
            e.preventDefault();
            var t, n = a.$formEvent[0];
            if (n.checkValidity()) {
                var formData = {
                    id: l("#event-id").val(),
                    title: l("#event-title").val(),
                    category: l("#event-category").val(),
                    start_date: l("#start-date").val(),
                    end_date: l("#end-date").val()
                };
                if (a.$selectedEvent) {
                    // Update existing event
                    l.ajax({
                        url: '../icore/json/save_events.php',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            alert(response.message);
                            a.$selectedEvent.setProp("title", formData.title);
                            a.$selectedEvent.setProp("classNames", [formData.category]);
                            a.$selectedEvent.setStart(formData.start_date);
                            a.$selectedEvent.setEnd(formData.end_date || null);
                            a.$modal.hide();
                        }
                    });
                } else {
                    // Add new event
                    l.ajax({
                        url: '../icore/json/save_events.php',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            alert(response.message);
                            a.$calendarObj.addEvent({
                                title: formData.title,
                                start: formData.start_date,
                                end: formData.end_date || null,
                                className: formData.category
                            });
                            a.$modal.hide();
                        }
                    });
                }
            } else {
                e.stopPropagation(),
                n.classList.add("was-validated")
            }
        }),

        l(a.$btnDeleteEvent.on("click", function(e) {
            if (a.$selectedEvent) {
                if (confirm('Are you sure you want to delete this event?')) {
                    l.ajax({
                        url: '../icore/json/delete_event.php',
                        type: 'POST',
                        data: { id: a.$selectedEvent.id },
                        success: function(response) {
                            console.log(response); // برای بررسی پیغام پاسخ
                            alert(response);
                            a.$selectedEvent.remove();
                            a.$selectedEvent = null;
                            a.$modal.hide();
                            setTimeout(x => {
                                location.reload();
                            }, 2002)
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error); // برای بررسی خطا
                        }
                    });
                }
            }
        }))
        
        
    };

    l.CalendarApp = new e,
    l.CalendarApp.Constructor = e
}(window.jQuery),

function() {
    "use strict";
    window.jQuery.CalendarApp.init()
}();
