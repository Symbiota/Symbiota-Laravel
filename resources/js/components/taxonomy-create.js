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

async function checkNameExistence(sciname, rankid, author = "") {
    console.log("deleteMe a1 Checking name existence for:", {
        sciname,
        rankid,
        author,
    });
    if (!sciname || !rankid) {
        return false;
    }
    const params = new URLSearchParams({ sciname, rankid });
    if (author) {
        params.set("author", author);
    }
    const response = await fetch(`/api/v3/taxonomy/check-exists?${params}`);
    console.log("deleteMe a2 API response is: ");
    console.log(response);
    const data = await response.json();
    console.log("deleteMe a3 API response data is: ");
    console.log(data);
    return data.exists;
}

async function validateTaxonForm(alpineData) {
    console.log("Validating taxon form...");
    let message = "";
    const parenttid = document.querySelector('[name="parenttid"]');
    const unitname1 = document.querySelector('[name="unitname1"]');
    const unitname2 = document.querySelector('[name="unitname2"]');
    const unit2namevisible =
        document.getElementById("unit2").style.display !== "none";
    const unitname3 = document.querySelector('[name="unitname3"]');
    const unit3namevisible =
        document.getElementById("unit3").style.display !== "none";
    const rankid = document.querySelector('[name="rankid"]');
    const author = document.querySelector('[name="author"]');

    if (!unitname1?.value) {
        message = "Missing required field: " + alpineData.unit1Label + " Name";
        return { isValid: false, message: message };
    }
    if (unit2namevisible && !unitname2?.value) {
        message = "Missing required field: " + alpineData.unit2Label + " Name";
        return { isValid: false, message: message };
    }
    if (unit3namevisible && !unitname3?.value) {
        message = "Missing required field: Infraspecific Epithet Name";
        return { isValid: false, message: message };
    }
    if (!parenttid?.value) {
        message =
            "Parent taxon is not valid. Make sure to select a parent taxon from the dropdown.";
        return { isValid: false, message: message };
    }
    const sciName = (
        unitname1.value +
        " " +
        unitname2.value +
        " " +
        unitname3.value
    ).trim();
    const exists = await checkNameExistence(
        sciName,
        rankid?.value,
        author?.value,
    );
    if (exists) {
        message = sciName + " already exists in the taxonomy";
        return { isValid: false, message: message };
    }
    return { isValid: true, message: "" };
}

window.updateLabels = updateLabels;
window.validateTaxonForm = validateTaxonForm;
