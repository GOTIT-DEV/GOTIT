<template>
  <layout :title="$t('messages.Dna')" :help-text="$t('help.dna_index')">
    <template #header-extra>
      <dna-header :urls="urls" />
    </template>
    <template #content>
      <b-container id="entity-import" class="mb-5">
        <b-row>
          <b-card-group deck>
            <b-card no-body>
              <b-tabs card>
                <b-tab :title="$t('messages.Dna')" active>
                  <import-csv-form
                    :template-url="templateUrl"
                    :api-route="apiRoute"
                    :types="types"
                    class="mb-3"
                    @success="onResponse"
                    @errors="onErrors"
                    @failure="onFailure"
                    @submit="onSubmit"
                  />
                </b-tab>
                <b-tab title="Store">
                  <b-form>
                    <b-form-group>
                      <b-file accept=".csv" required />
                    </b-form-group>
                  </b-form>
                </b-tab>
                <b-tab title="Move">
                  <b-card-text>Tab contents 2</b-card-text>
                </b-tab>
              </b-tabs>
            </b-card>
            <import-csv-doc />
          </b-card-group>
        </b-row>
      </b-container>
      <import-csv-errors :errors="errors" />
      <div v-show="items.length">
        <b-alert show variant="success">
          <i class="fas fa-check-circle" />
          {{ items.length }} items successfully imported
        </b-alert>
        <dna-table :items="items" @delete:item="deleteItem" />
      </div>
    </template>
  </layout>
</template>

<script>
import Layout from "~Components/Layout.vue";
import DnaHeader from "./DnaHeader.vue";
import ImportCsvForm from "~Components/import/ImportCsvForm";
import ImportCsvDoc from "~Components/import/ImportCsvDoc.vue";
import ImportCsvErrors from "~Components/import/ImportCsvErrors.vue";
import DnaTable from "./index/DnaTable.vue";
export default {
  components: {
    Layout,
    ImportCsvForm,
    DnaHeader,
    ImportCsvDoc,
    ImportCsvErrors,
    DnaTable,
  },
  props: {
    templateUrl: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      items: [],
      errors: {},
      urls: {
        create: this.generateRoute("dna_new"),
        list: this.generateRoute("dna_index"),
        import: this.generateRoute("dna_import"),
      },
      types: [{ name: "DNA", value: null }],
    };
  },
  computed: {
    apiRoute() {
      return this.generateRoute("api_dnas_import_collection");
    },
  },
  methods: {
    onSubmit() {
      this.errors = [];
      this.items = [];
    },
    onResponse(json) {
      this.items = json;
    },
    onErrors(errors) {
      this.errors = errors;
    },
    onFailure({ code, message }) {
      this.$bvModal.msgBoxOk(
        `An unexpected error occured : ${message} (${code})`
      );
    },
    deleteItem(item) {
      this.items = this.items.filter((i) => i.id !== item.id);
    },
  },
};
</script>

<style lang="less" scoped></style>
