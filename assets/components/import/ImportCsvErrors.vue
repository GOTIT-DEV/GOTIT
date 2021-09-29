<template>
  <b-card v-show="errorsCount" id="errors" no-body border-variant="warning">
    <template #header>
      <div class="d-flex justify-content-between">
        <h6 class="mb-0">
          {{ `Invalid data in ${errorsCount} items` }}
        </h6>
        <span>
          No data was saved in the database yet. Fix the following errors and
          submit again.
        </span>
      </div>
    </template>
    <b-list-group flush>
      <b-list-group-item
        v-for="(recordErrors, line) in mappedErrors"
        :key="line"
        class="d-flex"
      >
        <h6>
          <b-badge variant="warning">
            Line #{{ line }}
          </b-badge>
        </h6>
        <ul class="m-0">
          <li v-for="(error, index) in recordErrors" :key="index">
            <code>{{ error.property }}</code> : {{ error.message }}
          </li>
        </ul>
      </b-list-group-item>
    </b-list-group>
  </b-card>
</template>

<script>
export default {
  props: {
    errors: {
      type: Object,
      required: true,
    },
  },
  computed: {
    errorsCount() {
      return Object.keys(this.mappedErrors || {}).length;
    },
    mappedErrors() {
      return this.errors.violations?.reduce(
        (acc, { propertyPath, message, code }) => {
          let [line, property] = propertyPath.split(".");
          line = line.slice(1, -1);
          const error = { line, property, message, code };
          acc[line] ? acc[line].push(error) : (acc[line] = [error]);
          return acc;
        },
        {}
      );
    },
  },
  methods: {},
};
</script>

<style lang="less" scoped></style>
