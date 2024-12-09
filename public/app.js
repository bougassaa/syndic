document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input.uppercase').forEach(input => {
        input.addEventListener('blur', function (event) {
            event.target.value = event.target.value.toUpperCase()
        })
    });

    document.querySelectorAll('input.isCurrentOwner').forEach(input => {
        leaveAtVisibility(input);

        input.addEventListener('change', function (event) {
            leaveAtVisibility(input);
        })
    });
});

function leaveAtVisibility(input) {
    const leaveAt = document.querySelector('.leaveAt').parentElement;
    if (input.checked) {
        leaveAt.classList.add('hidden');
    } else {
        leaveAt.classList.remove('hidden');
    }
}