<template>
  <div id="index-table-container">
    <b-data-table
      :id="tableId"
      ref="table"
      :items="itemProvider"
      :fields="extendedFields"
      :provider-pagination="providerPagination"
      :export-filename="exportFilename"
    >
      <template
        v-for="slotName in Object.keys($scopedSlots)"
        #[slotName]="slotScope"
      >
        <slot :name="slotName" v-bind="slotScope" />
      </template>
      <template #cell(actions)="data">
        <action-buttons
          :item="data.item"
          :show-route="routes.show"
          :edit-route="routes.edit"
          @delete="deleteItemConfirm"
        />
      </template>
    </b-data-table>
    <b-modal
      id="delete-confirm"
      size="sm"
      hide-header
      @hidden="pendingDeletion = {}"
      @ok="deleteItem(pendingDeletion)"
    >
      Delete item {{ shortItemRepr(pendingDeletion) }} ?
    </b-modal>
  </div>
</template>

<i18n>
{
  "en": {
    "meta.update": "Update",
    "meta.creation": "Creation"
  },
  "fr": {
    "meta.update": "Mise à jour",
    "meta.creation": "Création"
  }
}
</i18n>

<script>
import BDataTable from "~Components/BDataTable.vue";
import ActionButtons from "./ActionButtons.vue";
import moment from "moment";
export default {
  components: { BDataTable, ActionButtons },
  props: {
    tableId: {
      type: String,
      required: true,
    },
    providerPagination: {
      type: Object,
      default: null,
      validator(pagination) {
        return ["items", "pagination"].every((key) => key in pagination);
      },
    },
    exportFilename: {
      type: String,
      required: true,
    },
    routes: {
      type: Object,
      required: true,
      validator(routes) {
        return ["show", "edit", "list", "delete"].every((key) => key in routes);
      },
    },
    fields: {
      type: Array,
      required: true,
    },
    shortItemRepr: {
      type: Function,
      required: true,
    },
  },
  data() {
    return {
      pendingDeletion: {},
      actionColumn: {
        key: "actions",
        sortable: false,
        visible: true,
        export: {
          exclude: true,
        },
        class: "text-center p-0 align-middle",
      },
      metaFields: [
        {
          key: "_meta.update",
          label: this.$t("meta.update"),
          sortable: true,
          visible: false,
          sortKey: "date",
          class: "metadata",
          sortKey: "metaUpdateDate",
          unpacker(value) {
            return value
              ? {
                  update_date: value.date,
                  update_user: value.user?.name,
                }
              : value;
          },
          formatter(value) {
            if (value) {
              const dateRepr = moment(value.date).format("L LT");
              const userRepr = value.user ? `\n(${value.user.name})` : "";
              return `${dateRepr}${userRepr}`;
            } else {
              return value;
            }
          },
        },
        {
          key: "_meta.creation",
          label: this.$t("meta.creation"),
          sortable: true,
          visible: false,
          sortKey: "metaCreationDate",
          class: "metadata",
          unpacker(value) {
            return value
              ? {
                  creation_date: value.date,
                  creation_user: value.user?.name,
                }
              : value;
          },
          formatter(value) {
            if (value) {
              const dateRepr = moment(value.date).format("L LT");
              const userRepr = value.user ? ` (${value.user.name})` : "";
              return `${dateRepr}${userRepr}`;
            } else {
              return value;
            }
          },
        },
      ],
    };
  },
  computed: {
    extendedFields() {
      return [...this.fields, ...this.metaFields, this.actionColumn];
    },
  },
  methods: {
    async itemProvider(ctx) {
      const response = await fetch(Routing.generate(this.routes.list, ctx));
      return await response.json();
    },
    deleteItemConfirm(item) {
      this.pendingDeletion = item;
      this.$bvModal.show("delete-confirm");
    },
    async deleteItem(item) {
      await fetch(Routing.generate(this.routes.delete, { id: item.id }), {
        method: "DELETE",
      });
      this.$bvToast.toast(this.shortItemRepr(item), {
        title: "Item deleted",
        autoHideDelay: 5000,
        appendToast: true,
      });
      this.$root.$emit("bv::refresh::table", this.tableId);
    },
  },
};
</script>

<style lang="less">
table td.metadata {
  max-width: 10ch;
  word-wrap: normal;
  padding-top: 0;
  padding-bottom: 0;
  font-size: 12px;
  vertical-align: middle;
}
</style>
