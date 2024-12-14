document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input.text-uppercase').forEach(input => {
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
        const options = {};

        if (select.hasAttribute('data-select-no-search')) {
            options.controlInput = null;
        }

        if (select.hasAttribute('data-select-allow-empty')) {
            options.allowEmptyOption = true;
        }

        if (select.hasAttribute('data-select-label')) {
            options.render = {
                item: function(data, escape) {
                    return `<div>
                        ${select.getAttribute('data-select-label')}
                        <strong>${escape(data.text)}</strong>
                    </div>`;
                }
            }
        }

        new TomSelect(select, options);
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
    });

    document.querySelectorAll('#cotisations-filter-form').forEach(form => {
        form.addEventListener('input', function () {
            form.submit();
        });
    });

    document.querySelectorAll('#tarif_debutPeriode').forEach(input => {
        const setOneYeatLater = () => {
            const date = input.valueAsDate;
            if (date) {
                date.setFullYear(date.getFullYear() + 1);
                date.setDate(date.getDate() - 1);

                const finPeriode = document.querySelector('#tarif_finPeriode');
                finPeriode.valueAsDate = date;
            }
        }

        setOneYeatLater();

        input.addEventListener('input', setOneYeatLater);
    });

    document.querySelectorAll('.selectTypeDepense').forEach(select => {
        select.addEventListener('change', function () {
            const inputMontant = document.querySelector('#depense_montant');
            const typesMontant = document.querySelector('#typesDepenseMontant').value;
            const json = JSON.parse(typesMontant);
            if (select.value) {
                inputMontant.value = json[select.value];
            }
        });
    });
});

function leaveAtVisibility(input) {
    const leaveAtInput = document.querySelector('.leaveAt');
    const leaveAtParent = leaveAtInput.parentElement;
    if (input.checked) {
        leaveAtParent.classList.add('d-none');
        leaveAtInput.required = false;
    } else {
        leaveAtParent.classList.remove('d-none');
        leaveAtInput.required = true;
    }
}