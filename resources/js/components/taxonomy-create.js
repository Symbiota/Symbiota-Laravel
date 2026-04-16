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

async function checkNameExistence(
    sciname,
    rankid,
    author = "",
    preExistingTaxonInfo = null,
) {
    console.log("deleteMe preExistingTaxonInfo in checkNameExistence is: ");
    console.log(preExistingTaxonInfo);
    console.log("deleteMe sciname in checkNameExistence is: " + sciname);
    console.log("deleteMe rankid in checkNameExistence is: " + rankid);
    console.log("deleteMe author in checkNameExistence is: " + author);
    const isTheSameAsPreExistingTaxon =
        preExistingTaxonInfo &&
        preExistingTaxonInfo.sciName === sciname &&
        (preExistingTaxonInfo.rankid ?? preExistingTaxonInfo.rankID) ==
            rankid &&
        preExistingTaxonInfo.author == author;
    console.log("deleteMe isTheSameAsPreExistingTaxon is: ");
    console.log(isTheSameAsPreExistingTaxon);

    if (isTheSameAsPreExistingTaxon) {
        return false;
    }
    if (!sciname || !rankid) {
        // editing unit names will be disabled in edit mode, so we should skip the existence check in that case since the sciname will already exist in the database
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

async function verifyLoadFormCore(
    alpineData,
    silent = false,
    preExistingTaxonInfo = null,
    sciNameRankRequiredMessage = null,
    alreadyExistsMessage = null,
    parentTaxonRequiredMessage = null,
    parentIdNotSetMessage = null,
    accNameNeedsValueMessage = null
) {
    console.log("deleteMe verifyLoadFormCore entered and alpineData is: ");
    const { rankid, unit1Label, unit2Label, isValid, validationMessage } = alpineData;
    console.log({ rankid, unit1Label, unit2Label, isValid, validationMessage });
    console.log("deleteMe and preExistingTaxonInfo is: ");
    console.log(preExistingTaxonInfo);
    const entryHasNotChanged = isTheSameEntryAsItStarted(preExistingTaxonInfo);
    console.log("deleteMe entryHasNotChanged is: " + entryHasNotChanged);
    if (entryHasNotChanged) {
        return { isValid: true, message: "" };
    }
    const unitname1 = document.querySelector('[name="unitname1"]');
    console.log("deleteMe unitname1 in verifyLoadFormCore is: ");
    console.log(unitname1);
    if (!unitname1?.value) {
        const msg = sciNameRankRequiredMessage;
        if (!silent) alert(msg);
        setErrorDisplay(msg);
        return { isValid: false, message: msg };
    }
    if (!alpineData.rankid) {
        const msg = sciNameRankRequiredMessage;
        if (!silent) alert(msg);
        setErrorDisplay(msg);
        return { isValid: false, message: msg };
    }
    const unit2nameIsRequired = alpineData.rankid >= 220;
    if(unit2nameIsRequired){
        const unit2nameIsMissing = !document.querySelector('[name="unitname2"]')?.value;
        if(unit2nameIsMissing){
            const msg = sciNameRankRequiredMessage;
            if (!silent) alert(msg);
            setErrorDisplay(msg);
            return { isValid: false, message: msg };
        }
    }

    const unit3nameIsRequired = alpineData.rankid >= 230;
    if(unit3nameIsRequired){
        const unit3nameIsMissing = !document.querySelector('[name="unitname3"]')?.value;
        if(unit3nameIsMissing){
            const msg = sciNameRankRequiredMessage;
            if (!silent) alert(msg);
            setErrorDisplay(msg);
            return { isValid: false, message: msg };
        }
    }
    
    const unitname3 = document.querySelector('[name="unitname3"]')?.value;
    console.log("deleteMe unitname3 in verifyLoadFormCore is: ");
    console.log(unitname3);
    const sciName = (
        (unitname1?.value || "") +
        " " +
        (unitname2?.value || "") +
        " " +
        (unitname3?.value || "")
    ).trim();
    console.log("deleteMe sciName in verifyLoadFormCore is: ");
    console.log(sciName);
    const exists = await checkNameExistence(
        sciName,
        alpineData.rankid,
        alpineData.author,
        preExistingTaxonInfo,
    );
    console.log("deleteMe exists in verifyLoadFormCore is: " + exists);
    if (exists) {
        const msg = sciName + alreadyExistsMessage;
        setErrorDisplay(msg);
        return { isValid: false, message: msg };
    }
    return { isValid: true, message: "" };
}

async function validateTaxonForm(
    alpineData,
    preExistingTaxonInfo = null,
    alreadyExistsMessage = null,
) {
    console.log("deleteMe validateTaxonForm entered and alpineData is: ");
    console.log(alpineData);
    console.log(JSON.parse(JSON.stringify(alpineData)));
    if (preExistingTaxonInfo) {
        return validateTaxonEditForm(preExistingTaxonInfo, alpineData);
    }
    console.log("deleteMe SHOULD NOT GET HERE IN THE TAXONOMY EDITOR");
    console.log("deleteMe preExistingTaxonInfo is: ");
    console.log(preExistingTaxonInfo);

    let message = "";
    const parenttid = document.querySelector('[name="parenttid"]');
    const unitname1 = document.querySelector('[name="unitname1"]');
    const unitname2 = document.querySelector('[name="unitname2"]');
    const unit2namevisible =
        !alpineData.rankid || parseInt(alpineData.rankid) >= 220;
    const unitname3 = document.querySelector('[name="unitname3"]');
    const unit3namevisible = !!(
        alpineData.rankid && parseInt(alpineData.rankid) >= 230
    );
    const cultivarEpithetVisible = !!(
        alpineData.rankid && parseInt(alpineData.rankid) >= 300
    );
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
    console.log("deleteMe sciName in validateTaxonForm is: ");
    console.log(sciName);
    const exists = await checkNameExistence(
        sciName,
        alpineData.rankid,
        alpineData.author,
        preExistingTaxonInfo,
    );
    if (exists) {
        message = sciName + alreadyExistsMessage;
        return { isValid: false, message: message };
    }
    return { isValid: true, message: "" };
}

async function verifyLoadForm(
    alpineData,
    silent = false,
    preExistingTaxonInfo = null,
    sciNameRankRequiredMessage,
    alreadyExistsMessage,
    parentTaxonRequiredMessage,
    parentIdNotSetMessage,
    accNameNeedsValueMessage
) {
    const coreResult = await verifyLoadFormCore(
        alpineData,
        true,
        preExistingTaxonInfo,
        sciNameRankRequiredMessage,
        alreadyExistsMessage,
        parentTaxonRequiredMessage,
        parentIdNotSetMessage,
        accNameNeedsValueMessage
    );
    if (!coreResult.isValid) {
        return coreResult;
    }
    return validateFormInput(
        alpineData,
        silent,
        sciNameRankRequiredMessage,
        parentTaxonRequiredMessage,
        parentIdNotSetMessage,
        accNameNeedsValueMessage
    );
}

function validateFormInput(
    alpineData,
    silent = false,
    sciNameRankRequiredMessage = null,
    parentTaxonRequiredMessage = null,
    parentIdNotSetMessage = null,
    accNameNeedsValueMessage = null
) {
    const rankId = alpineData.rankid;
    const unitname1 = document.querySelector('[name="unitname1"]');
    if (!unitname1?.value) {
        if (!silent) alert(sciNameRankRequiredMessage);
        setErrorDisplay(sciNameRankRequiredMessage);
        return { isValid: false, message: sciNameRankRequiredMessage };
    }
    const parentname = document.querySelector('[name="parentname"]');
    if (!parentname?.value && rankId > "10") {
        if (!silent) alert(parentTaxonRequiredMessage);
        setErrorDisplay(parentTaxonRequiredMessage);
        return { isValid: false, message: parentTaxonRequiredMessage };
    }
    const parenttid = document.querySelector('[name="parenttid"]');
    if (!parenttid?.value && rankId > "10") {
        if (!silent) alert(parentIdNotSetMessage);
        setErrorDisplay(parentIdNotSetMessage);
        return { isValid: false, message: parentIdNotSetMessage };
    }
    const notes = document.querySelector('[name="notes"]');
    const source = document.querySelector('[name="source"]');
    if (
        !validateFieldLength(notes, 250, silent) ||
        !validateFieldLength(source, 250, silent)
    )
        return { isValid: false, message: "" };

    //If name is not accepted, verify accepted name
    const accStatusObj = document.querySelectorAll('[name="acceptstatus"]');
    if (accStatusObj[0]?.checked === false) {
        const acceptedstr = document.querySelector('[name="acceptedstr"]');
        if (!acceptedstr?.value) {
            if (!silent) alert(accNameNeedsValueMessage);
            setErrorDisplay(accNameNeedsValueMessage);
            return {
                isValid: false,
                message: accNameNeedsValueMessage,
            };
        }
    }
    return { isValid: true, message: "" };
}

async function validateTaxonEditForm(
    alpineData,
    silent = false,
    preExistingTaxonInfo,
    sciNameRankRequiredMessage,
    alreadyExistsMessage,
    parentTaxonRequiredMessage = null,
    parentIdNotSetMessage = null,
    accNameNeedsValueMessage = null
) {
    return verifyLoadFormCore(
        alpineData,
        silent,
        preExistingTaxonInfo,
        sciNameRankRequiredMessage,
        alreadyExistsMessage,
        parentTaxonRequiredMessage,
        parentIdNotSetMessage,
        accNameNeedsValueMessage
    );
}

function isTheSameEntryAsItStarted(preExistingTaxonInfo) {
    if (!preExistingTaxonInfo) {
        return false;
    }
    const currentForm = document.getElementById("taxon-form");
    if (!currentForm) {
        return false;
    }

    const getFieldValue = (name) => {
        const el = currentForm.querySelector(`[name="${name}"]`);
        return el ? el.value : "";
    };

    const originalAcceptStatus =
        preExistingTaxonInfo.tid == preExistingTaxonInfo.tidaccepted
            ? "1"
            : "0";
    const acceptStatusEl = currentForm.querySelector(
        '[name="acceptstatus"]:checked',
    );
    const currentAcceptStatus = acceptStatusEl ? acceptStatusEl.value : "1";

    const fieldMatches = [
        getFieldValue("rankid") ==
            (preExistingTaxonInfo.rankID ?? preExistingTaxonInfo.rankid ?? ""),
        getFieldValue("unitind1") == (preExistingTaxonInfo.unitInd1 ?? ""),
        getFieldValue("unitname1") == (preExistingTaxonInfo.unitName1 ?? ""),
        getFieldValue("unitind2") == (preExistingTaxonInfo.unitInd2 ?? ""),
        getFieldValue("unitname2") == (preExistingTaxonInfo.unitName2 ?? ""),
        getFieldValue("unitind3") == (preExistingTaxonInfo.unitInd3 ?? ""),
        getFieldValue("unitname3") == (preExistingTaxonInfo.unitName3 ?? ""),
        getFieldValue("cultivarEpithet") ==
            (preExistingTaxonInfo.cultivarEpithet ?? ""),
        getFieldValue("tradeName") == (preExistingTaxonInfo.tradeName ?? ""),
        getFieldValue("author") == (preExistingTaxonInfo.author ?? ""),
        getFieldValue("parenttid") == (preExistingTaxonInfo.parenttid ?? ""),
        getFieldValue("notes") == (preExistingTaxonInfo.notes ?? ""),
        getFieldValue("source") == (preExistingTaxonInfo.source ?? ""),
        getFieldValue("securitystatus") ==
            (preExistingTaxonInfo.securityStatus ?? "0"),
        currentAcceptStatus == originalAcceptStatus,
        getFieldValue("tidaccepted") ==
            (preExistingTaxonInfo.tidaccepted ?? ""),
        getFieldValue("unacceptabilityreason") ==
            (preExistingTaxonInfo.UnacceptabilityReason ?? ""),
    ];

    const isSame = fieldMatches.every(Boolean);
    if (isSame) {
        setErrorDisplay("");
    }
    return isSame;
}

function processTextContent(content) {
    return content?.replace("undefined", "")?.trim();
}

function setErrorDisplay(_text) { // @TODO refactor this/ ensure that errors are already handled by Alpine
    // Validation messages are returned to Alpine's validate() which sets
    // this.validationMessage — driving x-text="validationMessage" on #validationMessage.
    // No direct DOM write needed here.
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

async function parseName() {
    //lightly modified from original function in old codebase
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
// window.validateTaxonForm = validateTaxonForm;
window.updateScinameDisplay = updateScinameDisplay;
window.parseName = parseName;
window.verifyLoadForm = verifyLoadForm;
window.validateTaxonEditForm = validateTaxonEditForm;
