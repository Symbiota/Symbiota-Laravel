function updateLabels(alpineData) {
    if (!alpineData) {
        return;
    }
    const rankValue = parseInt(alpineData.rankid);
    const highRankThreshold = 220;
    const defaultUnit1Label = "Genus";
    const defaultUnit2Label = "Species";

    if (rankValue >= highRankThreshold) {
        alpineData.unit1Label = defaultUnit1Label;
        alpineData.unit2Label = defaultUnit2Label;
    } else if (
        alpineData.rankid &&
        alpineData.allTaxonRanks &&
        Array.isArray(alpineData.allTaxonRanks)
    ) {
        const selectedRank = alpineData.allTaxonRanks.find(
            (rank) => rank.rankid == alpineData.rankid,
        );
        alpineData.unit1Label = selectedRank
            ? selectedRank.rankname
            : defaultUnit1Label;
    } else {
        alpineData.unit1Label = defaultUnit1Label;
        alpineData.unit2Label = defaultUnit2Label;
    }
}

function validateTaxonForm(alpineData) {
    console.log("Validating taxon form...");
    let message = "";
    const parenttid = document.querySelector('[name="parenttid"]');
    const unitname1 = document.querySelector('[name="unitname1"]');
    if (parenttid?.value && !unitname1?.value) {
        message = "Missing required field: " + alpineData.unit1Label;
    }
    if (!parenttid?.value) {
        message = "Parent taxon is not valid";
    }
    const isValid = !!(parenttid?.value && unitname1?.value);
    const validationObj = { isValid: isValid, message: message };
    console.log("Form validation result:");
    console.log(validationObj);
    return validationObj;
}

window.updateLabels = updateLabels;
window.validateTaxonForm = validateTaxonForm;
