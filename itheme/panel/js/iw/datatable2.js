function getSavedLength() {

    const name = "datatable_length=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookieArray = decodedCookie.split(';');

    for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i].trim();
        if (cookie.indexOf(name) === 0) {
            return parseInt(cookie.substring(name.length), 10);
        }
    }
    return 10; // مقدار پیش‌فرض
}

function saveLength(length) {
    const expiryDate = new Date();
    expiryDate.setTime(expiryDate.getTime() + (180 * 24 * 60 * 60 * 1000)); // 180 روز
    const expires = "expires=" + expiryDate.toUTCString();
    // Save to both cookies for consistency
    document.cookie = "datatable_length=" + length + ";" + expires + ";path=/";
    document.cookie = "ticket_show_limit=" + length + ";" + expires + ";path=/";
}

// مدیریت خطای FixedHeader
function initializeFixedHeader(datatable) {
    if (typeof $.fn.dataTable.FixedHeader !== 'undefined') {
        try {

            var fhTarget = datatable;
            if (datatable && typeof datatable.settings === 'function') {
                var s = datatable.settings();
                if (s && s.length) fhTarget = s[0];
            }
            if (fhTarget && fhTarget.jquery && fhTarget.length) {
                fhTarget = fhTarget.get(0);
            }
            if (!fhTarget && typeof datatable === 'string') {
                var $t = $(datatable);
                if ($t.length) fhTarget = $t.get(0);
            }

            new $.fn.dataTable.FixedHeader(fhTarget);
        } catch (error) {
            console.warn('FixedHeader initialization failed:', error);
        }
    } else {
        console.warn('FixedHeader plugin not available');
    }
}

$(document).ready(function () {
    "use strict";

    // Basic Datatable
    $("#basic-datatable").DataTable({
        keys: true,
        order: [0, 'desc'],
        lengthMenu: [10, 25, 50, 100, 200],
        pageLength: getSavedLength(),
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        }
    }).on('length.dt', function (e, settings, len) {
        saveLength(len);
    });

    var buttonsTable = $("#datatable-buttons").DataTable({
        lengthChange: false,
        order: [0, 'desc'],
        pageLength: getSavedLength(),
        buttons: ["copy", "print"],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        }
    });

    buttonsTable.buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)");

    $("#selection-datatable").DataTable({
        select: {
            style: "multi"
        },
        order: [0, 'desc'],
        lengthMenu: [10, 25, 50, 100, 200],
        pageLength: getSavedLength(),
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        }
    }).on('length.dt', function (e, settings, len) {
        saveLength(len);
    });

    $("#scroll-vertical-datatable").DataTable({
        scrollY: "450px",
        order: [0, 'desc'],
        scrollCollapse: true,
        paging: false,
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        }
    });

    var savedLength = getSavedLength();
    var lengthMenuOptions = [10, 25, 50, 100, 200];
    
    if (savedLength > 0 && !lengthMenuOptions.includes(savedLength)) {
        lengthMenuOptions.push(savedLength);
        lengthMenuOptions.sort((a, b) => a - b);
    }
    
    $("#alternative-page-datatable").DataTable({
        pagingType: "full_numbers",
        responsive: true,
        order: [0, 'desc'],
        lengthMenu: [lengthMenuOptions, lengthMenuOptions],
        pageLength: savedLength === 0 ? -1 : savedLength, // 0 means show all (-1 in DataTables)
        dom: '<"top"flB>rt<"bottom"pi><"clear">',
        buttons: ['copy', 'csv', 'excel', 'print'],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        }
    }).on('length.dt', function (e, settings, len) {
        saveLength(len);
    });

    var fixedHeaderTable = $("#fixed-header-datatable").DataTable({
        responsive: true,
        order: [0, 'desc'],
        lengthMenu: [10, 25, 50, 100, 200],
        pageLength: getSavedLength(),
        initComplete: function(settings, json) {
            try {

                initializeFixedHeader(settings);
            } catch (e) {
                console.warn('FixedHeader initComplete handler failed:', e);
            }
        },
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        }
    }).on('length.dt', function (e, settings, len) {
        saveLength(len);
    });

    $(".dataTables_length select").addClass("form-select form-select-sm");
    $(".dataTables_length label").addClass("form-label");
});

if (typeof $.fn.dataTable.FixedHeader === 'undefined') {
    $.fn.dataTable.FixedHeader = function(table) {
        return {
            destroy: function() {}
        };
    };
}