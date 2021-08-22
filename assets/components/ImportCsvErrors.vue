<template>
  <b-card v-show="errors.length" id="errors" no-body border-variant="warning">
    <template #header>
      <div class="d-flex justify-content-between">
        <h6 class="mb-0">
          Invalid data in {{ errors.length }} items out of {{ records.length }}
        </h6>
        No data was saved in the database yet. Fix the following errors and
        submit again.
      </div>
    </template>
    <b-list-group flush>
      <b-list-group-item
        v-for="recordErrors in errors"
        :key="recordErrors.line"
        variant="warning"
        class="d-flex"
      >
        <h6>
          <b-badge variant="warning">
            Line #{{ recordErrors.line }}
          </b-badge>
        </h6>
        <ul class="m-0">
          <li v-for="(error, index) in recordErrors.payload" :key="index">
            <code>{{ error.property_path }}</code> : {{ error.message }}
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
      type: Array,
      required: true,
    },
    records: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style lang="less" scoped></style>
