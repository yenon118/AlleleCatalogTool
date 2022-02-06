function processAccessionCounts(accessionCounts) {

    let splittedAccessionCounts = {};

    for (let i = 0; i < accessionCounts.length; i++) {
        // let positionArray = String(accessionCounts[i]['Position']).split(" ");
        // let genotypeWithDescriptionArray = String(accessionCounts[i]['Genotype_with_Description']).split(" ");
        // let genotypeArray = String(accessionCounts[i]['Genotype']).split(" ");
        // for (let j = 0; j < positionArray.length; j++) {
        //     accessionCounts[i][positionArray[j]] = genotypeWithDescriptionArray[j];
        // }
        if (!splittedAccessionCounts.hasOwnProperty(accessionCounts[i]['Gene'])) {
            splittedAccessionCounts[accessionCounts[i]['Gene']] = [];
            splittedAccessionCounts[accessionCounts[i]['Gene']].push(accessionCounts[i]);
        } else {
            splittedAccessionCounts[accessionCounts[i]['Gene']].push(accessionCounts[i]);
        }
    }

    return splittedAccessionCounts;
}