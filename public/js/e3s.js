/*
 * This file is part of the E3sBundle from the GOTIT project (Gene, Occurence and Taxa in Integrative Taxonomy)
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Philippe Grison  <philippe.grison@mnhn.fr>
 * 
 */

/**
 * function addCollectionButtonsEmbed() : Manage form with two level of embed form
 * @param {String} formNameOfCollection : form name of the first level Collection to embed ex. bbees_e3sbundle_lotmateriel_especeIdentifiees
 * @param {String} nameCollection : name of the Entity relative to the first level Collection to embed ex. EspeceIdentifiee
 * @param {Boolean} addnew : to get the add new button in the embed form
 * @param {Boolean} fieldRequired :  if embed form value(s) is required
 * @param {String} nameArrayCollectionEmbed : Array name of the second level Collection to embed ex. estIdentifiePars
 * @param {String} nameCollectionEmbed : name of the Entity relative to the second level Collection to embed ex. Personne
 * @param {Boolean} addnewEmbed : to get the add new button in the second level embed form
 * @param {Boolean} fieldRequiredEmbed :  if the second level embed form value(s)is required
 */
function addCollectionButtonsEmbed(formNameOfCollection, nameCollection, addnew = false, fieldRequired = true, nameArrayCollectionEmbed = null, nameCollectionEmbed = null, addnewEmbed = false, fieldRequiredEmbed = true) {

  // define selector of div with the « data-prototype » of embed form    
  var $containerCollectionEmbed = $('div#' + formNameOfCollection);
  if ($containerCollectionEmbed.length !== 0) {
    $containerCollectionEmbed.prepend('</br></br>');

    // set the last index of the formNameOfCollection
    var index = getLastIndex(formNameOfCollection);

    // add button "add New nameCollection"     
    if (addnew) {
      var nameAddNewButon = "Add a new " + nameCollection;
      var $addBoutonAdd = $('<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal' + nameCollection + '">' + Translator.trans('button.' + nameAddNewButon) + '</button>');
      $containerCollectionEmbed.prepend($addBoutonAdd);
    }

    // add button "add nameCollection" 
    var nameAddButon = "Add a " + nameCollection;
    var $addLink = $('<span><a href="#" id="add_' + nameCollection + '" class="btn btn-primary btn-sm">' + Translator.trans('button.' + nameAddButon) + '</a></span>');
    $containerCollectionEmbed.prepend($addLink);

    // if the field is required display the embeded form
    if (index == 0 && fieldRequired) {
      addCategoryForExistingRecordEmbed2(index, $containerCollectionEmbed, false, nameCollection);
      var containerEmbed = formNameOfCollection + '_' + index.toString() + '_' + nameArrayCollectionEmbed;
      index++;
    }

    // add a new field each time you click on the add button
    $addLink.click(function (e) {
      addCategoryForExistingRecordEmbed2(index, $containerCollectionEmbed, true, nameCollection);
      var $containerEmbed = $(formNameOfCollection + '_' + index.toString() + '_' + nameArrayCollectionEmbed);
      var containerEmbed = formNameOfCollection + '_' + index.toString() + '_' + nameArrayCollectionEmbed;
      addCollectionButtons(containerEmbed, 'Personne', false);
      e.preventDefault(); // prevents a # from appearing in the URL
      index++;
      return false;
    });

    // For each collection we add a delete link & we add the buttons for the ArraycollectionEmbed
    var comptChildren = 0;
    var nbCollectionEmbed = $containerCollectionEmbed.children('div').length;
    $containerCollectionEmbed.children('div').each(function () {
      //alert("attribut Id="+$(this).find('[id$="_'+nameArrayCollectionEmbed+'"]').html());
      if (fieldRequired == false || comptChildren > 0) {
        addDeleteLink($(this), true, nameCollection);
      } else {
        addDeleteLink($(this), false, nameCollection);
      }
      var containerEstIdentifieePar = $(this).find('[id$="_' + nameArrayCollectionEmbed + '"]').attr('id');
      // alert($(this).find('[id$="_' + nameArrayCollectionEmbed + '"]').attr('id')); 
      //alert($(this).find('[id$="_' + nameArrayCollectionEmbed + '"]').html());
      addCollectionButtons(containerEstIdentifieePar, 'Personne', false);
      comptChildren++;
    });
  }

}

/**
 * function addCollectionButtons() : Manage form with one level of embed form
 * @param {String} formNameOfCollection : form name of the first level Collection to embed ex. bbees_e3sbundle_lotmateriel_especeIdentifiees
 * @param {String} nameCollection : name of the Entity relative to the first level Collection to embed ex. EspeceIdentifiee
 * @param {Boolean} addnew : to get the add new button in the embed form
 * @param {Boolean} fieldRequired :  if embed form value(s) is required
 */
