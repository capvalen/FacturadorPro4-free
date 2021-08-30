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
    <small class="form-control-feedback">Se recomienda una imagen de 747 x 547px con fondo transparente en formato PNG o SVG</small>
    <el-button type="primary" class="btn-block" @click="onShowFilePicker" :loading="loading" :disabled="loading"
      >Cambiar image de fondo</el-button
    >
  </div>
</template>

<script>
export default {
  props: {
    config: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      imageUrl: "",
      loading: false,
    };
  },
  mounted() {
    this.imageUrl = this.config.image;
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
        this.loading = true;
        this.$http
          .post("/login-page/upload-bg-image", payload)
          .then((response) => {
            this.$message({
              message: response.data.message,
              type: "success",
            });
          }).finally(() => this.loading = false);
      }
    },
  },
};
</script>
