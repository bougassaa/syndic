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

    document.querySelectorAll('select.cotisationTarif').forEach(select => {
        const montantInput = document.querySelector('#cotisation_montant');
        const tarifsMapping = jsonParse(document.querySelector('#tarifsMapping').value);
        select.addEventListener('change', () => {
            if (tarifsMapping[select.value]) {
                montantInput.value = tarifsMapping[select.value];
            }
        })
    });

    document.querySelectorAll('#cotisation_montant').forEach(input => {
        const tarifsMapping = jsonParse(document.querySelector('#tarifsMapping').value);
        const tarifSelect = document.querySelector('select.cotisationTarif');
        const isPartialPayment = document.querySelector('#isPartialPayment-row');

        input.addEventListener('input', () => {
            const montant = tarifsMapping[tarifSelect.value];
            if (!montant || Number(input.value) < Number(montant)) {
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

    document.querySelectorAll('.list-filter-form').forEach(form => {
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
            const json = jsonParse(typesMontant);
            if (select.value) {
                inputMontant.value = json[select.value];
            }
        });
    });

    document.querySelectorAll('input[type=file]').forEach(input => {
        input.addEventListener('change', async e => {
            const files = e.target.files;
            const dataTransfer = new DataTransfer();

            // Create an array of Promises to wait for all compressions to complete
            const filePromises = Array.from(files).map(file => {
                if (file.type.startsWith('image/')) {
                    return new Promise(resolve => {
                        new Compressor(file, {
                            quality: 0.7, // Compression quality
                            maxWidth: 1100, // Max width of compressed image
                            maxHeight: 800, // Max height of compressed image
                            success(result) {
                                const compressedFile = new File([result], file.name, {
                                    type: file.type,
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile); // Resolve with the compressed file
                            },
                            error() {
                                resolve(file); // Resolve with the original file if compression fails
                            }
                        });
                    });
                } else {
                    return Promise.resolve(file); // Non-image files don't need compression
                }
            });
            // Wait for all Promises to resolve
            const processedFiles = await Promise.all(filePromises);
            // Add all processed files to dataTransfer
            processedFiles.forEach(file => dataTransfer.items.add(file));
            // Update the input's files property
            input.files = dataTransfer.files;
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

function jsonParse(value) {
    try {
        return JSON.parse(value);
    } catch (e) {
        return [];
    }
}