function addCollectionButtons(formNameOfCollection, nameCollection, addnew = false, fieldRequired = true) {

  // define selector of div with the « data-prototype » of embed form     
  var $container = $('div#' + formNameOfCollection);
  if ($container.length !== 0) {
    $container.prepend('</br></br>');

    // set the last index of the formNameOfCollection
    var index = getLastIndex(formNameOfCollection);

    // add button "add New nameCollection" 
    if (addnew) {
      var nameAddNewButon = "Add a new " + nameCollection;
      var $addBoutonAdd = $('<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal' + nameCollection + '">' + Translator.trans('button.' + nameAddNewButon) + '</button>');
      $container.prepend($addBoutonAdd);
      $addBoutonAdd.click(function (e) {
        index++;
        e.preventDefault(); // prevents a # from appearing in the URL
        return true;
      });
    }

    // add button "add nameCollection" 
    var nameAddButon = "Add a " + nameCollection;
    var $addLink = $('<span><a href="#" id="add_' + nameCollection + '" class="btn btn-primary btn-sm">' + Translator.trans('button.' + nameAddButon) + '</a></span>');
    $container.prepend($addLink);

    // add a new field each time you click on the add button
    $addLink.click(function (e) {
      addCategoryForExistingRecord(index, $container, true, nameCollection);
      index++;
      e.preventDefault(); // prevents a # from appearing in the URL
      return false;
    });

    // if the field is required display the embeded form
    if (index == 0 && fieldRequired) {
      addCategoryForExistingRecord(index, $container, false, nameCollection);
      index++;
    }

    // For each collection we add a delete link
    var comptChildren = 0;
    $container.children('div').each(function () {
      if (fieldRequired == false || comptChildren > 0) {
        addDeleteLink($(this), true, nameCollection);
      } else {
        addDeleteLink($(this), false, nameCollection);
      }
      comptChildren++;
    });
  }

}

// function to add a delete link 
function addDeleteLink($prototype, visible = true, nameCollection = '') {
  // Create link
  var id_delete = (nameCollection != '') ? 'id="delete_' + nameCollection + '"' : 'id="delete_Collection"';
  if (visible) {
    var $deleteLink = $('<div ' + id_delete + ' class="col-sm-2 pull-right"><a href="#" class="btn btn-danger btn-sm " type="button">Delete</a></div>');
  } else {
    var $deleteLink = $('<div ' + id_delete + ' class="col-sm-2 pull-right">&nbsp;</div>');
  }
  $prototype.prepend($deleteLink);
  // Add  listener to the clic link
  $deleteLink.click(function (e) {
    $prototype.remove();
    e.preventDefault(); // prevents a # from appearing in the URL
    return false;
  });
}


/**
 * function addCategoryForExistingEmbedRecord() : add a form to a collectionEmbed
 * @param {Int} index : last index of nameArrayCollectionEmbed
 * @param {Object} $container : jquery container 
 * @param {String} nameArrayCollectionEmbed : name of the ArrayCollectionEmbed
 * @param {Boolean} deleteBouton : to get the delete button in the embed form
 */
function addCategoryForExistingEmbedRecord(index, $container, nameArrayCollectionEmbed, deleteBouton = true) {
  // Dans le contenu de l'attribut « data-prototype », on remplace :
  // - le texte "__name__label__" qu'il contient par le label du champ
  // - le texte "__name__" qu'il contient par le numéro du champ
  var $prototype = $($container.attr('data-prototype').replace('<label class="col-sm-2 col-form-label required">__name__label__</label>', '').replace(/__name__/g, index));
  // On ajoute au prototype un lien pour pouvoir supprimer 
  if (deleteBouton) addDeleteLink($prototype);
  // On ajoute le prototype modifié à la fin de la balise <div>
  var newindex = index + 1;
  var $prototypeEmbed = $($prototype.find('div[id$="' + nameArrayCollectionEmbed + '_0"]').html().replace(nameArrayCollectionEmbed + '_0', nameArrayCollectionEmbed + '_' + newindex).replace(nameArrayCollectionEmbed + '][0', nameArrayCollectionEmbed + '][' + newindex));
  // On ajoute le prototype modifié à la fin de la balise <div>
  $container.append($prototypeEmbed);
  // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
  return false;
}

/**
 * function addCategoryForExistingRecord() : add a form to a embeded collectionEmbed
 * @param {Int} index : last index 
 * @param {Object} $container : jquery container 
 * @param {String} nameCollection : name of the Collection Embed
 * @param {Boolean} deleteBouton : to get the delete button in the embed form
 */
