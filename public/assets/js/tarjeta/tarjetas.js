document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-danger');
    buttons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const card = this.closest('.card-payment');
            if (card) {
                card.remove();
            }
        });
    });
});
