
//
function  addArrayCollectionButtonEmbed(container, nameFirstFieldCollection, nameCollection, addnew = false, fieldRequired = true, nameArrayCollectionEmbed = null, nameCollectionEmbed = null, addnewEmbed = false, fieldRequiredEmbed = true){
    
    $containerCollectionEmbed = $('div#'+container);
    if ($containerCollectionEmbed.length !== 0) {
        // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.$
        $containerCollectionEmbed.prepend('</br></br>');
        // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
        //var index = $container.find(':input').length;
        // var index = $('div [id^='+container+'_]').length;
        var index = $('[id^="'+container+'_"][id$="_'+nameFirstFieldCollection+'"]').length;
        //alert('index='+index);
        if (addnew){
           // ajout du bonton add New
           var nameAddNewButon = "Add a new "+nameCollection;
           var $addBoutonAdd= $('<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal'+nameCollection+'">'+addButon[nameAddNewButon]+'</button>');
           $containerCollectionEmbed.prepend($addBoutonAdd);   
        }
        // On ajoute un lien pour ajouter une nouvelle catégorie
        var nameAddButon = "Add a "+nameCollection;
        var $addLink = $('<span><a href="#" id="add_'+nameCollection+'" class="btn btn-primary btn-sm">'+addButon[nameAddButon]+'</a></span>');
        $containerCollectionEmbed.prepend($addLink);
        // Si le champ obligatoire on affiche le formulaire Embed
        if (index == 0 && fieldRequired) {
          // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un 
          addCategoryForExistingRecord(index, $containerCollectionEmbed, false);
          var containerEmbed = container+'_'+index.toString()+'_'+nameArrayCollectionEmbed;
          //addArrayCollectionButton2(containerEmbed,'personneFk',nameCollectionEmbed,false,true ); 
        }
        // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
        $addLink.click(function(e) {
          index++;
          addCategoryForExistingRecord(index, $containerCollectionEmbed);
          var $containerEmbed = $(container+'_'+index.toString()+'_'+nameArrayCollectionEmbed);
          var containerEmbed = container+'_'+index.toString()+'_'+nameArrayCollectionEmbed;
          //alert(containerEmbed);
          addArrayCollectionButton2(containerEmbed,'personneFk',nameCollectionEmbed,false,true);
          e.preventDefault(); // évite qu'un # apparaisse dans l'URL
          return false;
        });  
        // Pour chaque collection  on ajoute un lien de suppression & on ajoute les boutons pour la ArraycollectionEmbed (estIdentifiePars)
        var comptChildren = 0;
        var nbCollectionEmbed = $containerCollectionEmbed.children('div').length;
        //alert('nbCollectionEmbed='+nbCollectionEmbed);
        $containerCollectionEmbed.children('div').each(function() {
            //alert("attribut Id="+$(this).find('[id$="_'+nameArrayCollectionEmbed+'"]').html());
            if (fieldRequired == false || comptChildren > 0) { 
                    addDeleteLink($(this),true,nameCollection); 
                } else {
                    addDeleteLink($(this),false,nameCollection); 
                }
            // nameArrayCollectionEmbed=estIdentifiePars,  nameCollectionEmbed = Personne
            addArrayCollectionButton3($(this).find('[id$="_'+nameArrayCollectionEmbed+'"]'),nameArrayCollectionEmbed,'personneFk',nameCollectionEmbed,false,true );
            comptChildren++;
         });
    }   
}