function addCategoryForExistingRecord(index, $container, deleteBouton = true, nameCollection = '') {
  //alert("addCategoryForExistingRecord  : index ="+index);
  // In the content of the "data-prototype" attribute, we replace:
  // - the text "__name__label__" that it contains by the label of the field
  // - the text "__name__" that it contains by the number of the field
  var $prototype = $($container.attr('data-prototype').replace('<label class="col-sm-2 col-form-label required">__name__label__</label>', '').replace(/__name__/g, index));
  // We add to the prototype a link to delete
  if (deleteBouton) addDeleteLink($prototype, true, nameCollection);
  // Add the modified prototype to the end of the <div> tag
  //$container.append($prototype);
  $container.append($prototype);
  return false;
}


/**
 * function addCategoryForExistingRecordEmbed2() : add a form to a embeded collectionEmbed
 * @param {Int} index : last index 
 * @param {Object} $container : jquery container 
 * @param {String} nameCollection : name of the Collection Embed
 * @param {Boolean} deleteBouton : to get the delete button in the embed form
 */
function addCategoryForExistingRecordEmbed2(index, $container, deleteBouton = true, nameCollection = '') {
  // replace in the « data-prototype » :
  // - the text "__name__label__" by a blank . No label
  // - the  text "__name__" by the index
  switch (nameCollection) {
    case 'EspeceIdentifiee':
      var Regex1 = /especeIdentifiees___name__/g;
      var Regex2 = /especeIdentifiees\]\[__name__/g;
      break;
    default:
      alert("bad nameCollection in addCategoryForExistingRecordEmbed2()");
      return false;
  }
  var $prototype = $($container.attr('data-prototype').replace('<label class="col-sm-2 col-form-label required">__name__label__</label>', '').replace(Regex1, "especeIdentifiees_" + index).replace(Regex2, "especeIdentifiees][" + index));
  // alert($prototype.html());
  // We add to the prototype a link to delete 
  if (deleteBouton) addDeleteLink($prototype, true, nameCollection);
  // Add the modified prototype to the end of the <div> tag
  //$container.append($prototype);
  $container.append($prototype);
  return true;

}

//  function to add a embeded form
function addCategoryForNewRecord(index, $container, select_id, select_name) {
  // Dans le contenu de l'attribut « data-prototype », 
  // - on supprime les labels
  // - on modifie le texte "__name__" qu'il contient par le numéro du champ
  //var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, 'APourSamplingMethod :' + (index+1)).replace(/__name__/g, index));
  var $prototype = $($container.attr('data-prototype').replace('<label class="col-sm-2 col-form-label required">__name__label__</label>', '').replace(/__name__/g, index)
    .replace('</select>', '><option value="' + select_id + '" selected="selected">' + select_name + '</option></select>'));
  // We add to the prototype a link to delete 
  addDeleteLink($prototype);
  // Add the modified prototype to the end of the <div> tag
  $container.append($prototype);
  // Finally, we increment the counter so that the next addition is done with another number
  return index++;
}


// function to call Ajax on form
function callAjax(form, $container, index) {
  $.ajax({
    type: form.attr('method'),
    url: form.attr('action'),
    data: form.serialize(),
    beforeSend: function (htmlResponse) {
      var $content_to_change = $('div#content_to_change_' + htmlResponse['entityname']);
      $content_to_change.html('<i class="fas fa-spinner fa-spin fa-4x"></i>');
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('error ajax request : ' + errorThrown);
      console.log(jqXHR);
      console.log(textStatus);
      console.log(errorThrown);
    },
    success: function (htmlResponse) {
      var $content_to_change = $('div#content_to_change_' + htmlResponse['entityname']);
      if (htmlResponse['exception_message'] == '') {
        if (index === undefined) { // case of linked records (rel 1-N)
          $($container).append($('<option>', {
            value: htmlResponse['select_id'],
            text: htmlResponse['select_name']
          }));
          $($container).val(htmlResponse['select_id']);
        } else { // case of ArrayCollection (rel N-N)
          index = addCategoryForNewRecord(index, $container, htmlResponse['select_id'], htmlResponse['select_name']);
        }
        $content_to_change.html(htmlResponse['html_form']);
      } else {
        alert(htmlResponse['exception_message'].split("#")[0]);
        $content_to_change.html(htmlResponse['html_form']);
      }
    },
    complete: function (htmlResponse) {
      //if (htmlResponse['exception_message'] == '') $('.modal').modal('hide');
      $('.modal').modal('hide');
    }
  });
}


