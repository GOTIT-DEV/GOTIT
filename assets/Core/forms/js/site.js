$(() => {
  // $("#station_showProximalStations").l

  $("#station_paysFk").change(event => {
    const country = event.target.value
    fetch(Routing.generate('country_municipalities', {id:country}))
      .then(response => response.json())
      .then(json => {
        const options = json.map(item => 
          `<option value="${item.id}">${item.codeCommune}</option>`
          )
        $("#station_communeFk").empty().append(options).selectpicker('refresh')
    })
  })

  $("#modal-station_newMunicipality").on('show.bs.modal', (event) => {
    const country = $("#station_paysFk").val()
    $(event.target).find("select#commune_paysFk").val(country).selectpicker('refresh')
  })
})