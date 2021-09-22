<template>
  <b-card>
    <template #header>
      <h6 class="mb-0">
        Documentation
      </h6>
    </template>
    <p>
      <b>A CSV template is provided with header annotations.
        <br>
        They are required for proper parsing of your data, and must not be
        altered.
      </b>
    </p>
    <p>
      <b-button class="border">
        <i class="fas fa-file-archive" />
        Download all templates
      </b-button>
      <b-button v-b-modal.modal-csv-structure variant="info">
        Help
      </b-button>
    </p>
    <p>
      CSV files must be saved with <code>;</code> separator,
      <code>"</code> (double-quote) string delimiter and UTF-8 encoding.
    </p>
    <b-modal id="modal-csv-structure" title="CSV header structure" size="xl">
      <p>
        <b> CSV header structure</b>
      </p>
      <p>A CSV header always holds the name of the field it targets.</p>
      <p>
        It may also contain annotations if the target field is related to a
        different entity. Annotations define the related entity name and
        property, e.g <code>sampling.code</code>. In the case of singular
        relationship, annotations are delimited by parenthesis <code>()</code>.
        Multiple relationships are annotated within <code>[]</code> delimiters,
        and values provided must be separated with <code>&&</code>.
      </p>
      <p>
        Relations to the vocabulary repository have an extra annotation with
        prefix <code>#</code> to specify the vocabulary
        <code>parent</code> domain.
      </p>

      <b-table-simple small caption-top responsive>
        <caption>
          Examples
        </caption>
        <b-thead>
          <b-tr>
            <b-th>Header</b-th><b-th>Value</b-th><b-th>Description</b-th>
          </b-tr>
        </b-thead>
        <b-tbody>
          <b-tr>
            <b-td>
              <code>code</code>
            </b-td>
            <b-td>
              <code>Some_code</code>
            </b-td>
            <b-td>A simple value for a local property</b-td>
          </b-tr>
          <b-tr>
            <b-td>
              <code>sampling(sampling.code)</code>
            </b-td>
            <b-td>
              <code>The_sampling_code</code>
            </b-td>
            <b-td>
              Register relation on <code>sampling</code> to entity
              <code>sampling</code> having a <code>code</code> equal
              <code>The_sampling_code</code>.
            </b-td>
          </b-tr>
          <b-tr>
            <b-td>
              <code>participants(person.name)</code>
            </b-td>
            <b-td>
              <code>Person A && Person B</code>
            </b-td>
            <b-td>
              Register multiple relations on <code>participants</code> to
              persons having name <code>Person A </code> and
              <code>Person B</code>.
            </b-td>
          </b-tr>
        </b-tbody>
      </b-table-simple>
    </b-modal>
  </b-card>
</template>

<script>
export default {};
</script>

<style lang="less" scoped></style>