/** getLastIndex(formNameOfCollection) 
 *  get the last index of the form Collection "formNameOfCollection"
 *  formNameOfCollection : the name of the Symfony Collection used in the form
 *   ex. formNameOfCollection = bbees_e3sbundle_collecte_estFinancePars
*/
function getLastIndex(formNameOfCollection) {
  var $container = $('div#' + formNameOfCollection);
  // search for the last index used and create a new record with  index = lastindex + 1 
  var posindexinid = formNameOfCollection.length;
  posindexinid = posindexinid + 1;
  // search for the last created index of one or two digit (max = 99) 
  // alert(' formNameOfCollection : '+formNameOfCollection);
  if (typeof $container.find('select').last().attr('id') !== 'undefined') {
    var lastindex = $container.find('select').last().attr('id').charAt(posindexinid);
    var nextcharafterlastindex = $container.find('select').last().attr('id').charAt(posindexinid + 1);
    var index = parseInt(lastindex);
    if (nextcharafterlastindex !== '_') {
      index = index * 10 + (parseInt(nextcharafterlastindex));
    }
    index = index + 1;
  } else {
    index = 0;
  }

  return index;
}


// function to convert in Upper case 
function forceUppercase($container) {
  $container.keyup(function (e) {
    var field_value = $container.val().toUpperCase();
    $container.val(field_value);
  })
}

// function to Add a Back to Button to the entityform Typeahead
function addBackToRelatedRecord(entityform, entityRel, nameButonBack, TypeaheadField = 0, action = 'edit') {
  console.log("ADD BACK")
  var entityrel_lowercase = entityRel.toLowerCase();
  if (TypeaheadField) {
    var entityRelSelected = $('#bbees_e3sbundle_' + entityform + '_' + entityRel + 'Id').val();
  } else {
    var entityRelSelected = $('#bbees_e3sbundle_' + entityform + '_' + entityRel + 'Fk option:selected').val();
  }
  console.log()

  //if(!nameEntityRel) nameEntityRel = entityRel.toUpperCase();
  //alert("entityRelSelected="+entityRelSelected);
  if (typeof entityRelSelected !== 'undefined' && entityRelSelected != null && entityRelSelected != '') {
    if (action == 'edit') {
      var $addBouton = $('<span>&nbsp;<a href="../../' + entityrel_lowercase + '/' + entityRelSelected + '/edit" class="btn btn-round btn-primary"> ' + nameButonBack + '</a><span> ');
      $('.main-header h1').append($addBouton);
    }
    if (action == 'show') {
      var $addBouton = $('<span>&nbsp;<a href="../' + entityrel_lowercase + '/' + entityRelSelected + '" class="btn btn-round btn-primary"> ' + nameButonBack + '</a><span> ');
      $('.main-header h1').append($addBouton);
    }
  }
}

// function to Test the values of fields date and date_precision
function dateDateprecision(container, nameFieldDate, message) {
  var valueDatePrecision = $('input[id^="' + container + '_datePrecisionVocFk_"]:checked ').val();
  var datePrecision = $('label[for=' + container + '_datePrecisionVocFk_' + valueDatePrecision + ']').text().trim();
  var dateCollecteYear = $('#' + container + '_' + nameFieldDate + '_year').val();
  var dateCollecteMonth = $('#' + container + '_' + nameFieldDate + '_month').val();
  var dateCollecteDay = $('#' + container + '_' + nameFieldDate + '_day').val();
  //alert(datePrecision); 
  var flagDatePrecision = 1;
  switch (datePrecision) {
    case 'INCONNU':
      if (dateCollecteYear != '' || dateCollecteMonth != '' || dateCollecteDay != '') {
        flagDatePrecision = 0;
        alert(message["INCONNU"]);
      }
      break;
    case 'ANNEE':
      if (dateCollecteYear == '' || Number(dateCollecteMonth) != 1 || Number(dateCollecteDay) != 1) {
        //alert(dateCollecteYear+' - '+Number(dateCollecteMonth)+' - '+Number(dateCollecteDay));
        flagDatePrecision = 0;
        alert(message["ANNEE"]);
      }
      break;
    case 'MOIS':
      if (dateCollecteYear == '' || dateCollecteMonth == '' || Number(dateCollecteDay) != 1) {
        flagDatePrecision = 0;
        alert(message["MOIS"]);
      }
      break;
    case 'JOUR':
      if (dateCollecteYear == '' || dateCollecteMonth == '' || dateCollecteDay == '') {
        flagDatePrecision = 0;
        alert(message["JOUR"]);
      }
      break;
    case 'NOT KNOWN':
      if (dateCollecteYear != '' || dateCollecteMonth != '' || dateCollecteDay != '') {
        flagDatePrecision = 0;
        alert(message["INCONNU"]);
      }
      break;
    case 'YEAR':
      if (dateCollecteYear == '' || Number(dateCollecteMonth) != 1 || Number(dateCollecteDay) != 1) {
        flagDatePrecision = 0;
        alert(message["ANNEE"]);
      }
      break;
    case 'MONTH':
      if (dateCollecteYear == '' || dateCollecteMonth == '' || Number(dateCollecteDay) != 1) {
        flagDatePrecision = 0;
        alert(message["MOIS"]);
      }
      break;
    case 'DAY':
      if (dateCollecteYear == '' || dateCollecteMonth == '' || dateCollecteDay == '') {
        flagDatePrecision = 0;
        alert(message["JOUR"]);
      }
      break;
    default:
      flagDatePrecision = 0;

  }
  if (!flagDatePrecision) $('#' + container + '_datePrecisionVocFk_' + valueDatePrecision).prop("checked", false);
  return flagDatePrecision;
}

