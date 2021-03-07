<template>
  <div>
    <b-form-group :label="$t('level')">
      <form-multiselect
        name="level"
        v-model="level"
        :options="levelOptions"
        label="text"
        track-by="value"
        :allowEmpty="false"
        :searchable="false"
      />
    </b-form-group>
    <b-form-group :label="$t('criteria')">
      <form-multiselect
        multiple
        name="criteria[]"
        :searchable="false"
        v-model="criteria"
        :options="criteriaOptions"
        label="libelle"
        track-by="id"
        :allowEmpty="false"
        :close-on-select="false"
        :custom-label="(opt) => $t(`messages.${opt.libelle}`).toLowerCase()"
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
import FormMultiselect from "../../../components/FormMultiselect";

export default {
  components: {
    FormMultiselect,
  },
  data() {
    return {
      ready: false,
      url: Routing.generate("list_voc", { parent: "critereIdentification" }),
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
  created() {
    this.level = this.levelOptions[2];
    this.ready = this.fetch();
  },
  methods: {
    async fetch() {
      const response = await fetch(this.url);
      return response.json().then((criteriaJson) => {
        this.criteriaOptions = criteriaJson;
      });
    },
  },
  watch: {
    criteriaOptions(newOptions, _) {
      this.criteria = newOptions;
    },
  },
};
</script>

<style lang="less" scoped>
</style>