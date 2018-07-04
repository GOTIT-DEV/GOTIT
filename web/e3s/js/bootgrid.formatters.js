function bgDetailsForm(column, row) { //"details"
    var template=$("#details-form-template").html();
    return Mustache.render(template, row);
}

function bgDateFormat(column,row) { //"date_format"
    return Date.parse(row.date_motu.date).toString('MMM yyyy') ;
}

function bgRoundFloat(column, row){ //"number_format"
    return parseFloat(row[column.id]).toFixed(3) ;
}

function bgNcbiLink(column, row) {
    return "<a href='https://www.ncbi.nlm.nih.gov/nuccore/" +
    row[column.id] + "'>" + row[column.id] + "</a>";
}

function bgTypeSeq(column,row){
    return (row[column.id] == 0) ? "Interne" : "Externe";
}