// function to Test date format
function dateFormat(container, nameFieldDate, message) {
  var valueDatePrecision = $('input[id^="' + container + '_datePrecisionVocFk_"]:checked ').val();
  var datePrecision = $('label[for=' + container + '_datePrecisionVocFk_' + valueDatePrecision + ']').text().trim();
  var dateCollecteYear = $('#' + container + '_' + nameFieldDate + '_year').val();
  var dateCollecteMonth = $('#' + container + '_' + nameFieldDate + '_month').val();
  var dateCollecteDay = $('#' + container + '_' + nameFieldDate + '_day').val();
  // if no date_precision is checked, leave the possibility to enter any date with a check on the format
  // if(dateCollecteDay != '' && dateCollecteMonth != '' && dateCollecteYear != '') alert("dateCollecteYear:"+dateCollecteYear+"dateCollecteMonth:"+dateCollecteMonth+"dateCollecteDay:"+dateCollecteDay); 
  var flagDate = 1;
  if (dateCollecteDay != '' && dateCollecteMonth != '' && dateCollecteYear != '') {
    var regJ = new RegExp("^[0-3]?[0-9]{1}$");
    var regM = new RegExp("^[0-1]?[0-9]{1}$");
    var regA = new RegExp("^[1-2]{1}[0-9]{3}$");
    //alert("dateCollecteYear:"+regA.test(dateCollecteYear)+"dateCollecteMonth:"+regM.test(dateCollecteMonth) +"dateCollecteDay:"+regJ.test(dateCollecteDay)); 
    if (!regJ.test(dateCollecteDay) || !regM.test(dateCollecteMonth) || !regA.test(dateCollecteYear)) {
      flagDate = 0;
      alert(message["BAD-FORMAT-DATE"]);
    } else {
      if (parseInt(dateCollecteDay) > 31 || parseInt(dateCollecteMonth) > 12) {
        flagDate = 0;
        alert(message["BAD-FORMAT-DATE"]);
      }
    }
  }
  $('#' + container + '_datePrecisionVocFk_' + valueDatePrecision).prop("checked", false);
  return flagDate;
}

/**
 * Returns an array of the values ​​of a key of a JSON object.
 * @param {Object} json objet JSON
 * @param {any} key clé à cibler
 */
function unpack(json, key) {
  return json.map(function (row) { return row[key] })
}

/**
 * Display stations located in an area of ​​0.1x0.1 deg around a GPS point
 * @param {Object} json_stations  
 * @param {number} latGPS
 * @param {number} longGPS
 */