//
function  addArrayCollectionButton3($container, nameArrayCollectionEmbed, nameFirstFieldCollection , nameCollection, addnew = false, fieldRequired = true){
    
    if ($container.length !== 0) {
        // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.$
        $container.prepend('</br></br>');
        // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
        if (addnew){
           // ajout du bonton add New
           var nameAddNewButon = "Add a new "+nameCollection;
           var $addBoutonAdd= $('<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal'+nameCollection+'">'+addButon[nameAddNewButon]+'</button>');
           $container.prepend($addBoutonAdd);   
       }
        // On ajoute un lien pour ajouter une nouvelle catégorie
        var nameAddButon = "Add a "+nameCollection;
        var $addLink = $('<span><a href="#" id="add_'+nameCollection+'" class="btn btn-primary btn-sm">'+addButon[nameAddButon]+'</a></span>');
        $container.prepend($addLink); 
        //on recherche l'index ;
        if(nameFirstFieldCollection !==''){
            var $selecteur = $container.find('[id$="_'+nameFirstFieldCollection+'"]');
        } else {
            var $selecteur = $container;
        }
        var index = $selecteur.length;
         // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un         
        if (index == 0 && fieldRequired) {
          addCategoryForExistingRecord(index, $container, false);
          //index++
          //e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        } 
        // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
        $addLink.click(function(e) {
          //alert('addArrayCollectionButton3 :index ='+index);
          //addCategoryForExistingEmbedRecord(index, $container, nameArrayCollectionEmbed);
          index++;
          addCategoryForExistingRecord(index, $container);
          e.preventDefault(); // évite qu'un # apparaisse dans l'URL
          return false;
        });

        //alert(nameCollection+':'+$container.children('div').length);
        //if($container.children('div').length == 0) alert(nameCollection+':'+index);
 
        // Pour chaque collection  on ajoute un lien de suppression
        var comptChildren = 0;
        $container.children('div').each(function() {
            if (fieldRequired == false || comptChildren > 0) { 
                addDeleteLink($(this),true, nameCollection); 
            } else {
                addDeleteLink($(this),false, nameCollection); 
            }
            comptChildren++;
         });
    }
}

//
function  addArrayCollectionButton2(container, nameFirstFieldCollection, nameCollection, addnew = false, fieldRequired = true){
    $container = $('div#'+container);
    if ($container.length !== 0) {
        // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.$
        $container.prepend('</br></br>');
        // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
        //var index = $container.find(':input').length;
        if(nameFirstFieldCollection !==''){
            var selecteur = 'div[id^="'+container+'_"][id$="_'+nameFirstFieldCollection+'"]';
        } else {
            var selecteur = 'div[id^="'+container+'_"]';
        }
        //alert('addArrayCollectionButton2 : selecteur ='+selecteur);
        //var index = $('div[id^="'+container+'_"][id$="_'+nameFirstFieldCollection+'"]').length;
        var index = $(selecteur).length;
        //alert("index="+index);
        if (addnew){
           // ajout du bonton add New
           var nameAddNewButon = "Add a new "+nameCollection;
           var $addBoutonAdd= $('<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal'+nameCollection+'">'+addButon[nameAddNewButon]+'</button>');
           $container.prepend($addBoutonAdd);   
       }
        // On ajoute un lien pour ajouter une nouvelle catégorie
        var nameAddButon = "Add a "+nameCollection;
        var $addLink = $('<span><a href="#" id="add_'+nameCollection+'" class="btn btn-primary btn-sm">'+addButon[nameAddButon]+'</a></span>');
        $container.prepend($addLink);
        // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un
        if (index == 0 && fieldRequired) { 
          addCategoryForExistingRecord(index, $container, false);
          //index++;
          //e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        } 
        // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
        $addLink.click(function(e) {
          //alert('addArrayCollectionButton2 : index ='+index);
          addCategoryForExistingRecord(index, $container);
          index++;
          e.preventDefault(); // évite qu'un # apparaisse dans l'URL
          return false;
        });
 
        // Pour chaque collection  on ajoute un lien de suppression
        var comptChildren = 0;
        $container.children('div').each(function() {
            if (fieldRequired == false || comptChildren > 0) { 
                addDeleteLink($(this),true, nameCollection); 
            } else {
                addDeleteLink($(this),false, nameCollection); 
            }
            comptChildren++;
         });
    }
}


//
function  addArrayCollectionButton($container, nameCollection, addnew = false, fieldRequired = true){
    
    if ($container.length !== 0) {
        // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.$
        $container.prepend('</br></br>');
        // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
        var index = $container.find(':input').length;
        if (addnew){
           // ajout du bonton add New
           var nameAddNewButon = "Add a new "+nameCollection;
           var $addBoutonAdd= $('<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal'+nameCollection+'">'+addButon[nameAddNewButon]+'</button>');
           $container.prepend($addBoutonAdd);   
       }
        // On ajoute un lien pour ajouter une nouvelle catégorie
        var nameAddButon = "Add a "+nameCollection;
        var $addLink = $('<span><a href="#" id="add_'+nameCollection+'" class="btn btn-primary btn-sm">'+addButon[nameAddButon]+'</a></span>');
        $container.prepend($addLink);
        // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
        $addLink.click(function(e) {
          index++;
          addCategoryForExistingRecord(index, $container);
          e.preventDefault(); // évite qu'un # apparaisse dans l'URL
          return false;
        });

        //alert(nameCollection+':'+$container.children('div').length);
        //if($container.children('div').length == 0) alert(nameCollection+':'+index);

        if (index == 0 && fieldRequired) {
          // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un 
          addCategoryForExistingRecord(index, $container, false);
          //index++;
          //e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        }  
        // Pour chaque collection  on ajoute un lien de suppression
        var comptChildren = 0;
        $container.children('div').each(function() {
            if (fieldRequired == false || comptChildren > 0) { 
                addDeleteLink($(this),true, nameCollection); 
            } else {
                addDeleteLink($(this),false, nameCollection); 
            }
            comptChildren++;
         });
    }
}

// La fonction qui ajoute un lien de suppression d'une catégorie
function addDeleteLink($prototype, visible = true, nameCollection = '') {
  // Création du lien
  if (visible) {
    var id_delete = (nameCollection != '') ? 'id="delete_'+nameCollection+'"' : '';
    $deleteLink = $('<div '+id_delete+' class="col-sm-2 pull-right"><a href="#" class="btn btn-danger btn-sm " type="button">Delete</a></div>');
  } else {
     $deleteLink = $('<div class="col-sm-2 pull-right">&nbsp;</div>');
  }
  // Ajout du lien
  $prototype.prepend($deleteLink);
  // Ajout du listener sur le clic du lien
  $deleteLink.click(function(e) {
    $prototype.remove();
    e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    return false;
  });
}

// La fonction qui ajoute un formulaire dans une collectionEmbed
 function addCategoryForExistingEmbedRecord(index,$container, nameArrayCollectionEmbed, deleteBouton = true) {
   // Dans le contenu de l'attribut « data-prototype », on remplace :
   // - le texte "__name__label__" qu'il contient par le label du champ
   // - le texte "__name__" qu'il contient par le numéro du champ
   var $prototype = $($container.attr('data-prototype').replace('<label class="col-sm-2 control-label required">__name__label__</label>', '').replace(/__name__/g, index));    
   // On ajoute au prototype un lien pour pouvoir supprimer 
   if (deleteBouton) addDeleteLink($prototype);
   // On ajoute le prototype modifié à la fin de la balise <div>
   //alert("addCategoryForExistingEmbedRecord  : index ="+index);
   //alert('addArrayCollectionButton3 :container ='+$container.html());
   var newindex = index+1;
   var $prototypeEmbed = $($prototype.find('div[id$="'+nameArrayCollectionEmbed+'_0"]').html().replace(nameArrayCollectionEmbed+'_0', nameArrayCollectionEmbed+'_'+newindex).replace(nameArrayCollectionEmbed+'][0', nameArrayCollectionEmbed+']['+newindex));    
   // On ajoute le prototype modifié à la fin de la balise <div>
   //$container.append($prototype);
   $container.append($prototypeEmbed);
   // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
   return false;
 }
   
// La fonction qui ajoute un formulaire 
 function addCategoryForExistingRecord(index,$container, deleteBouton = true) {
   //alert("addCategoryForExistingRecord  : index ="+index);
   // Dans le contenu de l'attribut « data-prototype », on remplace :
   // - le texte "__name__label__" qu'il contient par le label du champ
   // - le texte "__name__" qu'il contient par le numéro du champ
   var $prototype = $($container.attr('data-prototype').replace('<label class="col-sm-2 control-label required">__name__label__</label>', '').replace(/__name__/g, index));    
   // On ajoute au prototype un lien pour pouvoir supprimer 
   if (deleteBouton) addDeleteLink($prototype);
   // On ajoute le prototype modifié à la fin de la balise <div>
   //$container.append($prototype);
   $container.append($prototype);
   // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
   return false;
 }

// La fonction qui ajoute un formulaire Structure
 function addCategoryForNewRecord(index, $container, select_id, select_name) {
   // Dans le contenu de l'attribut « data-prototype », 
   // - on supprime les labels
   // - on modifie le texte "__name__" qu'il contient par le numéro du champ
   //var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, 'APourSamplingMethod :' + (index+1)).replace(/__name__/g, index));
   var $prototype = $($container.attr('data-prototype').replace('<label class="col-sm-2 control-label required">__name__label__</label>', '').replace(/__name__/g, index)
           .replace('</select>','><option value="'+select_id+'" selected="selected">'+select_name+'</option></select>'));
   // On ajoute au prototype un lien pour pouvoir supprimer 
   addDeleteLink($prototype);
   // On ajoute le prototype modifié à la fin de la balise <div>
   $container.append($prototype);
   // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
   return index++;
 }
 
 
 //
 function callAjax(form, $container, index){		
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize(),
        beforeSend: function(htmlResponse) {  
            var $content_to_change = $('div#content_to_change_'+htmlResponse['entityname']);
            //$content_to_change.html('<img id="img_load" src="https://d13yacurqjgara.cloudfront.net/users/82092/screenshots/1073359/spinner.gif" height="42" width="42" />');
            $content_to_change.html('<i class="fa fa-spinner fa-spin fa-4x"></i>');
                                },
        error: function(jqXHR, textStatus, errorThrown) {
                alert('error ajax request : '+errorThrown);
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            },
        success: function(htmlResponse) {  
            var $content_to_change = $('div#content_to_change_'+htmlResponse['entityname']);
            if (htmlResponse['exception_message'] == ''){
                if (index === undefined ){ // cas des enregistrement liés (rel 1-N)
                    $($container).append($('<option>', {
                        value: htmlResponse['select_id'],
                        text: htmlResponse['select_name']
                     }));
                     $($container).val(htmlResponse['select_id']);
                } else { // cas des ArrayCollection (rel N-N)
                    index = addCategoryForNewRecord(index, $container, htmlResponse['select_id'], htmlResponse['select_name']);
                }  
                $content_to_change.html(htmlResponse['html_form']);
             } else {
                alert(htmlResponse['exception_message'].split("#")[0]);
                $content_to_change.html(htmlResponse['html_form']);
             }
            },
        complete: function(htmlResponse) {
            //if (htmlResponse['exception_message'] == '') $('.modal').modal('hide');
            $('.modal').modal('hide');
        }
    });		
}

// Mise en majuscule
function maj($container) {
         $container.keyup(function (e) {
            var field_value = $container.val().toUpperCase();
            $container.val(field_value);
        })
}

// L'ajout d'un bouton Back to the collect
function addBackToRelatedRecord(entityform, entityRel, nameEntityRel = false) {
        var entityrel_lowercase = entityRel.toLowerCase();
        var entityRelSelected = $('#bbees_e3sbundle_'+entityform+'_'+entityRel+'Fk option:selected').val();
        if(!nameEntityRel) nameEntityRel = entityRel.toUpperCase();
        //alert(CollecteSelected);
        if (typeof entityRelSelected !== 'undefined' && entityRelSelected != null && entityRelSelected != '') {
            var $addBouton= $('<span>&nbsp;<a href="../../'+entityrel_lowercase+'/'+entityRelSelected+'/edit" class="btn btn-round btn-primary">Back to '+nameEntityRel+'</a><span> ');
            $('.title_left h1').append($addBouton); 
        }
}

// Test de cohérences sur les champ date et date_precision
function dateDateprecision(container, nameFieldDate,  message){
    var valueDatePrecision = $('input[id^="'+container+'_datePrecisionVocFk_"]:checked ').val(); 
    var datePrecision = $('label[for='+container+'_datePrecisionVocFk_'+valueDatePrecision+']').text().trim();
    var dateCollecteYear = $('#'+container+'_'+nameFieldDate+'_year').val();
    var dateCollecteMonth = $('#'+container+'_'+nameFieldDate+'_month').val();
    var dateCollecteDay = $('#'+container+'_'+nameFieldDate+'_day').val();
    //alert(datePrecision); 
    var flagDatePrecision = 1;
    switch (datePrecision) {
        case 'INCONNU':
            if (dateCollecteYear != '' || dateCollecteMonth != '' || dateCollecteDay!= ''){
                flagDatePrecision = 0;
                alert(message["INCONNU"]);
            } 
          break;
        case 'ANNEE':
            if (dateCollecteYear == '' || Number(dateCollecteMonth) != 1 || Number(dateCollecteDay) != 1){
                //alert(dateCollecteYear+' - '+Number(dateCollecteMonth)+' - '+Number(dateCollecteDay));
                flagDatePrecision = 0;
                alert(message["ANNEE"]);
            }         
          break;
        case 'MOIS':
            if (dateCollecteYear == '' || dateCollecteMonth == '' || Number(dateCollecteDay) != 1){
                flagDatePrecision = 0;
                alert(message["MOIS"]);
            }         
          break;
        case 'JOUR':
            if (dateCollecteYear == '' || dateCollecteMonth == '' || dateCollecteDay == ''){
                flagDatePrecision = 0;
                alert(message["JOUR"]);
            }         
          break;
        default:
            flagDatePrecision = 0;

      }  
      if (!flagDatePrecision) $('#'+container+'_datePrecisionVocFk_'+valueDatePrecision).prop( "checked", false );
    return flagDatePrecision;
}

// Test sur le format des dates
function dateFormat(container, nameFieldDate,  message){
    var valueDatePrecision = $('input[id^="'+container+'_datePrecisionVocFk_"]:checked ').val(); 
    var datePrecision = $('label[for='+container+'_datePrecisionVocFk_'+valueDatePrecision+']').text().trim();
    var dateCollecteYear = $('#'+container+'_'+nameFieldDate+'_year').val();
    var dateCollecteMonth = $('#'+container+'_'+nameFieldDate+'_month').val();
    var dateCollecteDay = $('#'+container+'_'+nameFieldDate+'_day').val();
    // si aucune date_precision est cochée on laisse la possibilité d'inscrire n'importe quelle date avec un contrôle sur le format
    // if(dateCollecteDay != '' && dateCollecteMonth != '' && dateCollecteYear != '') alert("dateCollecteYear:"+dateCollecteYear+"dateCollecteMonth:"+dateCollecteMonth+"dateCollecteDay:"+dateCollecteDay); 
    var flagDate = 1;
    if(dateCollecteDay != '' && dateCollecteMonth != '' && dateCollecteYear != '') {
        var regJ = new RegExp("^[0-3]?[0-9]{1}$");
        var regM = new RegExp("^[0-1]?[0-9]{1}$");
        var regA = new RegExp("^[1-2]{1}[0-9]{3}$");
        //alert("dateCollecteYear:"+regA.test(dateCollecteYear)+"dateCollecteMonth:"+regM.test(dateCollecteMonth) +"dateCollecteDay:"+regJ.test(dateCollecteDay)); 
        if(!regJ.test(dateCollecteDay) || !regM.test(dateCollecteMonth) || !regA.test(dateCollecteYear) ){
             flagDate = 0;
             alert(message["BAD-FORMAT-DATE"]);
        } else {
            if(parseInt(dateCollecteDay)>31  || parseInt(dateCollecteMonth)>12 ){
                flagDate = 0;
                alert(message["BAD-FORMAT-DATE"]);
            }
        }
    }
    $('#'+container+'_datePrecisionVocFk_'+valueDatePrecision).prop( "checked", false );
    return flagDate;
}

/**
 * Renvoie un tableau des valeurs d'une clé d'un objet JSON.
 * @param {Object} json objet JSON
 * @param {any} key clé à cibler
 */
function unpack(json, key) {
    return json.map(function(row) { return row[key] })
}

/**
 * Fonction d'affichage des stations situées dans une aire de 0.1x0.1 deg autour d'un point GPS
 * 
 * @param {Object} json_stations  
 * @param {number} latGPS
 * @param {number} longGPS
 */
function stationsPlot(json_stations, latGPS = undefined, longGPS = undefined) {
/**
 * Fonction pour extraire les données JSON et construire un objet de données 
 * pour plotly
 * 
 * @param {Object} json données json
 * @param {Object} update données à ajouter
 */

var longmin = Number(longGPS) - 0.1;
var longmax = Number(longGPS) + 0.1;
var latmin = Number(latGPS) - 0.1;
var latmax = Number(latGPS) + 0.1;
var latGPS = parseFloat(latGPS);
var longGPS = parseFloat(longGPS);
var latArray = [latGPS];
var longArray = [longGPS];

  function build_station_data(json, update = {}) {
    const  latitude = unpack(json, 'station.latDegDec'),
      longitude = unpack(json, 'station.longDegDec'),
      code_station = unpack(json, 'station.codeStation'),
      nom_station = unpack(json, 'station.nomStation'),
      code_commune = unpack(json,'commune.codeCommune')
    // Initialisation des hover text
    var hoverText = []
    for (var i = 0; i < latitude.length; i++) {
      var difLat = parseFloat(latitude[i]-latGPS).toFixed(6);
      var difLong = parseFloat(longitude[i]-longGPS).toFixed(6);
      var stationText = [
        "Code: " + code_station[i],
        "Nom: " + nom_station[i] ,
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
    // Ajout données supplémentaires à l'objet data
    $.extend(true, data, update)
    return data
  }

    // Init plotly
    var d3 = Plotly.d3
    $("#station-geo-map").html('')
    var gd3 = d3.select('#station-geo-map')
    var gd = gd3.node()

    // Données
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
      name: "GPS : lat = "+latGPS+"  /  long = "+longGPS,
    }
    
  // Objet data : contient les scatterplots
  var data = [
    data_stations,
    dataSelectedStation
  ]


  // Objet data complet : scatterplots 

  // Paramètres d'affichage du graphique
  const layout = {
    font: {
      family: 'Droid Serif, serif',
      size: 14
    },
    titlefont: {
      size: 16
    },
    height: 600,
    margin: {
      l: 0,
      r: 0,
      t: 15,
      b: 0
    },
    showlegend: true,
    geo: { // carte geographique
      scope: 'world',
      resolution: 50,
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
      projection: {
        type: 'miller'
      },
      showrivers: true,
      rivercolor: '#fff',
      showlakes: true,
      lakecolor: '#fff',
      showland: true,
      landcolor: '#2bc',
      countrycolor: 'grey',
      countrywidth: 1,
      subunitcolor: '#d3d3d3',
      showocean: true,
      oceancolor: 'lightblue',
      showframe: true,
      framecolor: '#000',
      framewidth: 2,
      bgcolor: 'lightgrey'
    }
  }

  Plotly.newPlot(gd, data, layout, {
    displaylogo: false, // pas de logo, enlever boutons de controle inutiles
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
  })

  Plotly.Plots.resize(gd) // Remplir l'espace dans le DOM

  return gd // Renvoi objet plotly
}

