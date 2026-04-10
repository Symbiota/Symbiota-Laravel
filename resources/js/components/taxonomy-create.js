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

async function checkNameExistence(sciname, rankid, author = "", preExistingTaxonInfo = null) {
    const isTheSameAsPreExistingTaxon = preExistingTaxonInfo && preExistingTaxonInfo.sciname === sciname && preExistingTaxonInfo.rankid == rankid && preExistingTaxonInfo.author == author;
    if (isTheSameAsPreExistingTaxon) {
        return false;
    }
    if (!sciname || !rankid) { // editing unit names will be disabled in edit mode, so we should skip the existence check in that case since the sciname will already exist in the database
        return false;
    }
    const params = new URLSearchParams({ sciname, rankid });
    if (author) {
        params.set("author", author);
    }
    const response = await fetch(`/api/v3/taxonomy/check-exists?${params}`);
    const data = await response.json();
    return data.exists;
}

async function validateTaxonForm(alpineData, preExistingTaxonInfo = null) {
    let message = "";
    const parenttid = document.querySelector('[name="parenttid"]');
    const unitname1 = document.querySelector('[name="unitname1"]');
    const unitname2 = document.querySelector('[name="unitname2"]');
    const unit2namevisible =
        !alpineData.rankid || parseInt(alpineData.rankid) >= 220;
    const unitname3 = document.querySelector('[name="unitname3"]');
    const unit3namevisible =
        !!(alpineData.rankid && parseInt(alpineData.rankid) >= 230);
    const cultivarEpithetVisible =
        !!(alpineData.rankid && parseInt(alpineData.rankid) >= 300);
    const cultivarEpithet = document.querySelector('[name="cultivarEpithet"]');

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
    if (cultivarEpithetVisible && !cultivarEpithet?.value) {
        message = "Missing required field: Cultivar Epithet";
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
        alpineData.rankid,
        alpineData.author,
        preExistingTaxonInfo
    );
    if (exists) {
        message = sciName + " already exists in the database.";
        return { isValid: false, message: message };
    }
    return { isValid: true, message: "" };
}

