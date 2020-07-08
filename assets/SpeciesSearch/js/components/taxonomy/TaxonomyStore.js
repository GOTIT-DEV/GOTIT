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
  namespaced:true,
  state: () => ({
    loading: true,
    genus: undefined,
    species: undefined,
    taxname: undefined,
    ready: defer()
  }),
  mutations: {
    setGenus(state, value) {
      state.genus = value
    },
    setSpecies(state, value) {
      state.species = value
    },
    setTaxname(state, value) {
      state.taxname = value
    },
    setLoading(state, value) {
      state.loading = value
      if(value===false) state.ready.resolve()
    }
  }
}