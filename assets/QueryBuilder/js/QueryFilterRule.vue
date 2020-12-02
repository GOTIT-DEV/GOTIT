<template>
  <!-- eslint-disable vue/no-v-html -->
  <b-list-group-item class="vqb-rule">
    <div class="form-inline">
      <label class="mr-5 rule-label col-2">{{ rule.label }}</label>

      <!-- List of operands (optional) -->
      <select
        v-if="typeof rule.operands !== 'undefined'"
        v-model="query.operand"
        class="form-control mr-2 col-1"
      >
        <option v-for="operand in rule.operands" :key="operand">
          {{ operand }}
        </option>
      </select>

      <!-- List of operators (e.g. =, !=, >, <) -->
      <select
        v-if="
          typeof rule.operators !== 'undefined' && rule.operators.length > 1
        "
        v-model="query.operator"
        class="form-control mr-2 form-control-sm col-3"
      >
        <option
          v-for="operator in rule.operators"
          :key="operator"
          :value="operator"
        >
          {{ operator }}
        </option>
      </select>

      <b-input-group
        v-if="
          !['is null', 'is not null', 'in', 'not in'].includes(query.operator)
        "
        size="sm"
        class="mr-1 col-5"
      >
        <!-- Basic text input -->
        <input
          v-if="rule.inputType === 'text'"
          v-model="value[0]"
          class="form-control form-control-sm"
          type="text"
          :placeholder="labels.textInputPlaceholder"
        />

        <!-- Basic number input -->
        <input
          v-if="rule.inputType === 'number'"
          v-model="value[0]"
          class="form-control form-control-sm"
          type="number"
          v-bind="rule.attrs"
        />

        <!-- Datepicker -->
        <input
          v-if="rule.inputType === 'date'"
          v-model="value[0]"
          class="form-control form-control-sm"
          type="text"
          placeholder="YYYY-MM-DD"
          v-mask="{ mask: '9999-99-99', placeholder: 'YYYY-MM-DD' }"
          required
        />

        <!-- Custom component input -->
        <div v-if="isCustomComponent" class="vqb-custom-component-wrap">
          <component
            :is="rule.component"
            v-bind="rule.props"
            v-model="value[0]"
            width="100%"
          />
        </div>

        <!-- Checkbox input -->
        <template v-if="rule.inputType === 'checkbox'">
          <div
            v-for="choice in rule.choices"
            :key="choice.value"
            class="form-check form-check-inline"
          >
            <input
              :id="
                'depth' +
                depth +
                '-' +
                rule.id +
                '-' +
                index +
                '-' +
                choice.value
              "
              v-model="value[0]"
              type="checkbox"
              :value="choice.value"
              class="form-check-input"
            />
            <label
              class="form-check-label"
              :for="
                'depth' +
                depth +
                '-' +
                rule.id +
                '-' +
                index +
                '-' +
                choice.value
              "
            >
              {{ choice.label }}
            </label>
          </div>
        </template>

        <!-- Radio input -->
        <template v-if="rule.inputType === 'radio'">
          <div
            v-for="choice in rule.choices"
            :key="choice.value"
            class="form-check form-check-inline"
          >
            <input
              :id="
                'depth' +
                depth +
                '-' +
                rule.id +
                '-' +
                index +
                '-' +
                choice.value
              "
              v-model="value[0]"
              :name="'depth' + depth + '-' + rule.id + '-' + index"
              type="radio"
              :value="choice.value"
              class="form-check-input"
            />
            <label
              class="form-check-label"
              :for="
                'depth' +
                depth +
                '-' +
                rule.id +
                '-' +
                index +
                '-' +
                choice.value
              "
            >
              {{ choice.label }}
            </label>
          </div>
        </template>

        <!-- Select without groups -->
        <select
          v-if="rule.inputType === 'select' && !hasOptionGroups"
          v-model="value[0]"
          class="form-control form-control-sm"
          :multiple="rule.type === 'multi-select'"
        >
          <option
            v-for="option in selectOptions"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>

        <!-- Select with groups -->
        <select
          v-if="rule.inputType === 'select' && hasOptionGroups"
          v-model="value[0]"
          class="form-control form-control-sm"
          :multiple="rule.type === 'multi-select'"
        >
          <optgroup
            v-for="(option, option_key) in selectOptions"
            :key="option_key"
            :label="option_key"
          >
            <option
              v-for="sub_option in option"
              :key="sub_option.value"
              :value="sub_option.value"
            >
              {{ sub_option.label }}
            </option>
          </optgroup>
        </select>

        <!-- Paired input -->
        <b-input-group-addon v-if="query.operator.includes('between')" is-text>
          &mdash;
        </b-input-group-addon>
        <input
          v-if="
            query.operator.includes('between') && rule.inputType === 'number'
          "
          v-model="value[1]"
          :min="value[0]"
          class="form-control form-control-sm"
          type="number"
        />
        <input
          v-if="query.operator.includes('between') && rule.inputType === 'date'"
          v-model="value[1]"
          :min="value[0]"
          class="form-control form-control-sm"
          type="text"
          placeholder="YYYY-MM-DD"
          v-mask="{ mask: '9999-99-99', placeholder: 'YYYY-MM-DD' }"
          required
        />
      </b-input-group>

      <!-- Multiple select with tags -->
      <div v-else-if="query.operator.endsWith('in')" class="col-6">
        <multiselect
          :options="
            rule.choices && rule.choices.length
              ? rule.choices
              : rule.props
              ? rule.props.options
              : []
          "
          required
          multiple
          searchable
          taggable
          @tag="addTag"
          v-bind="rule.props || {}"
          v-model="value"
          :closeOnSelect="false"
        />
      </div>

      <!-- Remove rule button -->
      <button
        type="button"
        class="close ml-auto"
        @click="remove"
        v-html="labels.removeRule"
      ></button>
    </div>
  </b-list-group-item>
</template>

<script>
import QueryBuilderRule from "vue-query-builder/src/components/QueryBuilderRule";
import Multiselect from "vue-multiselect";
import Mask from "../../directives/InputMask";

export default {
  extends: QueryBuilderRule,
  directives: { Mask },
  components: { Multiselect },
  computed: {
    isMultiple() {
      return (
        this.query.operator.endsWith("between") ||
        this.query.operator.endsWith("in")
      );
    },
  },
  data() {
    return {
      value: [],
    };
  },
  methods: {
    addTag(tag) {
      this.rule.choices.push(tag);
      this.value.push(tag);
    },
  },
  watch: {
    query: {
      deep: true,
      handler(val, oldVal) {
        this.query.value = this.isMultiple ? this.value : this.value[0];
      },
    },

    value(val) {
      this.query.value = this.isMultiple ? this.value : this.value[0];
    },
  },
};
</script>

<style lang="less" scoped>
.rule-label {
  min-width: 50px;
}
</style>