const standardizeCultivarEpithet = (unstandardizedCultivarEpithet) => {
    if (unstandardizedCultivarEpithet) {
        const cleanString = unstandardizedCultivarEpithet.replace(
            /(^['"“”]+)|(['"“”]+$)/g,
            "",
        );
        return "'" + cleanString + "'";
    } else {
        return "";
    }
};

const standardizeTradeName = (unstandardizedTradeName) => {
    if (unstandardizedTradeName) {
        return unstandardizedTradeName.toUpperCase();
    } else {
        return "";
    }
};

function updateScinameDisplay() {
    const unitind1 = document.querySelector('[name="unitind1"]').value;
    const unitname1 = document.querySelector('[name="unitname1"]').value;

    const unitind2 = document.querySelector('[name="unitind2"]').value;
    const unitname2 = document.querySelector('[name="unitname2"]').value;
    // const unit2namevisible =
    //     document.getElementById("unit2").style.display !== "none";

    const unitname3 = document.querySelector('[name="unitname3"]').value;
    const unitind3 = document.querySelector('[name="unitind3"]').value;
    const cultivarEpithet = document.querySelector(
        '[name="cultivarEpithet"]',
    ).value;
    const tradeName = document.querySelector('[name="tradeName"]').value;
    // const unit3namevisible =
    //     document.getElementById("unit3").style.display !== "none";
    // const rankid = document.querySelector('[name="rankid"]');
    // const author = document.querySelector('[name="author"]');
    let sciname = unitind1 + unitname1 + " " + unitind2 + unitname2 + " ";
    if (unitname3) {
        sciname = sciname + (unitind3 + " " + unitname3).trim();
    }
    if (cultivarEpithet) {
        sciname += " " + standardizeCultivarEpithet(cultivarEpithet);
    }
    if (tradeName) {
        sciname += " " + standardizeTradeName(tradeName);
    }
    const target = document.getElementById("sciname-preview");
    target.textContent = sciname.trim();
    // return sciname;
}

async function parseName() { //lightly modified from original function in old codebase
    const taxonForm = document.getElementById("taxon-form");
    if (!taxonForm.quickparser.value) {
        return;
    }
    let sciNameInput = taxonForm.quickparser.value;
    sciNameInput = sciNameInput.trim();
    taxonForm.reset();
    const sciNameArr = sciNameInput.split(" ");
    let activeIndex = 0;
    let rankId = "";
    const isGenericHybridOrExtinct =
        sciNameArr.length > 0 && sciNameArr[activeIndex].length == 1;
    if (isGenericHybridOrExtinct) {
        const unitind1El = document.getElementById("unitind1");
        if (unitind1El) {
            unitind1El.value = sciNameArr[activeIndex];
            if (sciNameArr[activeIndex].toLowerCase() == "x") {
                unitind1El.selectedIndex = 1; // @TODO - this is pretty brittle, should use value instead of index, but following the pattern established in the old codebase
            } else if (sciNameArr[activeIndex].toLowerCase() == "†") {
                unitind1El.selectedIndex = 2; // @TODO - this is pretty brittle, should use value instead of index, but following the pattern established in the old codebase
            }
        }
        activeIndex += 1;
    }
    taxonForm.unitname1.value = sciNameArr[activeIndex];
    activeIndex += 1;
    if (sciNameArr.length > activeIndex) {
        const isHybrid = sciNameArr[activeIndex].length == 1;
        if (isHybrid) {
            if (sciNameArr[activeIndex].toLowerCase() == "x") {
                const unitind2El = document.getElementById("unitind2");
                if (unitind2El) unitind2El.selectedIndex = 1; // @TODO - this is pretty brittle, should use value instead of index, but following the pattern established in the old codebase
            }
            activeIndex += 1;
        }
        if (
            sciNameArr[activeIndex]?.substring(0, 1) == "(" &&
            sciNameArr[activeIndex]?.substring(
                sciNameArr[activeIndex].length - 1,
            ) == ")"
        ) {
            taxonForm.unitname1.value =
                taxonForm.unitname1.value + " " + sciNameArr[activeIndex];
            activeIndex = activeIndex + 1;
            rankId = 190;
        }
        if (sciNameArr.length > activeIndex) {
            taxonForm.unitname2.value = sciNameArr[activeIndex];
        }
        activeIndex = activeIndex + 1;
    }
    if (sciNameArr.length > activeIndex) {
        let subjectUnit = sciNameArr[activeIndex];
        if (subjectUnit == "ssp.") subjectUnit = "subsp.";
        if (subjectUnit == "fo.") subjectUnit = "f.";
        if (
            subjectUnit == "subsp." ||
            subjectUnit == "var." ||
            subjectUnit == "f."
        ) {
            taxonForm.unitind3.value = subjectUnit;
            taxonForm.unitname3.value = sciNameArr[activeIndex + 1];
            activeIndex = activeIndex + 2;
        } else if (sciNameArr[activeIndex].length == 1) {
            taxonForm.unitind3.value = sciNameArr[activeIndex];
            activeIndex = activeIndex + 1;
            while (sciNameArr.length > activeIndex) {
                taxonForm.unitname3.value = (
                    taxonForm.unitname3.value +
                    " " +
                    sciNameArr[activeIndex]
                ).trim();
                activeIndex = activeIndex + 1;
            }
        } else {
            let firstChar = sciNameArr[activeIndex].substring(0, 1);
            if (firstChar != firstChar.toUpperCase()) {
                taxonForm.unitname3.value = sciNameArr[activeIndex];
                activeIndex = activeIndex + 1;
            }
        }
    }
    let author = "";
    while (sciNameArr.length > activeIndex) {
        //Place remaining taxon units into the author field
        author = author + " " + sciNameArr[activeIndex];
        activeIndex = activeIndex + 1;
    }
    taxonForm.author.value = author.trim();
    let unitName1 = taxonForm.unitname1.value;
    //If rankid is not set, determine rank
    if (taxonForm.unitname2.value == "") {
        if (rankId == "" && unitName1.length > 4) {
            if (
                unitName1.indexOf("aceae") == unitName1.length - 5 ||
                unitName1.indexOf("idae") == unitName1.length - 4
            ) {
                rankId = 140;
            } else if (
                unitName1.indexOf("oideae") == unitName1.length - 6 ||
                unitName1.indexOf("inae") == unitName1.length - 4
            ) {
                rankId = 150;
            } else if (unitName1.indexOf("ineae") == unitName1.length - 5) {
                rankId = 110;
            } else if (unitName1.indexOf("ales") == unitName1.length - 4) {
                rankId = 100;
            }
        }
    } else {
        rankId = 220;
        if (taxonForm.unitname3.value != "") {
            rankId = 230;
            if (taxonForm.unitind3.value == "var.") rankId = 240;
            else if (taxonForm.unitind3.value == "f.") rankId = 260;
            else if (taxonForm.unitind3.value == "×") rankId = 220;
        }
    }
    //Deal with problematic subgeneric ranks
    let parentName = "";
    if (unitName1.indexOf("(") > -1) {
        if (
            unitName1.substring(0, 1) == "(" &&
            unitName1.substring(unitName1.length - 1) == ")"
        ) {
            unitName1 =
                unitName1.substring(1, unitName1.length - 1) + " " + unitName1;
            taxonForm.unitname1.value = unitName1;
            rankId = 190;
        }
        if (rankId == 190) {
            parentName = unitName1.substring(0, unitName1.indexOf("(")).trim();
        } else if (rankId > 190) {
            if (rankId == 220) parentName = unitName1;
            taxonForm.unitname1.value = unitName1
                .substring(0, unitName1.indexOf("("))
                .trim();
        }
    }
    const rankidEl = document.getElementById("rankid");
    if (rankidEl) rankidEl.value = rankId;
    if (unitName1.substring(0, 1) == "×" || unitName1.substring(0, 1) == "†") {
        const unitind1El = document.getElementById("unitind1");
        if (!unitind1El?.value) {
            if (unitName1.substring(0, 1) == "×") unitind1El.selectedIndex = 1;
            if (unitName1.substring(0, 1) == "†") unitind1El.selectedIndex = 2;
        }
        taxonForm.unitname1.value = taxonForm.unitname1.value.substring(1);
    }
    if (taxonForm.unitname2.value.substring(0, 1) == "×") {
        const unitind2El = document.getElementById("unitind2");
        if (!unitind2El?.value) {
            if (taxonForm.unitname2.value.substring(0, 1) == "×")
                unitind2El.selectedIndex = 1;
        }
        taxonForm.unitname2.value = taxonForm.unitname2.value.substring(1);
    }
    if (parentName == "") {
        //Set parent name
        if (rankId > 180) {
            if (rankId == 220) parentName = taxonForm.unitname1.value;
            else if (rankId > 220)
                parentName =
                    taxonForm.unitname1.value + " " + taxonForm.unitname2.value;
        }
    }
    if (parentName !== "") {
        const taxaSearchInput = taxonForm.querySelector(
            'input#parentname[name="taxa"]',
        );
        if (taxaSearchInput) taxaSearchInput.value = parentName;

        try {
            const response = await fetch(
                `/api/taxa/search?taxa=${encodeURIComponent(parentName)}&format=json`,
            );
            const results = await response.json();
            const exactMatch = results.find((r) => r.sciname === parentName);
            const parentTidInput = document.querySelector("#tid-parentname");
            if (parentTidInput) parentTidInput.value = exactMatch?.tid ?? "";
        } catch (e) {
            console.error("Error looking up parent taxon:", e);
        }
    }
    taxonForm.quickparser.value = "";
}

window.updateLabels = updateLabels;
window.validateTaxonForm = validateTaxonForm;
window.updateScinameDisplay = updateScinameDisplay;
window.parseName = parseName;