function stationsPlot(json_stations, latGPS = undefined, longGPS = undefined) {

  var longmin = (parseFloat(longGPS.replace(",", ".")) - 0.1).toFixed(6);
  var longmax = (parseFloat(longGPS.replace(",", ".")) + 0.1).toFixed(6);
  var latmin = (parseFloat(latGPS.replace(",", ".")) - 0.1).toFixed(6);
  var latmax = (parseFloat(latGPS.replace(",", ".")) + 0.1).toFixed(6);
  var latGPS = parseFloat(latGPS.replace(",", ".")).toFixed(6);
  var longGPS = parseFloat(longGPS.replace(",", ".")).toFixed(6);
  var latArray = [latGPS];
  var longArray = [longGPS];
  //alert(longmin.toString()+'-'+longmax+'-'+latmin+'-'+latmax+'-'+longGPS.toString()+'-'+latGPS.toString());

  function build_station_data(json, update = {}) {
    const latitude = unpack(json, 'station.latDegDec'),
      longitude = unpack(json, 'station.longDegDec'),
      code_station = unpack(json, 'station.codeStation'),
      nom_station = unpack(json, 'station.nomStation'),
      code_commune = unpack(json, 'commune.codeCommune')
    // Initialization of hover text
    var hoverText = []
    for (var i = 0; i < latitude.length; i++) {
      var difLat = parseFloat(latitude[i] - latGPS).toFixed(6);
      var difLong = parseFloat(longitude[i] - longGPS).toFixed(6);
      var stationText = [
        "Code: " + code_station[i],
        "Nom: " + nom_station[i],
        "Coords: " + latitude[i] + "  /  " + longitude[i],
        "Diff Coords: " + difLat + "  /  " + difLong,
        "Commune: " + code_commune[i]
      ].join("<br>")
      hoverText.push(stationText)
    }
    const data = {
      type: 'scattergeo',
      lat: latitude,
      lon: longitude,
      hoverinfo: 'text',
      text: hoverText,
      marker: {
        size: 8,
        line: {
          width: 1,
          color: 'grey'
        }
      },
      name: "Stations",
    }
    // Add data
    $.extend(true, data, update)
    return data
  }

  // Init plotly
  var d3 = Plotly.d3
  $("#station-geo-map").html('')
  var gd3 = d3.select('#station-geo-map')
  var gd = gd3.node()

  // Data
  const data_stations = build_station_data(json_stations, {
    name: "Stations BDD",
    marker: {
      symbol: "circle-open",
      size: 10,
      color: "orange",
      opacity: 0.8,
      line: {
        width: 2,
        color: "green",
      }
    }
  })

  const dataSelectedStation = {
    type: 'scattergeo',
    lat: latArray,
    lon: longArray,
    marker: {
      symbol: "triangle-up",
      size: 5,
      color: "red",
      opacity: 0.3,
      line: {
        width: 2,
        color: "red",
      }
    },
    name: "GPS : lat = " + latGPS + "  /  long = " + longGPS,
  }

  // Objet data : scatterplots
  var data = [
    data_stations,
    dataSelectedStation
  ]

  // Graph display settings
  const layout = $.extend(plotlyconfig.geo.layout, {
    geo: $.extend(plotlyconfig.geo.layout.geo, {
      lonaxis: {
        'range': [longmin, longmax]
      },
      lataxis: {
        'range': [latmin, latmax]
      },
      center: {
        'lon': longGPS,
        'lat': latGPS
      },
    })
  })

  Plotly.newPlot(gd, data, layout, {
    displaylogo: false, // no logo, remove unnecessary control buttons
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
  })

  Plotly.Plots.resize(gd)

  return gd // Return objet plotly
}



/**
 * Function to plot stations on map
 * @param {Object} json_stations  
 * @param {number} latGPS
 * @param {number} longGPS
 */
function stationsMap(json_stations, latGPS = undefined, longGPS = undefined) {

  var longmin = (parseFloat(longGPS.replace(",", ".")) - 15).toFixed(6);
  var longmax = (parseFloat(longGPS.replace(",", ".")) + 15).toFixed(6);
  var latmin = (parseFloat(latGPS.replace(",", ".")) - 11).toFixed(6);
  var latmax = (parseFloat(latGPS.replace(",", ".")) + 11).toFixed(6);
  var latGPS = parseFloat(latGPS.replace(",", ".")).toFixed(6);
  var longGPS = parseFloat(longGPS.replace(",", ".")).toFixed(6);
  var latArray = [latGPS];
  var longArray = [longGPS];
  //alert(longmin.toString()+'-'+longmax+'-'+latmin+'-'+latmax+'-'+longGPS.toString()+'-'+latGPS.toString());

  function build_station_data(json, update = {}) {
    const latitude = unpack(json, 'station.latDegDec'),
      longitude = unpack(json, 'station.longDegDec')
    const data = {
      type: 'scattergeo',
      lat: latitude,
      lon: longitude,
      hoverinfo: 'none',
      marker: {
        size: 8,
        line: {
          width: 1,
          color: 'grey'
        }
      },
      name: "Stations",
    }
    // Add data 
    $.extend(true, data, update)
    return data
  }

  // Init plotly
  var d3 = Plotly.d3
  $("#station-geo-map").html('')
  var gd3 = d3.select('#station-geo-map')
  var gd = gd3.node()

  // Data
  const data_stations = build_station_data(json_stations, {
    name: "Stations BDD",
    marker: {
      symbol: "triangle-up",
      size: 3,
      color: "orange",
      opacity: 0.8,
      line: {
        width: 1,
        color: "green",
      }
    }
  })

  const dataSelectedStation = {
    type: 'scattergeo',
    lat: latArray,
    lon: longArray,
    marker: {
      symbol: "triangle-up",
      size: 5,
      color: "red",
      opacity: 0.3,
      line: {
        width: 2,
        color: "red",
      }
    },
    name: "GPS : lat = " + latGPS + "  /  long = " + longGPS,
  }

  // Objet data : scatterplots
  var data = [
    data_stations
  ]


  // Objet data complet : scatterplots 

  // graphic display Parameters 
  const layout = $.extend(plotlyconfig.geo.layout, {
    showlegend: false,
    margin: {
      t: 0,
      b: 0,
      l: 0,
      r: 0
    },
    height: 487,
    geo: $.extend(plotlyconfig.geo.layout.geo, {
      lonaxis: {
        'range': [longmin, longmax]
      },
      lataxis: {
        'range': [latmin, latmax]
      },
      center: {
        'lon': longGPS,
        'lat': latGPS
      }
    })
  })

  Plotly.newPlot(gd, data, layout, {
    displaylogo: false,
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d'],
    staticPlot: true
  })

  Plotly.Plots.resize(gd)

  return gd // Return objet plotly
}

