 const lists = Array.from(document.querySelectorAll('.task-list-items'));

    dragula(lists)
        .on('drop', function (el, target, source, sibling) {
            // شناسه تسک
            const board_id = el.getAttribute('data-id');

            // شناسه ستون مقصد (board جدید)
            const board_tag_id = target.getAttribute('data-board-tag-id');

            // 📤 با Ajax آپدیت می‌کنیم
            updateTaskPriority(board_id, board_tag_id);
        });

    function updateTaskPriority(board_id, board_tag_id) {
        $.ajax({
            url: '../icore/json/update_kanban_board.php',
            method: 'POST',
            dataType: 'json',
            data: {
                board_id: board_id,
                board_tag_id: board_tag_id
            },
            success: function (response) {
                if (response.success) {
                    setTimeout(() => location.reload(), 2002);
                } else {
                    alert('❌ Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
            }
        });
    }