function updateLabels(alpineData) {
    console.log("updateLabels called! rankid:", alpineData?.rankid);
    
    // Defensive check to ensure alpineData exists
    if (!alpineData) {
        return;
    }

    const rankValue = parseInt(alpineData.rankid);
    const highRankThreshold = 220;
    
    if (rankValue >= highRankThreshold) {
        alpineData.unit1Label = "Genus";
        alpineData.unit2Label = "Species";
        console.log("Set labels for high rank (>=220): Genus/Species");
    } else if (alpineData.rankid && alpineData.allTaxonRanks && Array.isArray(alpineData.allTaxonRanks)) {
        const selectedRank = alpineData.allTaxonRanks.find(
            (rank) => rank.rankid == alpineData.rankid,
        );
        alpineData.unit1Label = selectedRank ? selectedRank.rankname : "Genus";
        console.log("Set label for selected rank:", selectedRank?.rankname || "Genus");
    } else {
        // Default fallback
        alpineData.unit1Label = "Genus"; 
        alpineData.unit2Label = "Species";
        console.log("Using fallback: Genus/Species");
    }
}

function taxonomyCreateInit() {
    // Store reference to Alpine.js context
    const alpineContext = this;
    
    // Use queueMicrotask to ensure DOM elements are ready
    queueMicrotask(() => {
        const parentNameEl = document.getElementById("parentname");
        if (parentNameEl) {
            parentNameEl.addEventListener("change", (e) => {
                const selectedId = e.detail.selection.id;
                console.log("Selected parent taxon ID:", selectedId);
            });
        }
    });
}

window.taxonomyCreateInit = taxonomyCreateInit;
window.updateLabels = updateLabels;

window.taxonomyCreateInit = taxonomyCreateInit;
window.updateLabels = updateLabels;
