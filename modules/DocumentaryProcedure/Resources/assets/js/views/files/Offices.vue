<template>
  <div>

  </div>
</template>

<script>
export default {
  props: {
    offices: {
      type: Array,
      required: true,
    },
    actions: {
      type: Array,
      required: true,
    },
    form: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      officesSelected: [],
    };
  },
  beforeMount() {
    this.onAddOffice();
  },
  methods: {
    onAddOffice() {
      this.form.offices.push({
        documentary_action_id: null,
        documentary_office_id: null,
        observation: null,
      });
    },
    onDeleteOffice(index) {
        this.form.offices.splice(index, 1);
    }
  },
};
</script>
<style land="scss">
.btn-delete-office {
  position: absolute;
  right: 16px;
  top: 44px;
  padding: 4px 4px;
  z-index: 2;
}
</style>
