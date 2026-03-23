function updateLabels(alpineData) {
    console.log("updateLabels called! rankid:", alpineData?.rankid);

    // Defensive check to ensure alpineData exists
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
        console.log(
            `Set labels for high rank (>=${highRankThreshold}): ${defaultUnit1Label}/${defaultUnit2Label}`,
        );
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
        console.log(
            "Set label for selected rank:",
            selectedRank?.rankname || defaultUnit1Label,
        );
    } else {
        // Default fallback
        alpineData.unit1Label = defaultUnit1Label;
        alpineData.unit2Label = defaultUnit2Label;
        console.log(
            `Using fallback: ${defaultUnit1Label}/${defaultUnit2Label}`,
        );
    }
}

window.updateLabels = updateLabels;
