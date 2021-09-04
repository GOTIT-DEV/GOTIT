/**
 * Attempt to get composite value with "[key].[export]"",
 * fallback on field key
 */
function getDeepValue(obj, key) {
  const compositeKey = key.split(".");
  return compositeKey.length > 1
    ? compositeKey.reduce(
        (val, key) => (val && val[key] ? val[key] : null),
        obj
      )
    : obj[key];
}

export default {
  props: {
    /**
     * @see https://bootstrap-vue.org/docs/components/table
     */
    fields: {
      type: Array,
      required: true,
    },
    /**
     * Allow exporting data as CSV.
     * When using an item provider, all data are fetched from source and exported.
     */
    allowExport: {
      type: Boolean,
      default: true,
    },
    /**
     * When exporting, use field `key` instead of `label` in the CSV header
     */
    exportColumnsByKey: {
      type: Boolean,
      default: false,
    },
    /**
     * The name of the file to export
     */
    exportFilename: {
      type: String,
      default: "data.csv",
    },
  },
  data() {
    return {
      downloading: false,
    };
  },
  computed: {
    /**
     * Build CSV header from exported field definitions
     * @returns {Array}
     */
    exportedHeader() {
      return this.exportedFields.reduce(
        (header, field) => [
          ...header,
          field.unpacker
            ? Object.keys(field.unpacker(field))
            : field.export?.label ||
              (this.exportColumnsByKey ? field.key : field.label) ||
              field.key,
        ],
        []
      );
    },
    /**
     * The field definitions to be exported
     * @returns {Array}
     */
    exportedFields() {
      return this.fields.filter((f) => !f.export?.exclude);
    },
  },
  methods: {
    /**
     * Serialize one item as a CSV record string
     */
    itemToCsv(item, sep = ",") {
      function itemArrayValues(item) {
        return function (acc, field) {
          let value = getDeepValue(item, field.key);
          if (field.unpacker) {
            acc = [
              ...acc,
              Object.values(field.unpacker(value)).map(JSON.stringify),
            ];
          } else {
            value = JSON.stringify(
              field.export?.formatter
                ? field.export.formatter(value)
                : field.formatter
                ? field.formatter(value)
                : value,
              (k, v) => (v === null ? "" : v)
            );
            acc = [...acc, value];
          }
          return acc;
        };
      }
      return this.exportedFields.reduce(itemArrayValues(item), []).join(sep);
    },
    /**
     * Serialize all data as a CSV string
     */
    itemsToCsv(items, sep = ",") {
      return [
        this.exportedHeader.join(sep),
        ...items.map((item) => this.itemToCsv(item, sep)),
      ].join("\r\n");
    },
    /**
     * Download items as CSV.
     * Items may be an array, or a provider function to fetch the array.
     * CSV header is defined depending on `field` property definition.
     * @param {Function|Array} items
     * @returns {boolean}
     */
    downloadItemsCSV(items) {
      this.downloading = true;
      const csv = this.itemsToCsv(items);
      const encodedUri = encodeURI("data:text/csv;charset=utf-8," + csv);
      let link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", this.exportFilename);
      link.click();
      this.downloading = false;
      return true;
    },
  },
};
