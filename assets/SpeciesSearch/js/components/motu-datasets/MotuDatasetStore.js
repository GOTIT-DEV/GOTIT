import { defer } from "../../utils"

export default {
  namespaced: true,
  state: () => ({
    multiple: false,
    loading: true,
    ready: defer(),
    dataset: undefined,
    datasetList: [],
    methods: undefined,
    methodList: [],
  }),
  actions: {
    async fetchDatasetList({ commit, state }) {

      const url = Routing.generate("datasets-list")
      let response = await fetch(url)
      let data = await response.json()
      commit("setDatasetList", data)
      commit("setDataset", data[0].id)

    },
    async fetchMethodList({ commit, state }, dataset_id) {
      const url = Routing.generate("methods-in-dataset", {id: dataset_id})
      let response = await fetch(url)
      let data = await response.json()
      commit("setMethodList", data)
      state.methods = state.multiple ?
        data.map(m => m.id) : 
        data[0].id

    },
    async init({dispatch, state}, multiple) {
      state.multiple = multiple
      if (state.multiple) state.methods = []
      await dispatch("fetchDatasetList")
      await dispatch("fetchMethodList", state.dataset)
      state.loading = false
      state.ready.resolve()
    }
  },
  mutations: {
    setDatasetList(state, value) {
      state.datasetList = value
    },
    setDataset(state, value) {
      state.dataset = value
    },
    setMethodList(state, value) {
      state.methodList = value
    },
    setMethods(state, value) {
      state.methods = value
    },
    setLoading(state, value) {
      state.loading = value
      // if (value === false) state.ready.resolve()
    },
    setMultiple(state, value) {
      console.log("Multiple = ", state.multiple)
      state.multiple = value
    }
  }
}