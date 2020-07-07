/*
* This file is part of the SpeciesSearchBundle.
*
* Authors : see information concerning authors of GOTIT project in file AUTHORS.md
*
* SpeciesSearchBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* 
* SpeciesSearchBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License along with SpeciesSearchBundle.  If not, see <https://www.gnu.org/licenses/>
* 
* Author : Louis Duchemin <ls.duchemin@gmail.com>
*/

import L from 'leaflet'
/**
 * Control panel to be used in leaflet maps to change marker styles
 */

L.Control.StyleControl = L.Control.extend({
  onAdd: function (map) {
    let control = L.DomUtil.create("div", "leaflet-control-collapse leaflet-control-layers leaflet-control")
    let icon = L.DomUtil.create("i", "fas fa-cog fa-2x", control)
    let panel = L.DomUtil.create("div", "map-control-bar leaflet-control-layers-list", control)
    L.DomEvent.disableClickPropagation(panel);
    // Radius slider
    let radiusLabel = L.DomUtil.create("label", "leaflet-ctrl-label", panel)
    radiusLabel.innerHTML = Translator.trans("maps.controls.radius")
    let radiusCtnr = L.DomUtil.create("div", "leaflet-slider-container", panel)
    this.radiusSlider = L.DomUtil.create("input", "leaflet-slider", radiusCtnr)
    Object.assign(this.radiusSlider, {
      type: "range",
      min: 3,
      max: 12,
      value: 5
    })
    // Opacity slider
    let opacityLabel = L.DomUtil.create("label", "leaflet-ctrl-label", panel)
    opacityLabel.innerHTML = Translator.trans("maps.controls.opacity")
    let opacityCtnr = L.DomUtil.create("div", "leaflet-slider-container", panel)
    this.opacitySlider = L.DomUtil.create("input", "leaflet-slider", opacityCtnr)
    Object.assign(this.opacitySlider, {
      type: "range",
      min: 0.1,
      max: 1,
      step: 0.05,
      value: 0.75
    })
    $(control).mouseover(_ => {
      $(panel).css("display", "grid")
      $(icon).hide()
    })
    $(control).mouseout(event => {
      let e = event.toElement || event.relatedTarget;
      if (e == this.radiusSlider || e == this.opacitySlider) {
        return;
      }
      $(panel).css("display", "none")
      $(icon).show()
    })
    return control
  },

  onRemove: function (map) {
    // Nothing to do here
  }
});


export function styleControl(opts) {
  return new L.Control.StyleControl(opts);
}

