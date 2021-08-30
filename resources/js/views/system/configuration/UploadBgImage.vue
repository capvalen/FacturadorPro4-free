<template>
  <div>
    <img
      :src="imageUrl"
      alt="Vista previa"
      class="img-fluid img-thumbnail w-100 mb-3"
    />
    <input
      type="file"
      @change="onGeneratePreview"
      ref="inputFile"
      class="hidden"
    />
    <small class="form-control-feedback"
      >{{ helpText }}</small
    >
    <el-button
      type="primary"
      class="btn-block"
      @click="onShowFilePicker"
      :loading="loading"
      :disabled="loading"
      >{{ btnText }}</el-button
    >
  </div>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      required: true,
    },
    config: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      imageUrl: "",
      loading: false,
      btnText: '',
      helpText: ''
    };
  },
  mounted() {
      if (this.type === 'bg') {
        this.imageUrl = this.config.image;
        this.helpText = 'Se recomienda una imagen de 747 x 547px con fondo transparente en formato PNG o SVG';
      } else {
          this.imageUrl = this.config.logo || '';
          this.helpText = 'Se recomienda una imagen de 700 x 300 con fondo transparente en formato PNG';
      }
    if (this.type === 'bg') {
        this.btnText = 'Cambiar image de fondo';
    } else {
        this.btnText = 'Cambiar logo';
    }
  },
  methods: {
    onShowFilePicker() {
      this.$refs.inputFile.click();
    },
    onGeneratePreview(event) {
      const files = event.target.files;
      if (files.length > 0) {
        const fileReader = new FileReader();
        fileReader.addEventListener("load", () => {
          this.imageUrl = fileReader.result;
        });
        fileReader.readAsDataURL(files[0]);
        const image = files[0];
        const payload = new FormData();
        payload.append("image", image);
        payload.append("type", this.type);
        this.loading = true;
        this.$http
          .post("/configurations/bg", payload)
          .then((response) => {
            this.$message({
              message: response.data.message,
              type: "success",
            });
          })
          .finally(() => (this.loading = false));
      }
    },
  },
};
</script>
