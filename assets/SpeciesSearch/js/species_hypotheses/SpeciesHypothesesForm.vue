<template>
  <b-form
    id="main-form"
    ref="form"
    class="d-flex align-items-center mb-5 row"
    action="#"
    @submit.prevent="submit"
  >
    <fieldset class="col-lg-5">
      <legend>
        <h3>
          {{ $t("queries.label.reference") }}
        </h3>
      </legend>
      <b-card no-body class="mb-1">
        <b-card-header role="tab">
          <b-form-radio
            v-model="reference"
            name="reference"
            value="morpho"
            size=""
          >
            {{ $t("queries.label.morpho") }}
          </b-form-radio>
        </b-card-header>
      </b-card>

      <b-card no-body class="mb-1">
        <b-card-header role="tab">
          <b-form-radio
            v-model="reference"
            name="reference"
            value="taxonomy"
            size=""
          >
            {{ $t("queries.label.morphotaxon") }}
          </b-form-radio>
        </b-card-header>
        <b-collapse
          id="accordion-taxonomy"
          accordion="form-accordion"
          role="tabpanel"
          :visible="reference == 'taxonomy'"
        >
          <b-card-body>
            <TaxonomySelect
              ref="taxonomy"
              with-taxname
              :disabled="reference != 'taxonomy'"
            />
          </b-card-body>
        </b-collapse>
      </b-card>

      <b-card no-body class="mb-1">
        <b-card-header role="tab">
          <b-form-radio
            v-model="reference"
            name="reference"
            value="motu"
            size=""
          >
            {{ $t("queries.methode.label") }}
          </b-form-radio>
        </b-card-header>
        <b-collapse
          id="accordion-motu"
          accordion="form-accordion"
          role="tabpanel"
          :visible="reference == 'motu'"
        >
          <b-card-body>
            <MotuDatasetSelect
              ref="motu"
              :disabled="reference != 'motu'"
              @update:motuList="motuList = $event"
              @update:dataset="refDatasetSelected($event)"
            />
          </b-card-body>
        </b-collapse>
      </b-card>
    </fieldset>

    <div class="col-lg-2 text-center">
      <b-button
        id="direction"
        ref="direction"
        variant="light"
        :class="{
          reversed: reversed,
          'm-3 border rounded-circle fas fa-long-arrow-alt-right fa-4x': true,
        }"
        type="button"
        data-toggle="collapse"
        data-target=".result-collapse"
        :disabled="transitioning"
        @click="reverse($event)"
      />
    </div>

    <div class="col-lg-5">
      <h3>{{ $t("queries.label.target") }}</h3>
      <b-card class="mb-3">
        <b-form-group
          :label="$t('queries.label.dataset')"
          label-for="target-dataset"
        >
          <form-multiselect
            ref="target"
            v-model="motu"
            :options="motuList"
            label="name"
            :disabled="reference == 'motu'"
            track-by="id"
            name="target-dataset"
            :allow-empty="false"
            :searchable="false"
          />
        </b-form-group>
      </b-card>
      <center
        class="
          col-12
          offset-sm-3
          col-sm-6
          offset-lg-0
          col-lg-12
          offset-xl-3
          col-xl-6
        "
      >
        <ButtonLoading
          id="submit"
          ref="submit"
          size="lg"
          block
          :loading="loading"
          @click="submit"
        >
          {{ $t("ui.search") }}
        </ButtonLoading>
      </center>
    </div>
  </b-form>
</template>

<script>
import ButtonLoading from "~Components/ButtonLoading";
import FormMultiselect from "~Components/FormMultiselect";
import TaxonomySelect from "../components/taxonomy/TaxonomySelect";
import MotuDatasetSelect from "../components/motu-datasets/MotuDatasetSelect";
export default {
  components: {
    TaxonomySelect,
    MotuDatasetSelect,
    ButtonLoading,
    FormMultiselect,
  },
  data() {
    return {
      loading: true,
      url: Routing.generate("species-hypotheses-query"),
      reference: "morpho",
      motuList: [],
      motu: undefined,
      transitioning: false,
      reversed: false,
    };
  },
  computed: {},
  watch: {
    motuList(newList, oldList) {
      this.motu = newList[0];
    },
    reference: function (newRef) {
      if (newRef === "motu") this.motu = this.$refs.motu.dataset;
    },
  },
  mounted() {
    Promise.all([this.$refs.motu.init(), this.$refs.taxonomy.init()]).then(
      this.submit
    );
  },
  methods: {
    async submit() {
      this.loading = true;
      const response = await fetch(this.url, {
        method: "POST",
        body: new FormData(this.$refs.form),
      });
      const results = await response.json();
      this.loading = false;
      this.$emit("update:results", results);
      return results;
    },
    refDatasetSelected(event) {
      if (this.reference === "motu") this.motu = event;
    },
    reverse(event) {
      this.reversed = !this.reversed;
      this.$emit("update:reversed", this.reversed);
      setTimeout(() => (this.transitioning = true), 5);
      setTimeout(() => (this.transitioning = false), 350);
    },
  },
};
</script>

<style lang="less" scoped>
@media (min-width: 992px) {
  #direction {
    transform: rotateZ(0deg);
    transition: transform 0.35s ease;
    &.reversed {
      transform: rotateZ(180deg);
      transition: transform 0.35s ease;
    }
  }
}

@media (max-width: 992px) {
  #direction {
    transform: rotateZ(90deg);
    transition: transform 0.35s ease;
    &.reversed {
      transform: rotateZ(-90deg);
      transition: transform 0.35s ease;
    }
  }
}
</style>