// function that automatically generates the Sequence code
function setCodeSqcAss() {
  var code = '';
  var codeIndBiomol = $('#form_individuFk option:selected').text();
  var statut = $('#bbees_e3sbundle_sequenceassemblee_statutSqcAssVocFk option:selected').text();
  var tabChromato = new Array();
  for (i = 0; i < 25; i++) {
    var estAligneEtTraite = $('#bbees_e3sbundle_sequenceassemblee_estAligneEtTraites_' + i.toString() + '_chromatogrammeFk option:selected').text();
    if (estAligneEtTraite !== '') {
      tabChromato.push(estAligneEtTraite);
    }
  }
  var codeChromatoSpecificite = ''
  if (tabChromato.length > 0) {
    tabChromato.sort();
    for (i = 0; i < tabChromato.length; i++) {
      codeChromatoSpecificite = (i == 0) ? tabChromato[i] : codeChromatoSpecificite + '-' + tabChromato[i];
    }
  } else {
  }
  if (statut.substr(0, 5) === 'VALID') {
    code = codeIndBiomol + '_' + codeChromatoSpecificite;
  } else {
    code = statut + '_' + codeIndBiomol + '_' + codeChromatoSpecificite;
  }
  $('#bbees_e3sbundle_sequenceassemblee_codeSqcAss').val(code);
}

// function that automatically generates the codeSqcAssExt
function setCodeSqcAssExt(CodePlaceholder = { "statut": "Statut", "RefTaxonSelected": "RefTaxonSelected", "numIndividu": "numIndividu", "accessionNumber": "accessionNumber" }) {
  var code = '';
  var RefTaxonSelected = $('#bbees_e3sbundle_sequenceassembleeext_especeIdentifiees_0_referentielTaxonFk option:selected').text();
  var statut = $('#bbees_e3sbundle_sequenceassembleeext_statutSqcAssVocFk option:selected').text();
  //var codeCollecte = $('#bbees_e3sbundle_sequenceassembleeext_collecteFk option:selected').text();
  var codeCollecte = $('#bbees_e3sbundle_sequenceassembleeext_collecteTypeahead').val();
  var origine = $('#bbees_e3sbundle_sequenceassembleeext_origineSqcAssExtVocFk option:selected').text();
  var numIndividu = $('#bbees_e3sbundle_sequenceassembleeext_numIndividuSqcAssExt').val();
  var accessionNumber = $('#bbees_e3sbundle_sequenceassembleeext_accessionNumberSqcAssExt').val();
  if (statut == '') statut = CodePlaceholder["statut"];
  if (RefTaxonSelected == '') RefTaxonSelected = CodePlaceholder["RefTaxonSelected"];
  if (numIndividu == '') numIndividu = CodePlaceholder["numIndividu"];
  if (accessionNumber == '') accessionNumber = CodePlaceholder["accessionNumber"];
  //alert(RefTaxonSelected);
  if (statut.substr(0, 5) === 'VALID') {
    code = RefTaxonSelected + '_' + codeCollecte + '_' + numIndividu + '_' + accessionNumber + '|' + origine;
  } else {
    code = statut + '_' + RefTaxonSelected + '_' + codeCollecte + '_' + numIndividu + '_' + accessionNumber + '|' + origine;
  }
  $('#bbees_e3sbundle_sequenceassembleeext_codeSqcAssExt').val(code);
  $('#bbees_e3sbundle_sequenceassembleeext_codeSqcAssExtAlignement').val(code);
}

// function that automatically generates the codeLot
function setCodeLot(CodePlaceholder = { "RefTaxonSelected": "RefTaxonSelected" }) {
  var code = '';
  var RefTaxonSelected = $('#bbees_e3sbundle_lotmateriel_especeIdentifiees_0_referentielTaxonFk option:selected').text();
  //var codeCollecte = $('#bbees_e3sbundle_lotmateriel_collecteFk option:selected').text();
  var codeCollecte = $('#bbees_e3sbundle_lotmateriel_collecteTypeahead').val();
  if (RefTaxonSelected == '') RefTaxonSelected = CodePlaceholder["RefTaxonSelected"];
  if (codeCollecte == '') codeCollecte = "Code";
  code = RefTaxonSelected + '|' + codeCollecte;
  $('#bbees_e3sbundle_lotmateriel_codeLotMateriel').val(code);
}

