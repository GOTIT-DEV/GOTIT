<template>
  <b-list-group-item class="vqb-rule">
    <div class="form-inline">
      <label class="mr-5 rule-label col-2">{{ rule.label }}</label>

      <!-- List of operators (e.g. =, !=, >, <) -->
      <b-select
        v-if="
          typeof rule.operators !== 'undefined' && rule.operators.length > 1
        "
        v-model="query.operator"
        class="mr-2 col-3"
        size="sm"
        :options="rule.operators"
      />

      <b-input-group
        v-if="
          ![
            'is null',
            'is not null',
            'is empty',
            'is not empty',
            'in',
            'not in',
          ].includes(query.operator)
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
          required
        >

        <!-- Basic number input -->
        <input
          v-else-if="rule.inputType === 'number'"
          v-model="value[0]"
          class="form-control form-control-sm"
          type="number"
          v-bind="rule.attrs"
          required
        >

        <!-- Datepicker -->
        <input
          v-else-if="rule.inputType === 'date'"
          v-model="value[0]"
          v-mask="{ mask: '9999-99-99', placeholder: 'YYYY-MM-DD' }"
          class="form-control form-control-sm"
          type="text"
          placeholder="YYYY-MM-DD"
          required
        >

        <!-- Custom component input -->
        <div v-else-if="isCustomComponent" class="vqb-custom-component-wrap">
          <component
            :is="rule.component"
            v-bind="rule.props"
            v-model="value[0]"
            width="100%"
            required
          />
        </div>

        <!-- Checkbox input -->
        <template v-else-if="rule.inputType === 'checkbox'">
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
            >
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
        <template v-else-if="rule.inputType === 'radio'">
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
            >
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
          v-else-if="rule.inputType === 'select' && !hasOptionGroups"
          v-model="value[0]"
          class="form-control form-control-sm"
          :multiple="rule.type === 'multi-select'"
          required
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
          v-else-if="rule.inputType === 'select' && hasOptionGroups"
          v-model="value[0]"
          class="form-control form-control-sm"
          :multiple="rule.type === 'multi-select'"
          required
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
        <b-input
          v-if="
            query.operator.includes('between') && rule.inputType === 'number'
          "
          v-model="value[1]"
          :min="value[0]"
          size="sm"
          type="number"
          required
        />
        <b-input
          v-if="query.operator.includes('between') && rule.inputType === 'date'"
          v-model="value[1]"
          v-mask="{ mask: '9999-99-99', placeholder: 'YYYY-MM-DD' }"
          :min="value[0]"
          size="sm"
          type="text"
          placeholder="YYYY-MM-DD"
          required
        />
      </b-input-group>

      <!-- Multiple select with tags -->
      <div v-else-if="query.operator.match(/^(not )?in$/)" class="col-6">
        <multiselect
          v-model="value"
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
          v-bind="rule.props || {}"
          :close-on-select="false"
          @tag="addTag"
        />
      </div>

      <!-- Remove rule button -->
      <button
        type="button"
        class="close ml-auto"
        @click="remove"
        v-html="'&times'"
      />
    </div>
  </b-list-group-item>
</template>

<script>
import QueryBuilderRule from "vue-query-builder/src/components/QueryBuilderRule";
import Multiselect from "vue-multiselect";
import Mask from "../../directives/InputMask";

export default {
  directives: { Mask },
  components: { Multiselect },
  extends: QueryBuilderRule,
  data() {
    return {
      value: [],
    };
  },
  computed: {
    isMultiple() {
      return this.query.operator.match(/^(not )?(in|between)$/) !== null;
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
  methods: {
    addTag(tag) {
      this.rule.choices.push(tag);
      this.value.push(tag);
    },
  },
};
</script>

<style lang="less">
@import "~vue-multiselect/dist/vue-multiselect.min.css";

.vqb-rule {
  padding: 0.25rem 1.25rem;
}

.rule-label {
  min-width: 50px;
}
</style>
