export const layout = {
  height: 300,
  // X axis
  xaxis: {
    title: Translator.trans("methode.label", {}, "queries"),
    titlefont: {
      family: "sans serif",
      size: 18,
      color: "#7f7f7f",
    },
    fixedrange: true,
  },
  // Y axis
  yaxis: {
    title: Translator.trans(
      "queries.specieshypotheses.short",
      {},
      "queries"
    ),
    titlefont: {
      family: "sans serif",
      size: 18,
      color: "#7f7f7f",
    },
    fixedrange: true,
  },
  margin: {
    t: 0
  },
  // Text layout
  font: {
    family: "sans serif",
    size: 14,
  },
  showlegend: true,
  // legend: {
  //   x: 0.5, y: 1,xanchor:'center', yanchor:'bottom'
  // },
  // Grouped bar chart
  barmode: "group",
}