// function that automatically generates the codeLotExt
function setCodeLotExt(CodePlaceholder = { "RefTaxonSelected": "RefTaxonSelected" }) {
  var code = '';
  var RefTaxonSelected = $('#bbees_e3sbundle_lotmaterielext_especeIdentifiees_0_referentielTaxonFk option:selected').text();
  //    var codeCollecte = $('#bbees_e3sbundle_lotmaterielext_collecteFk option:selected').text();
  var codeCollecte = $('#bbees_e3sbundle_lotmaterielext_collecteTypeahead').val();
  if (RefTaxonSelected == '') RefTaxonSelected = CodePlaceholder["RefTaxonSelected"];
  if (codeCollecte == '') codeCollecte = "Code";
  code = RefTaxonSelected + '|' + codeCollecte;
  $('#bbees_e3sbundle_lotmaterielext_codeLotMaterielExt').val(code);
}

// function that automatically generates the CodeIndBiomol
function setCodeIndBiomol(CodePlaceholder = { "RefTaxonSelected": "RefTaxonSelected" }) {
  var code = '';
  var RefTaxonSelected = $('#bbees_e3sbundle_individu_especeIdentifiees_0_referentielTaxonFk option:selected').text();
  // var codeLot = $('#bbees_e3sbundle_individu_lotMaterielFk option:selected').text();
  var codeLot = $('#bbees_e3sbundle_individu_lotmaterielTypeahead').val();
  var codeCollecte = codeLot;
  if (codeLot.split("|").length > 1) codeCollecte = codeLot.split("|")[1];
  var numIndBiomol = $('#bbees_e3sbundle_individu_numIndBiomol').val();
  if (RefTaxonSelected == '') RefTaxonSelected = CodePlaceholder["RefTaxonSelected"];
  if (numIndBiomol != '') {
    code = RefTaxonSelected + '_' + codeCollecte + '_' + numIndBiomol;
  }
  //alert('change on click: '+code);
  $('#bbees_e3sbundle_individu_codeIndBiomol').val(code);
}

// function that automatically generates the CodeIndTriMorpho
function setCodeIndTriMorpho(CodePlaceholder = { "codeTube": "codeTube", "RefTaxonSelected": "RefTaxonSelected" }) {
  var code = '';
  var RefTaxonSelected = $('#bbees_e3sbundle_individu_especeIdentifiees_0_referentielTaxonFk option:selected').text();
  //var codeLot = $('#bbees_e3sbundle_individu_lotMaterielFk option:selected').text();
  var codeLot = $('#bbees_e3sbundle_individu_lotmaterielTypeahead').val();
  var codeCollecte = codeLot;
  if (codeLot.split("|").length > 1) codeCollecte = codeLot.split("|")[1];
  var codeTube = $('#bbees_e3sbundle_individu_codeTube').val();
  if (RefTaxonSelected == '') RefTaxonSelected = CodePlaceholder["RefTaxonSelected"];
  if (codeTube == '') codeTube = CodePlaceholder["codeTube"];
  code = RefTaxonSelected + '|' + codeCollecte + '[' + codeTube + ']';
  //alert('change on click: '+datePrecision+'-'+dateCollecteYear);
  $('#bbees_e3sbundle_individu_codeIndTriMorpho').val(code);
}

// function that automatically generates the codeChromato of the chromatogram
function setCodeChromato(CodePlaceholder = []) {
  var code = '';
  var primerSelected = $('#bbees_e3sbundle_chromatogramme_primerChromatoVocFk option:selected').text();
  var numYAS = $('#bbees_e3sbundle_chromatogramme_numYas').val();
  if (numYAS == '') numYAS = CodePlaceholder["numYAS"];
  code = numYAS + '|' + primerSelected;
  $('#bbees_e3sbundle_chromatogramme_codeChromato').val(code);
}

/**
 * Set automaticaly the codePcr in form PCR
 */
function setCodePcr(CodePlaceholder = []) {
  var code = '';
  var adnSelected = $('#bbees_e3sbundle_pcr_adnTypeahead').val();
  var primerPcrStartSelected = $('#bbees_e3sbundle_pcr_primerPcrStartVocFk option:selected').text();
  var primerPcrEndSelected = $('#bbees_e3sbundle_pcr_primerPcrEndVocFk option:selected').text();
  var numPcr = $('#bbees_e3sbundle_pcr_numPcr').val();
  if (numPcr == '') numPcr = CodePlaceholder["numPcr"];
  if (adnSelected == '') adnSelected = "DNA code";
  code = adnSelected + '_' + numPcr + '_' + primerPcrStartSelected + '_' + primerPcrEndSelected;
  $('#bbees_e3sbundle_pcr_codePcr').val(code);
}
