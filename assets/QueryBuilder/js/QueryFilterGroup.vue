<template>
  <!-- eslint-disable vue/no-v-html -->
  <div
    class="vqb-group card"
    :class="['depth-' + depth.toString(), active || depth > 1 ? 'card' : '']"
  >
    <div class="vqb-group-heading card-header d-flex">
      <div v-if="depth == 1" class="filter-switch mr-3">
        <label class="mr-2 text-uppercase"> {{ $t("filters") }} </label>
        <toggle-button
          id="toggle-constraints"
          class="toggle-btn"
          :value="active"
          :sync="true"
          @change="$emit('update:active', $event.value)"
          :labels="true"
          :width="60"
          :height="25"
          :disabled="rules.length == 0"
        />
      </div>
      <transition name="fade">
        <div
          class="match-type-container form-inline w-100"
          v-if="active || depth > 1"
        >
          <b-radio-group
            v-model="query.logicalOperator"
            :options="labels.matchTypes"
            value-field="id"
            text-field="label"
            buttons
            button-variant="outline-primary"
            size="sm"
          ></b-radio-group>

          <div class="rule-actions form-group ml-4">
            <b-input-group size="sm" class="flex-nowrap">
              <multiselect
                :options="rules"
                label="label"
                :showLabels="false"
                v-model="selectedRule"
                :allowEmpty="false"
              />
              <b-input-group-append>
                <b-button class="mr-2" @click="addRule">
                  {{ $t("add_rule") }}
                </b-button>
              </b-input-group-append>
            </b-input-group>

            <button
              v-if="depth < maxDepth"
              type="button"
              class="btn btn-secondary btn-sm"
              @click="addGroup"
            >
              {{ $t("add_group") }}
            </button>
          </div>

          <button
            v-if="depth > 1"
            type="button"
            class="close ml-auto"
            @click="remove"
            v-html="'&times;'"
          />

          <b-button
            v-else
            variant="light"
            size="sm"
            class="ml-auto text-secondary"
            @click="$emit('reset')"
          >
            <i class="fas fa-redo-alt"></i>
            <span class="d-none d-lg-inline">
              {{ $t("reset") | capitalize }}
            </span>
          </b-button>
        </div>
      </transition>
    </div>
    <transition name="fade">
      <div
        v-if="query.children.length > 0 && (active || depth > 1)"
        class="vqb-group-body"
      >
        <query-filter-children v-bind="$props" />
      </div>
    </transition>
  </div>
</template>

<i18n>
{
  "en": {
    "add_rule": "Add rule",
    "add_group": "Add group",
    "filters" : "filters"
    },
  "fr": {
    "add_rule": "Ajouter",
    "add_group": "Ajouter groupe",
    "filters" : "filtres"
  }
}
</i18n>

<script>
import QueryBuilderGroup from "vue-query-builder/src/components/QueryBuilderGroup";
import QueryFilterRule from "./QueryFilterRule";
import QueryFilterChildren from "./QueryFilterChildren";
import Multiselect from "vue-multiselect";
import { ToggleButton } from "vue-js-toggle-button";

export default {
  name: "QueryFilterGroup",
  components: {
    // eslint-disable-next-line vue/no-unused-components
    ToggleButton,
    Multiselect,
    QueryFilterRule,
    QueryFilterChildren,
  },
  extends: QueryBuilderGroup,
  props: {
    active: Boolean,
  },
  watch: {
    rules(newRules) {
      this.selectedRule = newRules[0];
    },
  },
};
</script>

<style lang="less">
.vue-query-builder .vqb-group {
  .vqb-custom-component-wrap {
    width: 100%;
  }

  .vqb-group {
    margin: 15px;
  }

  .vqb-group-heading {
    padding-right: 1.25rem;

    .filter-switch {
      min-height: 32px;
      justify-self: start;
      align-self: center;
      display: flex;
      align-items: center;

      label {
        margin: 0;
      }
    }
    .match-type-container {
      .rule-actions.form-group {
        flex-wrap: nowrap;

        margin-bottom: 0;

        .input-group {
          .multiselect {
            width: 200px;
            min-height: 30px;
            .multiselect__select {
              padding: 8px;
              height: 28px;
              &::before {
                top: 100%;
              }
            }
            .multiselect__tags {
              padding-top: 3px;
              height: calc(1.5em + 0.5rem + 2px);
              min-height: 30px;
              border-top-right-radius: 0;
              border-bottom-right-radius: 0;
            }
            span.multiselect__option {
              padding: 6px 12px;
              min-height: 30px;
            }
          }
        }
      }
    }
  }
}

.vue-query-builder .vqb-rule {
  border-color: #ddd;
}
.vue-query-builder .vqb-group.depth-1 {
  border-left: 2px solid #8bc34a;
}
.vue-query-builder .vqb-group.depth-2 {
  border-left: 2px solid #00bcd4;
}
.vue-query-builder .vqb-group.depth-3 {
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

// Vue transitions https://vuejs.org/v2/guide/transitions.html
.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>