L.Control.StyleControl = L.Control.extend({
    onAdd: function (map) {
        let control = L.DomUtil.create("div", "leaflet-control-collapse leaflet-control-layers leaflet-control")
        let icon = L.DomUtil.create("i", "fa fa-cog fa-2x", control)
        let panel = L.DomUtil.create("div", "map-control-bar leaflet-control-layers-list", control)
        L.DomEvent.disableClickPropagation(panel);
        // Radius slider
        let radiusLabel = L.DomUtil.create("label", "leaflet-ctrl-label", panel)
        radiusLabel.innerHTML = "Radius"
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
        opacityLabel.innerHTML = "Opacity"
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

