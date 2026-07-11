
// todo ticket

document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('.dropdown-todo').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            document.getElementById('dropdownTodoMenuButton').textContent = item.textContent;
            document.getElementById('selectedTodoList').value = item.getAttribute('data-value');
        });
    });
});


