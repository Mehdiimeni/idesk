$(document).ready(function () {
    "use strict";

    // تابع کمکی برای مقداردهی اولیه FixedHeader
    function initializeFixedHeader(datatableInstance) {
        // بررسی می‌کنیم که آیا FixedHeader واقعاً موجود است یا نه
        if (typeof $.fn.dataTable.FixedHeader !== 'undefined') {
            try {
                // datatableInstance معمولاً خود instance DataTables است
                // اگر بخواهیم مستقیم‌تر رفتار کنیم، می‌توانیم خود جدول را پاس دهیم
                // اما این رویکرد هم کار می‌کند اگر datatableInstance تنظیمات درستی داشته باشد
                new $.fn.dataTable.FixedHeader(datatableInstance.table().container());
                console.log('FixedHeader initialized successfully.');
            } catch (error) {
                console.warn('FixedHeader initialization failed:', error);
            }
        } else {
            console.warn('FixedHeader plugin is not available. Please ensure dataTables.fixedHeader.min.js is included.');
        }
    }

    // ------ جداول مختلف ------

    // Basic Datatable
    $("#basic-datatable").DataTable({
        keys: true,
        order: [0, 'desc'],
        lengthMenu: [10, 25, 50, 100, 200],
        pageLength: 25,
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

    // Datatable with Buttons
    var buttonsTable = $("#datatable-buttons").DataTable({
        lengthChange: false,
        order: [0, 'desc'],
        pageLength: 25,
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

    // Datatable with Selection
    $("#selection-datatable").DataTable({
        select: {
            style: "multi"
        },
        order: [0, 'desc'],
        lengthMenu: [10, 25, 50, 100, 200],
        pageLength: 25,
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

    // Datatable with Vertical Scrolling
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

    // Datatable with Alternative Pagination and Buttons
$(document).ready(function () {
    "use strict";

    let urlParams = new URLSearchParams(window.location.search);
    let mark = urlParams.get('mark') || '';
    let referred = urlParams.get('referred') || '';
    let condition = urlParams.get('condition_name') || '';
    let details = urlParams.get('details') || '';

    let currentSearch = '';
    let originalTitle = document.title;
    let tabHasUpdate = false;

    if (!$("#alternative-page-datatable").length) {
        return;
    }

    let table = $("#alternative-page-datatable").DataTable({
        pagingType: "full_numbers",
        responsive: true,
        order: [[0, 'desc']],
        lengthMenu: [10, 25, 50, 100, 250, 500],
        pageLength: 25,
        processing: true,
        serverSide: true,
        stateSave: true,
        deferRender: true,

        ajax: {
            url: "../icore/json/admin_data_table.php",
            type: "POST",
            data: function (d) {
                d.mark = mark;
                d.referred = referred;
                d.condition = condition;
                d.details = details;
                d.search.value = currentSearch;
                return d;
            }
        },

        columns: window.datatableColumns,

        dom:
            "<'ticket-table-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2'lfB>" +
            "rt" +
            "<'d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3'ip>",

        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-light border rounded-pill' },
            { extend: 'csv', className: 'btn btn-sm btn-light border rounded-pill' },
            { extend: 'excel', className: 'btn btn-sm btn-light border rounded-pill' },
            { extend: 'print', className: 'btn btn-sm btn-light border rounded-pill' }
        ],

        language: {
            processing:
                "<div class='ticket-loading'>" +
                "<div class='spinner-border spinner-border-sm text-primary me-2'></div>" +
                "در حال بارگذاری اطلاعات..." +
                "</div>",
            paginate: {
                first: "<i class='mdi mdi-page-first'></i>",
                last: "<i class='mdi mdi-page-last'></i>",
                previous: "<i class='mdi mdi-chevron-left'></i>",
                next: "<i class='mdi mdi-chevron-right'></i>"
            },
            search: "",
            lengthMenu: "نمایش _MENU_ ردیف",
            info: "نمایش _START_ تا _END_ از مجموع _TOTAL_ تیکت",
            emptyTable: "تیکتی برای نمایش وجود ندارد",
            zeroRecords: "نتیجه‌ای مطابق جستجو پیدا نشد"
        },

        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");

            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                    new bootstrap.Tooltip(el);
                });
            }
        },

        initComplete: function () {
            let searchInput = $('#alternative-page-datatable_filter input');

            searchInput.off();
            searchInput.off('.DT');

            searchInput
                .addClass('form-control form-control-sm rounded-pill ticket-search-input')
                .attr('placeholder', 'جستجو با حداقل ۴ کاراکتر + Enter');

            searchInput.on('input', function () {
                let value = $(this).val().trim();

                if (value.length === 0 && currentSearch !== '') {
                    currentSearch = '';
                    table.search('').draw();
                }
            });

            searchInput.on('keydown', function (e) {
                if (e.key !== 'Enter') {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                let value = $(this).val().trim();

                if (value.length === 0) {
                    currentSearch = '';
                    table.search('').draw();
                    return false;
                }

                if (value.length < 4) {
                    alert('حداقل ۴ کاراکتر برای جستجو وارد کنید');
                    return false;
                }

                currentSearch = value;
                table.search(value).draw();

                return false;
            });
        }
    });

    window.ticketDataTable = table;

    setupTicketAutoReload(table);

    function setupTicketAutoReload(table) {
        const reloadInterval = 15 * 60 * 1000;

        setInterval(function () {
            table.ajax.reload(function () {
                if (document.hidden) {
                    tabHasUpdate = true;
                    document.title = "🔴 " + originalTitle;
                } else {
                    showTicketReloadToast('لیست تیکت‌ها به‌روزرسانی شد');
                }
            }, false);
        }, reloadInterval);
    }

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && tabHasUpdate) {
            tabHasUpdate = false;
            document.title = originalTitle;
            showTicketReloadToast('لیست تیکت‌ها به‌روزرسانی شد');
        }
    });

    function showTicketReloadToast(message) {
        if (!document.getElementById('ticketReloadToast')) {
            $('body').append(
                '<div id="ticketReloadToast" ' +
                'class="position-fixed bottom-0 end-0 m-3 alert alert-primary shadow-sm rounded-pill px-3 py-2" ' +
                'style="z-index:9999; display:none;"></div>'
            );
        }

        $('#ticketReloadToast')
            .text(message)
            .stop(true, true)
            .fadeIn(150)
            .delay(2500)
            .fadeOut(300);
    }
});

});

