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

    document.querySelectorAll('.row-clickable[data-url]').forEach(row => {
        row.addEventListener('click', async () => {
            const response = await fetch(row.dataset.url);
            const data = await response.text();
            const modalElement = createElementFromString(data);
            document.body.appendChild(modalElement);

            const modal = new bootstrap.Modal(modalElement); // Créer l'instance de modal
            modal.show(); // Afficher la modal
        });
    });

    pdfjsLib.GlobalWorkerOptions.workerSrc = '/public/pdfjs/pdf.worker.min.js';

    document.querySelectorAll('input[type=file]').forEach(input => {
        input.addEventListener('change', async e => {
            const files = e.target.files;
            const dataTransfer = new DataTransfer();

            const filePromises = Array.from(files).map(async file => {
                if (file.type.startsWith('image/')) {
                    return new Promise(resolve => {
                        new Compressor(file, {
                            quality: 0.7, // Compression quality
                            maxWidth: 1100, // Max width of compressed image
                            maxHeight: 800, // Max height of compressed image
                            success(result) {
                                const compressedFile = new File([result], file.name, { type: file.type });
                                resolve(compressedFile); // Resolve with the compressed file
                            },
                            error() {
                                resolve(file); // Resolve with the original file if compression fails
                            }
                        });
                    });
                } else if (file.type === 'application/pdf')  {
                    const pdfFiles = [];
                    const arrayBuffer = await file.arrayBuffer();
                    const pdfDoc = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;

                    for (let pageNumber = 1; pageNumber <= pdfDoc.numPages; pageNumber++) {
                        const page = await pdfDoc.getPage(pageNumber);
                        const viewport = page.getViewport({ scale: 1.2 }); // Adjust scale as needed
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');

                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        await page.render({ canvasContext: context, viewport }).promise;

                        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                        const pdfFile = new File([blob], `page-${pageNumber}.png`, { type: 'image/png' });
                        pdfFiles.push(pdfFile);
                    }
                    return Promise.resolve(pdfFiles);
                }
            });
            // Wait for all Promises to resolve
            const processedFiles = await Promise.all(filePromises);
            // Add all processed files to dataTransfer
            processedFiles.flat().forEach(file => {
                dataTransfer.items.add(file)
            });
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

function createElementFromString(string) {
    const div = document.createElement('div');
    div.innerHTML = string.trim();
    return div.firstElementChild;
}