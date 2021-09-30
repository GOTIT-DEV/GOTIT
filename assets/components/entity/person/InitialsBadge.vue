<template>
  <a
    v-b-tooltip
    :href="generateRoute('person_show', { id: person.id })"
    class="
      rounded-circle
      bg-info
      text-light text-monospace
      border border-secondary
    "
    :title="person.name"
  >
    <span>
      {{ initials }}
    </span>
  </a>
</template>

<script>
export default {
  props: {
    person: {
      type: Object,
      required: true,
    },
  },
  computed: {
    initials() {
      return this.person.name
        .split(" ")
        .map((part) => part.charAt(0))
        .join("");
    },
  },
};
</script>

<style lang="less" scoped>
a {
  width: 1.6rem;
  height: 1.6rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
