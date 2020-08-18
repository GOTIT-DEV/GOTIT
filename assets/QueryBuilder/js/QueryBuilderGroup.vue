<template>
  <!-- eslint-disable vue/no-v-html -->
  <div class="vqb-group card" :class="'depth-' + depth.toString()">
    <div class="vqb-group-heading card-header">
      <div class="match-type-container form-inline">
        <b-radio-group
          v-model="query.logicalOperator"
          :options="labels.matchTypes"
          value-field="id"
          text-field="label"
          buttons
          button-variant="outline-primary"
          size="sm"
        ></b-radio-group>

        <!-- <select
          id="vqb-match-type"
          v-model="query.logicalOperator"
          class="form-control"
        >
          <option
            v-for="label in labels.matchTypes"
            :key="label.id"
            :value="label.id"
          >
            {{ label.label }}
          </option>
        </select> -->

        <div class="rule-actions form-inline ml-4">
          <div class="form-group">
            <b-input-group size="sm">
              <b-select v-model="selectedRule">
                <b-select-option
                  v-for="rule in rules"
                  :key="rule.id"
                  :value="rule"
                >
                  {{ rule.label }}
                </b-select-option>
              </b-select>
              <b-input-group-append>
                <b-button class="mr-2" @click="addRule">
                  {{ labels.addRule }}
                </b-button>
              </b-input-group-append>
            </b-input-group>

            <button
              v-if="depth < maxDepth"
              type="button"
              class="btn btn-secondary btn-sm"
              @click="addGroup"
            >
              {{ labels.addGroup }}
            </button>
          </div>
        </div>

        <button
          v-if="depth > 1"
          type="button"
          class="close ml-auto"
          @click="remove"
          v-html="labels.removeGroup"
        ></button>
        <b-button
        v-else
        variant="light"
        class="ml-auto text-secondary"
        @click="$emit('reset')">
        <i class="fas fa-redo-alt"></i>
        Reset
        </b-button>
      </div>
    </div>

    <div v-if="query.children.length" class="vqb-group-body">
      <query-builder-children v-bind="$props" />
    </div>
  </div>
</template>

<script>
import QueryBuilderGroup from "vue-query-builder/src/components/QueryBuilderGroup";
import QueryBuilderRule from "./QueryBuilderRule";
import QueryBuilderChildren from "./QueryBuilderChildren";
export default {
  name: "QueryBuilderGroup",
  components: {
    // eslint-disable-next-line vue/no-unused-components
    QueryBuilderRule,
    QueryBuilderChildren
  },
  extends: QueryBuilderGroup,
};
</script>

<style lang="less">
.vue-query-builder .vqb-group {

  .vqb-group {
    margin: 15px;
  }

  .vqb-group-heading {
    padding-right: 1.25rem;

    .match-type-container .rule-actions {
      // margin-bottom: 20px;
      .form-group {
        margin-bottom: 0;
      }
    }
  }
}

// .vue-query-builder .vqb-group-body {
//   padding: 0.5rem 1rem;
// }

.vue-query-builder .vqb-rule {
  border-color: #ddd;
}
.vue-query-builder .vqb-group.depth-1
{
  border-left: 2px solid #8bc34a;
}
.vue-query-builder .vqb-group.depth-2
{
  border-left: 2px solid #00bcd4;
}
.vue-query-builder .vqb-group.depth-3
{
  border-left: 2px solid #ff5722;
}
.vue-query-builder .close {
  opacity: 1;
  color: rgb(255, 104, 104);
}
@media (min-width: 768px) {
  .vue-query-builder .vqb-rule.form-inline .form-group {
    display: block;
  }
}
</style>