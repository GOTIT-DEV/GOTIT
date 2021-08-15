<template>
  <div>
    <b-form-group :label="$t('level')">
      <form-multiselect
        v-model="level"
        name="level"
        :options="levelOptions"
        label="text"
        track-by="value"
        :allow-empty="false"
        :searchable="false"
      />
    </b-form-group>
    <b-form-group :label="$t('criteria')">
      <form-multiselect
        v-model="criteria"
        multiple
        name="criteria[]"
        :searchable="false"
        :options="criteriaOptions"
        label="label"
        track-by="id"
        :allow-empty="false"
        :close-on-select="false"
        :custom-label="(opt) => $t(`messages.${opt.label}`).toLowerCase()"
      />
    </b-form-group>
  </div>
</template>

<i18n>
{
  "en": {
    "level": "Table",
    "dna": "DNA Sequence",
    "specimen": "Specimen",
    "biomaterial": "Bio-Material",
    "criteria": "Criteria"
  },
  "fr": {
    "level": "Table",
    "dna": "Séquence",
    "specimen": "Individu",
    "biomaterial": "Lot Matériel",
    "criteria": "Critères"
  }
}
</i18n>

<script>
import FormMultiselect from "~Components/FormMultiselect";

export default {
  components: {
    FormMultiselect,
  },
  data() {
    return {
      url: Routing.generate("app_api_voc_listbyparent", {
        parent: "critereIdentification",
      }),
      level: undefined,
      levelOptions: [
        { value: 1, text: this.$t("biomaterial") },
        { value: 2, text: this.$t("specimen") },
        { value: 3, text: this.$t("dna") },
      ],
      criteria: undefined,
      criteriaOptions: [],
    };
  },
  watch: {
    criteriaOptions(newOptions, _) {
      this.criteria = newOptions;
    },
  },
  created() {
    this.level = this.levelOptions[2];
    this.isInitialized = false;
  },
  methods: {
    async init() {
      return this.isInitialized ? Promise.resolve(true) : this.fetch();
    },
    async fetch() {
      const response = await fetch(this.url);
      return response.json().then((criteriaJson) => {
        this.criteriaOptions = criteriaJson;
        this.isInitialized = true;
        return true;
      });
    },
  },
};
</script>

<style lang="less" scoped></style>
