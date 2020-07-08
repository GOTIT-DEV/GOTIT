// Used to initialize state.ready as promise.
// https://lea.verou.me/2016/12/resolve-promises-externally-with-this-one-weird-trick/
function defer() {
  var res, rej;

  var promise = new Promise((resolve, reject) => {
    res = resolve;
    rej = reject;
  });

  promise.resolve = res;
  promise.reject = rej;

  return promise;
}

export default {
  namespaced: true,
  state: () => ({
    loading: true,
    genus: undefined,
    genus_data: [],
    species: undefined,
    species_data: [],
    taxname: undefined,
    taxname_data: [],
    ready: defer()
  }),
  actions: {
    async fetchGenusSet({ commit, state }) {

      const url = Routing.generate("genus-list")
      let response = await fetch(url)
      let data = await response.json()
      commit("setGenusData", data)
      commit("setGenus", data[0].genus)

    },
    async fetchSpeciesSet({ commit, state }) {
      const url = Routing.generate("species-in-genus")
      let response = await fetch(url, {
        method: "POST",
        body: JSON.stringify({ genus: state.genus }),
        credentials: "include",
        headers: { "Content-Type": "application/json" }
      })
      let data = await response.json()
      commit("setSpeciesData", data)
      state.species = data[0].species

    },
    async fetchTaxnameSet({ commit, state }) {
      const url = Routing.generate("taxname-search")
      let response = await fetch(url,{
        method: "POST",
        body: JSON.stringify({
          genus: state.genus,
          species: state.species
        }),
        credentials: "include",
        headers: { "Content-Type": "application/json" }
      })
      let data = await response.json()
      commit("setTaxnameData", data)
      state.taxname = data[0].taxname

    },
    async init({dispatch, state}) {
      await dispatch("fetchGenusSet")
      await dispatch("fetchSpeciesSet")
      await dispatch("fetchTaxnameSet")
      state.loading = false
      state.ready.resolve()
    }
  },
  mutations: {
    setGenus(state, value) {
      state.genus = value
    },
    setGenusData(state, value) {
      state.genus_data = value
    },
    setSpecies(state, value) {
      state.species = value
    },
    setSpeciesData(state, value) {
      state.species_data = value
    },
    setTaxname(state, value) {
      state.taxname = value
    },
    setTaxnameData(state, value) {
      state.taxname_data = value
    },
    setLoading(state, value) {
      state.loading = value
      if (value === false) state.ready.resolve()
    }
  }
}