<template>
  <div class="vqb-children">
    <b-list-group flush>
      <QueryBuilderRule
        v-for="(child, index) in ruleChildren"
        :key="index"
        :depths="depth + 1"
        :query.sync="child.query"
        :rule="$parent.ruleById(child.query.rule)"
        :index="child.id"
        :labels="labels"
        @child-deletion-requested="$parent.removeChild"
      />
    </b-list-group>

    <component
      :is="groupComponent"
      v-for="(child, index) in groupChildren"
      :key="index"
      :depth="depth + 1"
      :labels="labels"
      :index="child.id"
      :type="child.type"
      :query.sync="child.query"
      :rules="rules"
      :rule-types="ruleTypes"
      :max-depth="maxDepth"
      @child-deletion-requested="$parent.removeChild"
    />
  </div>
</template>

<script>
import QueryBuilderRule from "./QueryBuilderRule";
import QueryBuilderGroup from "./QueryBuilderGroup";

export default {
  name: "QueryBuilderChildren",

  components: { QueryBuilderGroup, QueryBuilderRule },

  // eslint-disable-next-line vue/require-prop-types
  props: ["query", "ruleTypes", "rules", "maxDepth", "labels", "depth"],

  computed: {
    childrenWithId() {
      return this.query.children.map((child, index) => {
        child.id = index;
        return child;
      });
    },
    ruleChildren() {
      return this.childrenWithId.filter(
        (child) => child.type != "query-builder-group"
      );
    },
    groupChildren() {
      return this.childrenWithId.filter(
        (child) => child.type == "query-builder-group"
      );
    },
  },

  data() {
    return {
      groupComponent: null,
      ruleComponent: null,
    };
  },

  mounted() {
    this.groupComponent = this.$parent.$options.components["QueryBuilderGroup"];
    this.ruleComponent = this.$parent.$options.components["QueryBuilderRule"];
  },

  methods: {
    getComponent(type) {
      return type === "query-builder-group"
        ? this.groupComponent
        : this.ruleComponent;
    },
  },
};
</script>