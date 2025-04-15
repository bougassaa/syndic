document.addEventListener('DOMContentLoaded', function () {
    initTooltip();
    initOwnerSwitch();
    initSelect();
    initDatepicker();
    initPhoneInput();
    initDynamicModal();
    new ClipboardJS('.clipboard');

    document.querySelectorAll('input.text-uppercase').forEach(input => {
        input.addEventListener('blur', function (event) {
            event.target.value = event.target.value.toUpperCase()
        })
    });

    document.querySelectorAll('select.cotisationAppartement').forEach(select => {
        const proprietaireSelect = document.querySelector('#cotisation_proprietaire');
        const appartementsMapping = jsonParse(document.querySelector('#appartementsMapping').value);
        select.addEventListener('change', () => {
            const appartementKey = select.value;
            const proprietaires = appartementsMapping[appartementKey];

            proprietaireSelect.tomselect.clear();
            proprietaireSelect.tomselect.clearOptions();
            proprietaireSelect.tomselect.addOption(proprietaires);
        })
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

    document.querySelectorAll('.list-filter-form, .change-lang').forEach(form => {
        form.addEventListener('input', function (event) {
            if (event.target.name === 'filterPeriode') {
                const monthInput = document.querySelector('[name="filterMonth"]');
                if (monthInput instanceof HTMLSelectElement) {
                    monthInput.value = "";
                }
            }
            form.submit();
        });
    });

    document.querySelectorAll('#addAppartement').forEach(button => {
        const wrapper = document.getElementById('proprietaire_possessions');
        const prototype = wrapper.dataset.prototype;
        let index = wrapper.children.length;

        button.addEventListener('click', function () {
            let element = prototype.replace(/__name__/g, index++);
            element = createElementFromString(element);
            wrapper.appendChild(element);
            initOwnerSwitch(element);
            initSelect(element);
            initDatepicker(element);
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

    pdfjsLib.GlobalWorkerOptions.workerSrc = '/public/pdfjs/pdf.worker.min.js';

    document.querySelectorAll('input[type=file]').forEach(input => {
        input.addEventListener('change', async e => {
            const sumbitButton = document.querySelector('form button[type="submit"]');
            const files = e.target.files;
            const dataTransfer = new DataTransfer();

            loading(sumbitButton, true);

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
            const uploadedFiles = processedFiles.flat();
            // Add all processed files to dataTransfer
            uploadedFiles.forEach(file => {
                dataTransfer.items.add(file)
            });
            // Update the input's files property
            input.files = dataTransfer.files;

            const preuveMore = document.querySelector('#preuve-preview-upload');
            if (preuveMore) {
                preuveMore.classList.remove('d-none');
                preuveMore.textContent = "+" + uploadedFiles.length;
            }

            loading(sumbitButton, false);
        });
    });

    // for onboarding step 1
    document.querySelectorAll('.onboarding__batiment').forEach(input => {
        input.addEventListener('change', () => {
            const radio = document.querySelector('[name="batiment"]:checked');
            if (radio instanceof HTMLInputElement && radio.value) {
                const appartements = jsonParse(radio.dataset.appartements);
                const select = document.querySelector('[name="appartement"]');
                const submit = document.querySelector('.btn[type="submit"]');
                const label = document.querySelector('#onboarding-appartement__label');
                label.classList.remove('opacity-0');
                select.tomselect.setDisabled(false);
                select.tomselect.clear();
                select.tomselect.clearOptions();
                select.tomselect.addOptions(appartements);
                select.addEventListener('change', () => {
                    submit.disabled = !select.tomselect.getValue();
                });
            }
        });
    });
});

function initOwnerSwitch(parent = document) {
    const leaveAtVisibility = (input) => {
        input.closest('.card-body')
            .querySelectorAll('.leaveAt')
            .forEach(leaveAtInput => {
                const leaveAtParent = leaveAtInput.parentElement;
                if (input.checked) {
                    leaveAtParent.classList.add('d-none');
                    leaveAtInput.required = false;
                    leaveAtInput.value = "";
                } else {
                    leaveAtParent.classList.remove('d-none');
                    leaveAtInput.required = true;
                }
            });
    }

    parent.querySelectorAll('input.isCurrentOwner').forEach(input => {
        leaveAtVisibility(input);

        input.addEventListener('change', function (event) {
            leaveAtVisibility(input);
        })
    });

    const existingPreuvesInput = document.querySelector('.existingPreuves');

    document.querySelectorAll('.remove-preuve').forEach(button => {
        button.addEventListener('click', () => {
            const image = button.getAttribute('data-image');
            const currentPreuves = jsonParse(existingPreuvesInput.value);
            const updatedPreuves = currentPreuves.filter(preuve => preuve !== image);

            existingPreuvesInput.value = JSON.stringify(updatedPreuves);

            // Supprimer l'aperçu visuellement
            button.closest('.preuve-preview').remove();
        });
    });
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

function initTooltip(parent = document) {
    parent.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
        new bootstrap.Tooltip(element)
    });
}

function initSelect(parent = document) {
    parent.querySelectorAll('select').forEach(select => {
        const options = {
            maxOptions: 300,
            render: {
                option: function(data, escape) {
                    return `<div class="d-flex align-items-center gap-2">
                        <span>${escape(data.text)}</span>
                        ${data.description ? `<small class="text-muted">${escape(data.description)}</small>` : ``}
                    </div>`;
                }
            }
        };

        if (select.hasAttribute('data-select-no-search')) {
            options.controlInput = null;
        }

        if (select.hasAttribute('data-select-allow-empty')) {
            options.allowEmptyOption = true;
        }

        if (select.hasAttribute('data-select-icon')) {
            options.render = {
                item: function(data, escape) {
                    return `<div class="gap-2">
                        <i class="${select.getAttribute('data-select-icon')} fs-4"></i>
                        <strong>${escape(data.text)}</strong>
                    </div>`;
                }, ...options.render
            }
        }

        if (select.hasAttribute('data-select-label')) {
            options.render = {
                item: function(data, escape) {
                    return `<div>
                        ${select.getAttribute('data-select-label')}
                        <strong>${escape(data.text)}</strong>
                    </div>`;
                }, ...options.render
            }
        }

        new TomSelect(select, options);
    });
}

function initDatepicker(parent = document) {
    parent.querySelectorAll('input[type="date"]').forEach(date => {
        date.addEventListener('focus', function (event) {
            if (event.target.showPicker) {
                event.target.showPicker();
            }
        });
    });
}

/**
 * @param {HTMLElement} element
 * @param {boolean} load
 */
function loading(element, load) {
    if (load) {
        const spinner = createElementFromString('<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>')
        element.setAttribute('disabled', 'disabled');
        element.prepend(spinner);
    } else {
        element.removeAttribute('disabled');
        element.querySelectorAll('.spinner-border').forEach(el => el.remove());
    }
}

function initPhoneInput() {
    const input = document.querySelector('[type="tel"]');
    if (input instanceof HTMLInputElement) {
        const countries = ['ma', 'fr', 'es', 'be', 'gb', 'it', 'de', 'ch', 'nl', 'pt'];
        const iti = window.intlTelInput(input, {
            containerClass: 'd-block',
            initialCountry: 'ma',
            separateDialCode: true,
            nationalMode: true,
            countrySearch: false,
            countryOrder: countries,
            onlyCountries: countries,
            i18n: {
                ma: "Maroc",
                fr: "France",
                es: "Espagne",
                be: "Belgique",
                gb: "Royaume-Uni",
                it: "Italie",
                de: "Allemagne",
                ch: "Suisse",
                nl: "Pays-Bas",
                pt: "Portugal"
            },
            loadUtils: () => import("/intl-tel-input/utils.js"),
        });

        const form = input.closest('form');
        form.addEventListener('submit', () => {
            input.value = iti.getNumber();
        });

        input.addEventListener('input', (event) => {
            if (event.isTrusted) {
                let phoneNumber = input.value.trim();
                if (/^00\d+/.test(phoneNumber)) {
                    phoneNumber = '+' + phoneNumber.slice(2);
                    iti.setNumber(phoneNumber);
                } else if (/^0\d+/.test(phoneNumber)) {
                    phoneNumber = phoneNumber.slice(1);
                    iti.setNumber(phoneNumber);
                }
            }
        });
    }
}

function initDynamicModal(parent = document, parentModal = null) {
    // used for all [data-url]
    parent.querySelectorAll('[data-url]').forEach(el => {
        el.addEventListener('click', async (event) => {
            const targetUrl = event.target && event.target.dataset.url;

            if (!targetUrl && event.target.closest('.dropdown') instanceof HTMLElement) {
                return;
            }

            if (!targetUrl && event.target.closest('a')) {
                return;
            }

            if (parentModal !== null) {
                parentModal.hide();
            }

            event.preventDefault();
            event.stopPropagation();

            const response = await fetch(targetUrl || el.dataset.url);
            const data = await response.text();
            const modalElement = createElementFromString(data);
            document.body.appendChild(modalElement);

            const modal = new bootstrap.Modal(modalElement); // Créer l'instance de modal
            modal.show(); // Afficher la modal

            modalElement.addEventListener('shown.bs.modal', () => {
                initTooltip(modalElement);
                initDynamicModal(modalElement, modal);
            })

            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.remove();
            })
        });
    });
}