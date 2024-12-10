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

    const appartementSelect = document.querySelector('#cotisation_appartement');
    const proprietaireSelect = document.querySelector('#cotisation_proprietaire');
    const appartementsMapping = document.querySelector('#appartementsMapping');

    if (appartementSelect && proprietaireSelect && appartementsMapping) {
        const mapping = JSON.parse(appartementsMapping.value);
        appartementSelect.addEventListener('change', function (event) {
            const proprietaire = getProprietaireFromAppartement(mapping, event.target.value);
            if (proprietaire) {
                proprietaireSelect.tomselect.setValue(proprietaire, true);
            } else {
                proprietaireSelect.tomselect.clear();
            }
        });
        proprietaireSelect.addEventListener('change', function (event) {
            const appartement = getAppartementFromProprietaire(mapping, event.target.value);
            if (appartement) {
                appartementSelect.tomselect.setValue(appartement, true);
            } else {
                appartementSelect.tomselect.clear();
            }
        });
    }
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

function getAppartementFromProprietaire(mapping, proprietaire) {
    for (const key in mapping) {
        if (mapping[key] == proprietaire) {
            return key;
        }
    }
    return null;
}

function getProprietaireFromAppartement(mapping, appartement) {
    for (const key in mapping) {
        if (key == appartement) {
            return mapping[key];
        }
    }
    return null;
}