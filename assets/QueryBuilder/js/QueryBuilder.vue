
<script>
/* eslint-disable vue/require-default-prop */
import VueQueryBuilder from "vue-query-builder";
import MultiSelect from "vue-multiselect"

var defaultLabels = {
  matchType: "Operator",
  matchTypes: [
    { id: "and", label: "AND" },
    { id: "or", label: "OR" },
  ],
  addRule: "Add Rule",
  removeRule: "&times;",
  addGroup: "Add Group",
  removeGroup: "&times;",
  textInputPlaceholder: "",
};

export default {
  name: "QueryBuilder",
  extends: VueQueryBuilder,
  props: {
    labels: {
      type: Object,
      default() {
        return defaultLabels;
      },
    },
  },
  data() {
    return {
      query: {
        logicalOperator: this.labels.matchTypes[0].id,
        children: []
      },
      ruleTypes: {
        text: {
          operators: [
            "equals",
            "does not equal",
            "contains",
            "does not contain",
            "is empty",
            "is not empty",
            "begins with",
            "ends with",
            "is null",
            "is not null",
          ],
          inputType: "text",
          id: "text-field",
        },
        date: {
          operators: [
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">=",
            "is null",
            "is not null",
            "between",
            "not between",
          ],
          inputType: "date",
          id: "date-field",
        },
        numeric: {
          operators: [
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">=",
            "between",
            "not between",
          ],
          inputType: "number",
          id: "number-field",
        },
        custom: {
          operators: [],
          inputType: "text",
          id: "custom-field",
        },
        radio: {
          operators: [],
          choices: [],
          inputType: "radio",
          id: "radio-field",
        },
        checkbox: {
          operators: [],
          choices: [],
          inputType: "checkbox",
          id: "checkbox-field",
        },
        select: {
          operators: [],
          choices: [],
          inputType: "select",
          id: "select-field",
        },
        "custom-component": {
          operators: ["=", "in"],
          choices: [],
          component:MultiSelect,
          // inputType: "select",
          id: "multi-select-field",
        },
      },
    };
  },
};
</script>
