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

window.updateLabels = updateLabels;
