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

    document.querySelectorAll('select').forEach(select => {
        new TomSelect(select);
    });

    document.querySelectorAll('input[type="date"]').forEach(date => {
        date.addEventListener('focus', function (event) {
            if (event.target.showPicker) {
                event.target.showPicker();
            }
        });
    });

    document.querySelectorAll('#cotisation_montant').forEach(input => {
        const defaultValue = input.value;
        const isPartialPayment = document.querySelector('#isPartialPayment-row');
        input.addEventListener('input', function (event) {
            if (!defaultValue || Number(event.target.value) < Number(defaultValue)) {
                isPartialPayment.style.display = 'block';
            } else {
                isPartialPayment.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('input.isPartialPayment').forEach(input => {
        input.addEventListener('change', function (event) {
            const partialPaymentReasonRow = document.querySelector('#partialPaymentReason-row');
            const partialPaymentReason = document.querySelector('#cotisation_partialReason');
            if (input.checked) {
                partialPaymentReasonRow.style.display = 'block';
                partialPaymentReason.required = true;
            } else {
                partialPaymentReasonRow.style.display = 'none';
                partialPaymentReason.required = false;
            }
        })
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
        new bootstrap.Tooltip(element)
    })
});

function leaveAtVisibility(input) {
    const leaveAtInput = document.querySelector('.leaveAt');
    const leaveAtParent = leaveAtInput.parentElement;
    if (input.checked) {
        leaveAtParent.classList.add('hidden');
        leaveAtInput.required = false;
    } else {
        leaveAtParent.classList.remove('hidden');
        leaveAtInput.required = true;
    }
}