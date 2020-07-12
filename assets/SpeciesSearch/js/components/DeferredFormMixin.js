
import { mapState, mapMutations } from "vuex";

export default {
  computed: {
    ...mapState(["loading"])
  },
  methods: {
    ...mapMutations(["setLoading"]),

    submit() {
      this.setLoading(true);
      this.$emit("submit");
    },

    received() {
      this.setLoading(false);
    }
  }
}