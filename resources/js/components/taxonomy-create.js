function updateLabels() {
    const rankValue = parseInt(this.rankid);
    const highRankThreshold = 220;
    if (rankValue >= highRankThreshold) {
        this.unit1Label = "Genus";
        this.unit2Label = "Species";
    } else if (this.rankid) {
        const selectedRank = this.allTaxonRanks.find(
            (rank) => rank.rankid == this.rankid,
        );
        this.unit1Label = selectedRank ? selectedRank.rankname : "Genus";
    } else {
        this.unit1Label = "Genus";
        this.unit2Label = "Species";
    }
}

function taxonomyCreateInit() {
    this.$nextTick(() => {
        console.log("deleteMe got here a0");
        // manually adding listeners after rankid is rendered worked when referencing the onChange in the select component did not. Also tried $watch but that did not work either.
        const selectEl = document.getElementById("rankid");
        if (selectEl) {
            selectEl.addEventListener("change", (e) => {
                this.rankid = e.target.value;
                this.updateLabels();
            });
            selectEl.addEventListener("input", (e) => {
                this.rankid = e.target.value;
                this.updateLabels();
            });
        }

        const parentNameEl = document.getElementById("parentname");
        if (parentNameEl) {
            console.log("deleteMe got here a1");
            parentNameEl.addEventListener("change", (e) => {
                const selectedId = e.detail.selection.id;
                console.log("Selected parent taxon ID:", selectedId);
            });
        }
    });
}

window.taxonomyCreateInit = taxonomyCreateInit;
window.updateLabels = updateLabels;
